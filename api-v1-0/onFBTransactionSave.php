<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

    $app = Application::getInstance();
    $userSocialId = filter_var($_POST['userSocialId']);
    $packId = filter_var($_POST['packId']);
    $requestId = filter_var($_POST['requestId']);
    $channelId = 4;

    $mainDb = $app->getMainDb($channelId);

    $time = date("Y-m-d H:i:s");
    $mainDb->query('INSERT INTO transactions SET uid='. $userSocialId .', product_code='.$packId.', time_try="'.$time.'",request_id="'.$requestId.'", status="start"');
    echo '';