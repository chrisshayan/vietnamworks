<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 * Request:
 *      POST https://{api_server_domain}/jobs/applyAttach
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters:
 *          job_id                  int         required
 *          file_contents           string      optional
 *          application_subject     string      required    Subject or title of application( Ex: Application for PHP Developer via VietnamWorks)
 *          cover_letter            string      optional
 *          email                   string      required
 *          password                string      required
 *          first_name              string      required
 *          last_name               string      required
 *          lang                    integer     optional    1: Vietnamese ; 2: English; 3: Japanese
 *
 * Response on error:
 *      HTTP/1.1 400 Bad Request
 *      Content-Type: application/json
 *      body: {"message": "explain about happened error"}
 *
 * Response on success:
 *      HTTP/1.1 200 OK
 *      Content-Type: application/json
 *      body: { "Applied"}
 *          an activation email is sent to email address of job-seeker
 *          a notify email is sent to email address of job-seeker
 *          a notify email with attachment for resume is sent to employer who is owner of job
 */

$apiKey     = 'your_api_key';
$loginToken = 'your login token';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = '/jobs/applyAttach';
$file_name_with_full_path = "your_resume_path";
$target_url = $apiHost.$apiPath;
$post = array(
    'file_contents' => '@' . $file_name_with_full_path,
    'job_id' => '418562',
    'application_subject' => 'Application for Abc via VietnamWorks',
    'cover_letter' => 'test',
    'email' => 'ttson1001@yopmail.com',
    'password' => '123456',
    'first_name' => 'Thai Son',
    'last_name' => 'Tran',
    'lang' => '1'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'CONTENT-MD5: '.$apiKey,
    'Content-Type: multipart/form-data'
));
$result = curl_exec($ch);
$info = curl_getinfo($ch);
var_dump($result);
var_dump($info);
curl_close($ch);