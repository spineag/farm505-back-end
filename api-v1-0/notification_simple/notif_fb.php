<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/php/fb-php-graph-sdk-5.5/src/Facebook/autoload.php';
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

$app = Application::getInstance();
$app_id = "1936104599955682";
$app_secret = "dd3c1b11a323f01a3ac23a3482724c49";
$app_token = "1936104599955682|BJ5JAYUV8FSdztyc3MW2lHVbXoU";

$mainDb = $app->getMainDb(4);

$fb = new Facebook\Facebook([
    'app_id' => $app_id ,
    'app_secret' => $app_secret,
    'default_graph_version' => 'v2.9',
]);

$txt = 'New event just started. Complete orders and get double XP!';

$result = $mainDb->query("SELECT COUNT(social_id) as c FROM users");
$ar = $result->fetch();
$countAll = (int)$ar['c'];

$idStart = 2;
$idFinish = 102;
while ($countAll > 0) {
    usleep(250000);
    $result = $mainDb->query("SELECT social_id FROM users WHERE id > ".$idStart." AND id <= $idFinish");
    $ar = $result->fetchAll();
    if ($ar) {
        foreach ($ar as $key => $value) {
            try {
                if ($value['social_id'] && $value['social_id'] != 'null') {
                    $sendNotif = $fb->post('/' . $value['social_id'] . '/notifications', array('href' => '?notif', 'template' => $txt), $app_token);
                }
            } catch (Exception $e) {

            }
        }
    }
    $countAll = $countAll - 100;
    $idStart = $idStart + 100;
    $idFinish = $idFinish + 100;
}


