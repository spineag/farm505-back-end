<?php
define('SN', 'fb');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
header("Content-Type: application/json; encoding=utf-8");

// Skip these two lines if you're using Composer
define('FACEBOOK_SDK_V4_SRC_DIR', 'facebook-php-sdk/src/Facebook/');
require __DIR__ . '/facebook-php-sdk/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

$verify_token = "kapusta";
$app_secret = "dd3c1b11a323f01a3ac23a3482724c49";
//$app_token = "567d08996291f371fc7def6a88a79314"; // ?? "YOUR_APP_ACCESS_TOKEN"
$app_token = "1936104599955682|BJ5JAYUV8FSdztyc3MW2lHVbXoU";
$app_id = "1936104599955682";
$server_url = "https://505.ninja/php/api-v1-0/payment/fb/";

$pack_id_for_product = [
    $server_url.'pack1a.html' => 1,
    $server_url.'pack2.html' => 2,
    $server_url.'pack3.html' => 3,
    $server_url.'pack4.html' => 4,
    $server_url.'pack5.html' => 5,
    $server_url.'pack6.html' => 6,
    $server_url.'pack7.html' => 7,
    $server_url.'pack8.html' => 8,
    $server_url.'pack9.html' => 9,
    $server_url.'pack10.html' => 10,
    $server_url.'pack11.html' => 11,
    $server_url.'pack12.html' => 12,
    $server_url.'pack13.html' => 13,
    $server_url.'pack14.html' => 14
];

FacebookSession::setDefaultApplication(
    $app_id,
    $app_secret);


$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET' && $_GET['hub_verify_token'] === $verify_token) {
    echo $_GET['hub_challenge'];
} else {
    $data = file_get_contents("php://input");
    $json = json_decode($data, true);
    // json["entry"][0] = { id, time, () };

    if( $json["object"] && $json["object"] == "payments" ) {
        $payment_id = $json["entry"][0]["id"];
        $mainDb = Application::getInstance()->getMainDb(4);
        $time = date("Y-m-d H:i:s");
        try {
            $session = new FacebookSession($app_token);
            $request = new FacebookRequest(
                $session,
                'GET',
                '/'.$payment_id,
                array(
                    'fields' => 'request_id,actions,user'
                )
            );
            $response = $request->execute();
            $result = $response->getGraphObject(GraphObject::className());
            $actions = $result->getPropertyAsArray('actions');
            if( $actions[0]->getProperty('status') == 'completed' ) {
                $request_id = $result->getProperty('request_id');
                $user = $result->getProperty('user')->getProperty('id');
                $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$request_id.'", message = "completed", time_try="'.$time.'", 
                user_social_id = "'.$user.'", payment_id="'.$payment_id.'"');
            }
        } catch (FacebookRequestException $e) {
            $mainDb->query('INSERT INTO trabsaction_webhooks SET payment_id="'.$payment_id.'", message ="'.$e->getRawResponse().'", time_try="'.$time.'"');
        } catch (Exception $e) {
            $mainDb->query('INSERT INTO trabsaction_webhooks SET payment_id="'.$payment_id.'", message ="'.$e->getRawResponse().'", time_try="'.$time.'"');
        }
    }
}
