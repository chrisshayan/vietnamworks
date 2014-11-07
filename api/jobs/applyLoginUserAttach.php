<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 * Request:
 *      POST https://{api_server_domain}/jobs/applyAttach/token/{token}
  *          {token}                     Login token of user account. It will be null if user is anonymous
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters:
 *          job_id                  int         required
 *          file_contents           string      optional    required if resume_attach_id is null, path of resume that is uploaded
 *          resume_attach_id        int         optional    required if file_contents is null, user can re-use attachment cv from last apply job
 *          application_subject     string      required    Subject or title of application( Ex: Application for PHP Developer via VietnamWorks)
 *          cover_letter            string      optional
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
 *          a notify email is sent to email address of job-seeker
 *          a notify email with attachment for resume is sent to employer who is owner of job
 */

$apiKey     = 'your_api_key';
$loginToken = 'your login token';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = '/jobs/applyAttach';
$file_name_with_full_path = "your_resume_path";
$target_url = $apiHost.$apiPath.'/token/'.$loginToken;
$post = array(
    'file_contents' => '@' . $file_name_with_full_path,
    'job_id' => '418562',
    'resume_attach_id' => '',
    'application_subject' => 'Application for Abc via VietnamWorks',
    'cover_letter' => 'test',
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