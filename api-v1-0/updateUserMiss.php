<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK
    $shardDb = $app->getShardDb($_POST['userId'], $channelId);

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        $m = md5($_POST['userId'].$_POST['taskId'].$_POST['countDone'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's453';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $result = $shardDb->query("SELECT * FROM user_miss WHERE user_id =" . $userId .' AND user_id_miss = ' .$_POST["userIdMiss"]);
                if ($result) {
                    $arr = $result->fetchAll();
                    if (!$arr) {
                        $result = $shardDb->query('INSERT INTO user_miss SET user_id=' . $userId . ', user_id_miss=' . $_POST["userIdMiss"] . ', count_send = ' . $_POST['countSend'] . ', send ='  . $_POST['send']);
                    } else {
                        $result = $shardDb->query('UPDATE user_miss SET count_send =' . $_POST['countSend'] . ', send ='  . $_POST['send'].' WHERE user_id='.$_POST["userId"].' AND user_id_miss='.$_POST["userIdMiss"]);
                    }
                }
                $json_data['message'] = '';
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's455';
                $json_data['message'] = $e->getMessage();
                echo json_encode($json_data);
            }
        }
    } else {
        $json_data['id'] = 13;
        $json_data['status'] = 's456';
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's457';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
