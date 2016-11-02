<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK
    $shardDb = $app->getShardDb($userId, $channelId);

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        $m = md5($_POST['userId'].$_POST['userSocialId'].$_POST['id'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's376';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                // trees
                $respTrees = [];
                $result = $shardDb->query("SELECT * FROM user_tree WHERE id = " . $_POST['id']); //user_id =".$userId);
                if ($result) {
                    $arr = $result->fetch();
                    $res = [];
                    $res['id'] = $arr['id'];
                    $res['user_db_building_id'] = $arr['user_db_building_id'];
                    $res['time_work'] = time() - $arr['time_start'];
                    $res['state'] = $arr['state'];

                } else {
                    $json_data['id'] = 4;
                    $json_data['status'] = 's258';
                    throw new Exception("Bad request to DB!");
                }
                $json_data['message'] = $res;
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's229';
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
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's230';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
