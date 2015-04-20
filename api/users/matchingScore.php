<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 *      This function requires login first
 * Request:
 *      GET https://{api_server_domain}/jobs/matchingScore/?token={token}&jobId[]={jobId}&jobId[]={jobId}
 *          {token}                     Login token of user account
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters:
 *          jobId                  array      required  list of job id which will be calculate matching score
 *
 * Response on error:
 *      HTTP/1.1 400 Bad Request
 *      Content-Type: application/json
 *      body: {"message": "explain about happened error"}
 *
 * Response on success:
 *      HTTP/1.1 200 OK
 *      Content-Type: application/json
 *      body: Returns list matching score for each job id, missing matching score parameter of job-seeker and new token for next api call
 */

$loginToken = 'your login token';
$apiKey     = 'your_api_key';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = sprintf('/jobs/matchingScore/?token=%s&jobId[]={jobId}&jobId[]={jobId}', $loginToken);

$ch = curl_init();

curl_setopt_array($ch, array(
    CURLOPT_URL             => $apiHost.$apiPath,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_SSL_VERIFYPEER  => false, // ignore SSL verification
    CURLOPT_POST            => false,  // http request method is GET
    CURLOPT_HTTPHEADER      => array(
        "CONTENT-MD5: $apiKey",
        'Content-Type: application/json'
    ),
));

$response = curl_exec($ch);
$responseArray = (array)json_decode($response, true);;

if ($responseArray['meta']['code'] == 400) { // error happened
    echo 'Server returned an error with message: '.$responseArray['meta']['message'];
} elseif ($responseArray['meta']['code'] == 200)  {
    echo "Response status: ".$responseArray['meta']['message']."".print_r($responseArray['data'], true);
} else {
    //unknown error
    $info = curl_getinfo($ch);
    echo "An error happened: \n".curl_error($ch)."\nInformation: ".print_r($info, true)."\nResponse: $response";
}

curl_close($ch);