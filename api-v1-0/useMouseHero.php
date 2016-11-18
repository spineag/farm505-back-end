<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        $m = md5($_POST['userId'].$_POST['count'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's412';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                if ($channelId == 2) {
                    $mainDb = $app->getMainDb($channelId);
                    $result = $mainDb->query('UPDATE users SET mouse_count=' . $_POST['count'] . ', mouse_day=' . time() . ' WHERE id=' . $_POST['userId']);
                    if (!$result) {
                        $json_data['id'] = 2;
                        $json_data['status'] = 's349';
                        throw new Exception("Bad request to DB!");
                    }
                } else {
                    $shardDb = $app->getShardDb($_POST['userId'], $channelId);
                    $result = $shardDb->query('UPDATE user_info SET mouse_count=' . $_POST['count'] . ', mouse_day=' . time() . ' WHERE user_id=' . $_POST['userId']);
                    if (!$result) {
                        $json_data['id'] = 2;
                        $json_data['status'] = 's349';
                        throw new Exception("Bad request to DB!");
                    }
                }

                $json_data['message'] = '';
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's204';
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
    $json_data['status'] = 's205';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
