<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
header("Content-Type: application/json; encoding=utf-8");

require $_SERVER['DOCUMENT_ROOT'] . '/php/fb-php-graph-sdk-5.5/src/Facebook/autoload.php';

session_start();
$verify_token = "kapusta";
$app_id = "1936104599955682";
$app_secret = "dd3c1b11a323f01a3ac23a3482724c49";
$app_token = "1936104599955682|BJ5JAYUV8FSdztyc3MW2lHVbXoU";
$server_url = "https://505.ninja/php/api-v1-0/payment/fb_5/";

$pack_id_for_product = [
    $server_url.'pack1.html' => 1,
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

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['hub_verify_token'] === $verify_token) {
    echo $_GET['hub_challenge'];
} else {
    $data = file_get_contents("php://input");
    $json = json_decode($data, true);


    if( $json["object"] && $json["object"] == "payments" ) {
        $payment_id = $json["entry"][0]["id"];
        try {
            $mainDb = Application::getInstance()->getMainDb(4);
            $time = date("Y-m-d H:i:s");
            $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$payment_id.'", message = "before check 2", time_try="'.$time.'", is_error=0');

            $fb = new Facebook\Facebook([
                'app_id'                => $app_id,
                'app_secret'            => $app_secret,
                'default_graph_version' => 'v2.10',
            ]);
            $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$payment_id.'", message = "before check 3", time_try="'.$time.'", is_error=0');
//            $response = $fb->get('/'.$payment_id.'?fields=actions', $app_token);
            $response = $fb->get('/'.$payment_id, $app_token);

            $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$payment_id.'", message = '.$response.', time_try="'.$time.'", is_error=2');
            $result = $response->getGraphObject();
            $actions = $result->getPropertyAsArray('actions');
            $actions = $result->getPropertyAsArray('actions');
            $payment_id = $result->getField('request_id');
            $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$payment_id.'", message = '.$actions[0]->getProperty('status').', time_try="'.$time.'", is_error=0');
            if( $actions[0]->getProperty('status') == 'completed' ){
                $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$payment_id.'", message = "completed2", time_try="'.$time.'", is_error=0');
            } else {
                $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$payment_id.'", message = '.$result.', time_try="'.$time.'", is_error=0');
            }
        } catch (FacebookRequestException $e) {
            $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$payment_id.'", message ='.$e->getRawResponse().', time_try="'.$time.'", is_error=1');
        } catch (\Exception $e) {
            $mainDb->query('INSERT INTO trabsaction_webhooks SET request_id="'.$payment_id.'", message ='.$e->getRawResponse().', time_try="'.$time.'", is_error=1');
        }
    }
}
