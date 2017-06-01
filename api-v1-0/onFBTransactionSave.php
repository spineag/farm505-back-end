<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

    $app = Application::getInstance();
    $userSocialId = filter_var($_POST['userSocialId']);
    $packId = filter_var($_POST['packId']);
    $requestId = filter_var($_POST['requestId']);
    $channelId = 4;

    $mainDb = $app->getMainDb($channelId);
    $result = $mainDb->query("SELECT is_tester FROM users WHERE social_id =".$userSocialId);
    $isUserTester = 0;
    $r = $result->fetch();
    if ($r && (int)$r['is_tester'] != 0) $isUserTester = 1;

    $time = date("Y-m-d H:i:s");
    $mainDb->query('INSERT INTO transactions SET uid='. $userSocialId .', product_code='.$packId.', time_try="'.$time.'",request_id="'.$requestId.'", status="start", test='.$isUserTester);
    echo '';