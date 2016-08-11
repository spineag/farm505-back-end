<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');
$app = Application::getInstance();
$mainDb = $app->getMainDb();
$memcache = $app->getMemcache();

$id = '1441';
$s = '11111';
$result = $memcache->set($id, $s, false, 300);
echo $result;

//$json_data['message'] = $result->fetch();
//$json_data['message'] = 'ok';
//echo json_encode($json_data);
