<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK

    try {
        if ($channelId == 2) {
            $mainDb = $app->getMainDb($channelId);
            $result = $mainDb->query('SELECT errors FROM users WHERE id='.$_POST['userId']);
            $ar = $result->fetch();
            $errorsCount = (int)$ar['errors'];
            $errorsCount = $errorsCount + 1;
            $result = $mainDb->query('UPDATE users SET errors=".$errorsCount." WHERE id=' . $_POST['userId']);
            if (!$result) {
                $json_data['id'] = 2;
                $json_data['status'] = 's333';
                throw new Exception("Bad request to DB!");
            }
        } else { // == 3 || == 4
            $shardDb = $app->getShardDb($_POST['userId'], $channelId);
            $result = $shardDb->query('SELECT errors FROM user_info WHERE user_id='.$_POST['userId']);
            $ar = $result->fetch();
            $errorsCount = (int)$ar['errors'];
            $errorsCount = $errorsCount + 1;
            $result = $shardDb->query('UPDATE user_info SET errors='.$errorsCount.' WHERE user_id='.$_POST['userId']);
            if (!$result) {
                $json_data['id'] = 2;
                $json_data['status'] = 's333';
                throw new Exception("Bad request to DB!");
            }
        }

        $json_data['message'] = 'ok';
        echo json_encode($json_data);
    } catch (Exception $e) {
        $json_data['status'] = 's178';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
} else {
    $json_data['id'] = 1;
    $json_data['status'] = 's179';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}