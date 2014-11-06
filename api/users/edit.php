<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 *      This function requires login first
 * Request:
 *      POST https://{api_server_domain}/users/edit/token/{token}
 *          {token}                     Login token of user account
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters:
 *          first_name                  string      optional
 *          last_name                   string      optional
 *          birthday                    string      optional    format (dd/mm/yyy)
 *          gender                      integer     optional    1: Male ; 2: Female
 *          nationality                 integer     optional    country_id
 *          residence                   string      optional    address
 *          home_phone                  string      optional
 *          cell_phone                  string      optional
 *          language                    integer     optional    1: Vietnamese ; 2: English; 3: Japanese
 *              values reference at /general/configuration API with path data->languages->language_id
 *
 * Response on error:
 *      HTTP/1.1 400 Bad Request
 *      Content-Type: application/json
 *      body: {"message": "explain about happened error"}
 *
 * Response on success:
 *      HTTP/1.1 200 OK
 *      Content-Type: application/json
 *      body: Returns updated profile information and new token for next api call
 */

$loginToken = 'your login token';
$apiKey     = 'your_api_key';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = sprintf('/users/edit/token/%s', $loginToken);

//In this example, only first_name and last_name are updated.
$jsonString = json_encode(array(
    'first_name'        => 'Lan',
    'last_name'         => 'Bui'
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
    echo "Response status: ".$responseArray['meta']['message']."\nUpdated profile information: ".print_r($responseArray['data'], true);
} else {
    //unknown error
    $info = curl_getinfo($ch);
    echo "An error happened: \n".curl_error($ch)."\nInformation: ".print_r($info, true)."\nResponse: $response";
}

curl_close($ch);