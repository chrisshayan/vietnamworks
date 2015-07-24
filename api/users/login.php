<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 * Request:
 *      POST https://{api_server_domain}/users/login
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters:
 *          user_email                    string      required
 *          user_password                    string      required
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
 *          "profile": {
 *              "login_token": "Login token of user account"
 *              "user_id": "id of user"
 *              "first_name": "First name"
 *              "last_name": "Last name"
 *              "birthday": "Birthday format (dd-mm-yyyy)"
 *              "email": "email address of user"
 *              "nationality": "nationality of user"
 *              "residence": "residence of user"
 *          }
 *          "coverletter": "cover letter of user that will be use for apply job function"
 *          "resumes": {
 *              "resume_id": "id of resume"
 *              "resume_title": "Title of resume"
 *              "last_update": "latest date update"
 *          }
 *      }
 *          an activation email is sent to email address of user
 */

$apiKey     = 'your_api_key';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = '/users/login';

$jsonString = json_encode(array(
    'user_email' => 'test_api@yopmail.com',
    'user_password' => 'This is PaSSword'
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
    if ($responseArray['meta']['message'] == 'OK') {
		echo 'Login successfully <br />';
		// login successfully. Handle your own code here
		var_dump($responseArray['data']);
	} else {
		echo 'Login failed <br />';
		// fail to login. Handle your own code here
		var_dump($responseArray['meta']);
	}
} else {
    //unknown error
    $info = curl_getinfo($ch);
    echo "An error happened: \n".curl_error($ch)."\nInformation: ".print_r($info, true)."\nResponse: $response";
}

curl_close($ch);