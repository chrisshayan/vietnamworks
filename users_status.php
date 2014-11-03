<?php
/**
 * Staging API server:      https://api.staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 * Request:
 *      GET https://{api_server_domain}/users/status/?email=email_to_check@test.com
 *      headers:
 *          CONTENT-MD5:your_api_key
 *
 * Response on error:
 *      HTTP/1.1 400 Bad Request
 *      Content-Type: application/json
 *      body: {"message": "explain about happened error"}
 *
 * Response on success:
 *      HTTP/1.1 200 OK
 *      Content-Type: application/json
 *      body: {"accountStatus": "status_code"}  with status_code in NEW, ACTIVATED, NON_ACTIVATED, BANNED
 */

$consumerId = 'your_consumer_id';
$apiKey = 'your_api_key';
$emailToCheck = 'test@email.com';

$apiUrl = 'https://api.staging.vietnamworks.com/users/status/?email='.urlencode($emailToCheck);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ignore SSL verification

curl_setopt($ch, CURLOPT_HTTPGET, true); // http request method is GET
curl_setopt($ch, CURLOPT_HTTPHEADER, array("CONTENT-MD5: $apiKey"));

$response = curl_exec($ch);
$info = curl_getinfo($ch);

// error case return code 400
if ($info['http_code'] == 400) {
    $response = json_decode($response, true);
    echo 'Server returned an error with message: '.$response['message'];
} elseif ($info['http_code'] == 200)  {
    $response = json_decode($response, true);
    echo 'Status of email '.$emailToCheck.' is: '.$response['accountStatus'];
} else {
    //unknown error
    echo "Server returned unknown error: \n".print_r($info, true)."\nResponse: $response";
}

curl_close($ch);