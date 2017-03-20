<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

$app = Application::getInstance();
$channelId = (int)$_POST['channelId'];
$mainDb = $app->getMainDb($channelId);

$result = $mainDb->query("SELECT version FROM version WHERE id=1");

if ($result) {
    $v = $result->fetch();
    echo $v['version'];
} else {
    echo 1;
}




