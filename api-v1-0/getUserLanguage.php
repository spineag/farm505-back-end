<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) { 
    $app = Application::getInstance();
    $channelId = (int)$_POST['channelId'];

//    if ($channelId == 4) {
//        $lang = 2;
//    } else {
//        $lang = 1;
//    }
    $lang = 0;
    if ($channelId == 2) {
        $mainDb = $app->getMainDb($channelId);
        $result = $shardDb->query("SELECT language_id FROM users WHERE social_id =" . $_POST['userSocialId']);
        $r = $result->fetch();
        if ($r) {
            $lang = (int)$r['language_id'];
        }
    } else { // == 3 || == 4
        $userId = $app->getUserId($channelId, $_POST['userSocialId']);
        $shardDb = $app->getShardDb($userId, $channelId);
        $result = $shardDb->query("SELECT language_id FROM user_info WHERE user_id =" . $userId);
        $r = $result->fetch();
        if ($r) {
            $lang = (int)$r['language_id'];
            if ($channelId == 4 && $lang == 0) {
                $lang = 2;
            }
        }
    }

    echo $lang;
} else {
    echo 2;
}
