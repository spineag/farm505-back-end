<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    $shardDb = $app->getShardDb($userId);
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $m = md5($_POST['userId'].$_POST['resourceId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's371';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $result = $shardDb->query('DELETE FROM user_cave WHERE resource_id=' . $_POST['resourceId'] . ' AND user_id = ' . $_POST['userId']);
                if ($result) {
                    $json_data['message'] = '';
                    echo json_encode($json_data);
                } else {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's244';
                    throw new Exception("Bad request to DB!");
                }
            } catch (Exception $e) {
                $json_data['status'] = 's225';
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
    $json_data['status'] = 's226';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
