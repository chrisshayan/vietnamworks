<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 * Request:
 *      POST https://{api_server_domain}/jobs/search
 *          {token}                     Login token of user account. It will be renew and return back to use for next request
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters:
 *          page_number                 integer     optional
 *          job_title                   string      optional
 *          job_location                string      optional(Maximum is 3) list of city ids which can be found in https://api-staging.vietnamworks.com/general/configuration/
 *          job_category                string      optional(Maximum is 3) list of industry ids which can be found in https://api-staging.vietnamworks.com/general/configuration/
 *          job_level                   integer     optional
 *          job_salary                  integer     optional
 *          job_benefit                 string      optional(Maximum is 3) list of industry ids which can be found in https://api-staging.vietnamworks.com/general/configuration/
 *          page_size                   integer     optional
 *
 * Response on error:
 *      HTTP/1.1 400 Bad Request
 *      Content-Type: application/json
 *      body: {"message": "explain about happened error"}
 *
 * Response on success:
 *      HTTP/1.1 200 OK
 *      Content-Type: application/json
 *      body: {
 *          "data": {
 *              "total": "Total job that is found"
 *              "jobs": [
 *                  {
 *                     "job_id": "id of job"
 *                     "job_title": "job title"
 *                     "job_company": "Company name"
 *                     "posted_date": "posted date format (dd-mm-yyyy)"
 *                     "job_location": "list of city ids"
 *                     "job_industry": "list of industry ids"
 *                     "job_detail_url": "job description url"
 *                     "job_logo_url": "logo of company who is posted this job"
 *                      "job_video_url": "introduce video of company who is posted this job"
 *                      "salary_min": "salary minimum"
 *                      "salary_max": "salary maximum"
 *                      "skills": "list of skill"
 *                      "benefits": "list of benefit"
 *                      "views": "number of viewed"
 *                      "applied": "number of applied"
 *                 },...
 *              ]
 *          "login_token": "New login token of user account"
 *          }
 *      }
 *          an activation email is sent to email address of user
 */

$apiKey     = 'your_api_key';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = '/jobs/search';

$jsonString = json_encode(array(
    'page_number' => 1,
    'job_title' => 'keyword',
    'job_location' => "24,29",
    'job_category' => "1,35",
    'job_level' => 3,
    'job_salary' => 500,
    'job_benefit' => "1,2,3",
    'page_size' => 20
));

$ch = curl_init();

curl_setopt_array($ch, array(
    CURLOPT_URL             => $apiHost.$apiPath,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_SSL_VERIFYPEER  => false, // ignore SSL verification
    CURLOPT_POST            => true,  // http request method is POST
    CURLOPT_HTTPHEADER      => array(
        "CONTENT-MD5: $apiKey",
        'Content-Type: application/json',
        'Content-Length: '.strlen($jsonString)
    ),
    CURLOPT_POSTFIELDS      => $jsonString
));

$response = curl_exec($ch);
$responseArray = (array)json_decode($response, true);;

if ($responseArray['meta']['code'] == 400) { // error happened
    echo 'Server returned an error with message: '.$responseArray['meta']['message'];
} elseif ($responseArray['meta']['code'] == 200)  {
    echo "Response status: ".$responseArray['meta']['message']."\nJob Information: ".$responseArray['data'];
} else {
    //unknown error
    $info = curl_getinfo($ch);
    echo "An error happened: \n".curl_error($ch)."\nInformation: ".print_r($info, true)."\nResponse: $response";
}

curl_close($ch);