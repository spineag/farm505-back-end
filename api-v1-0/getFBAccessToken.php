<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
$app = Application::getInstance();

$userSocialId = filter_var($_POST['userSocialId']);
$channelId = 4;
$mainDb = $app->getMainDb($channelId);
try {
    $result = $mainDb->query('SELECT token FROM access_token WHERE social_id="'.$userSocialId.'"');
    $q = $result->fetch();

    if ($q) {
        echo $q['token'];
    } else {
        echo '0';
    }
} catch (Exception $e){
    echo '0';
}