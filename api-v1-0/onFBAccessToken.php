<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
$app = Application::getInstance();

$userSocialId = $_POST['userSocialId'];
$aToken = $_POST['accessToken'];
$channelId = 4;
$mainDb = $app->getMainDb($channelId);
try {
    $result = $mainDb->query('SELECT * FROM access_token WHERE social_id="'.$userSocialId.'"');
    $q = $result->fetch();
    $st = '_';
    if ($q) {
        $st = 'UPDATE access_token SET token="'.$aToken.'" WHERE social_id="'.$userSocialId.'"';
        $result = $mainDb->query($st);
    } else {
        $st = 'INSERT INTO access_token SET social_id="'.$userSocialId.'", token="'.$aToken.'"';
        $result = $mainDb->query($st);
    }

    if ($result) {
        echo 'OK';
    } else {
        echo 'error';
    }
} catch (Exception $e){
    echo $e;
}