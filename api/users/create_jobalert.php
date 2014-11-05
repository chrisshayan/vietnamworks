<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 * Request:
 *      POST https://{api_server_domain}/users/create-jobalert/
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters: (make sure at least one of following parameters submitted: keywords, job_categories, job_locations, job_level, min_salary)
 *          email                       string      required
 *          keywords                    string      optional
 *          job_categories              array       optional
 *              values reference at /general/configuration API with path data->categories->category_id
 *          job_locations               array       optional
 *              values reference at /general/configuration API with path data->locations->location_id
 *          job_level                   integer     optional
 *              values reference at /general/configuration API with path data->job_levels->job_level_id
 *          min_salary                  float       optional
 *          frequency                   integer     optional
 *              default = 3: send weekly
 *              values reference at /general/configuration API with path data->frequencies->frequency_id
 *          lang                        integer     optional
 *              default = 1: Vietnamese
 *              values reference at /general/configuration API with path data->languages->language_id
 *
 * Response on error:
 *      HTTP/1.1 400 Bad Request
 *      Content-Type: application/json
 *      body:
 *          {
 *              "meta": {
 *                  "code": 400,
 *                  "message": "explain about happened error"
 *              }
 *          }
 *
 * Response on success:
 *      HTTP/1.1 200 OK
 *      Content-Type: application/json
 *      body:
 *          {
 *              "meta": {
 *                  "code": 200
 *              },
 *              "data": {
 *                  "createdStatus": "SENT_EMAIL"
 *              }
 *          }
 *      an email is sent to submitted email address
 */

$apiKey     = 'your_api_key';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = '/users/create-jobalert/';

$jsonString = json_encode(array(
    'email'             => 'test@email.com',
    'keywords'          => 'php',
    'job_categories'    => array(35, 55),   // IT - Software and IT - Hardware/Networking
    'job_locations'     => array(24, 29),   // Ha Noi and Ho Chi Minh
    'job_level'         => 5,               // Experienced (Non-Manager)
    'min_salary'        => 1000,            // unit is usd
    'frequency'         => 3,               // send weekly
    'lang'              => 1                // Vietnamese
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
$info = curl_getinfo($ch);

if ($info['http_code'] == 400) { // error happened
    $response = json_decode($response, true);
    echo 'Server returned an error with message: '.$response['meta']['message'];
} elseif ($info['http_code'] == 200 && !empty($response))  {
    $response = json_decode($response, true);
    echo 'Response status: '.$response['data']['createdStatus'];
} else {
    //unknown error
    echo "An error happened: \n".curl_error($ch)."\nInformation: ".print_r($info, true)."\nResponse: $response";
}

curl_close($ch);