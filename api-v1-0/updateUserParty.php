<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK
    $shardDb = $app->getShardDb($userId, $channelId);
    try {
    if ($app->checkSessionKey($userId, $_POST['sessionKey'], $channelId)) {
        $m = md5($_POST['userId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's397';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $result = $shardDb->query('UPDATE user_party SET took_gift ="'.$_POST["tookGift"].'", count_resource ='.$_POST["countResource"].', show_window ='.$_POST["showWindow"].' WHERE user_id ='.$userId);
                if (!$result) {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's340';
                    throw new Exception("Bad request to DB!");
                }

                $json_data['message'] = '';
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's180';
                $json_data['message'] = $e->getMessage();
                echo json_encode($json_data);
            }
        }
    } else {
        $json_data['id'] = 13;
        $json_data['status'] = 's221';
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
catch (Exception $e)
{
    $json_data['status'] = 's098';
    $json_data['message'] = $e->getMessage();
    echo json_encode($json_data);
}
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's181';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
