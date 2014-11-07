<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 * Request:
 *      POST https://{api_server_domain}/users/register
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters:
 *          email                       string      required
 *          password                    string      required
 *          first_name                  string      required
 *          last_name                   string      required
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
 *      body: { "userID": "new id of user"}
 *          an activation email is sent to email address of user
 */

$apiKey     = 'your_api_key';
$loginToken = 'your login token';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = '/jobs/applyAttach';
$uploaddir  = 'path_to_upload_file';

if($_POST['submit']){
    $uploadfile = $uploaddir . basename($_FILES['cvAttach']['name']);
    move_uploaded_file($_FILES['cvAttach']['tmp_name'], $uploadfile);
    $target_url = $apiHost.$apiPath.'/token/'.$loginToken;
    $file_name_with_full_path = realpath($uploaddir . $_FILES['cvAttach']['name']);
    $post = array(
        'extra_info' => '123456',
        'file_contents' => '@' . $file_name_with_full_path,
        'job_id' => '418562',
        'resume_attach_id' => '',
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
}
?>
<form method="post" enctype="multipart/form-data" action="http://sontt.vnw25.com/jobseekers/apply.php">
    File Upload <input type="file" name="cvAttach"/>
    <input type="submit" value="Submit" name="submit"/>
</form>