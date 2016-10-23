<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    $shardDb = $app->getShardDb($userId);
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $m = md5($_POST['userId'].$_POST['id'].$_POST['state'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's372';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $result = $shardDb->query("SELECT * FROM user_tree WHERE id =" . $_POST['id']);
                if ($result) {
                    $arr = $result->fetch();
                    if ($arr['state'] == $_POST['state']) {
                        $count = (int)$arr['crafted_count'] + 1;
                        $result = $shardDb->query('UPDATE user_tree SET crafted_count = ' . $count . ' WHERE id=' . $_POST['id']);
                        if (!$result) {
                            $json_data['id'] = 4;
                            $json_data['status'] = 's245';
                            throw new Exception("Bad request to DB at update!");
                        }
                    } else {
                        $json_data['id'] = 3;
                        $json_data['status'] = 's246';
                        throw new Exception("different tree state");
                    }
                } else {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's247';
                    throw new Exception("Bad request to DB!");
                }

                $json_data['message'] = '';
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's052';
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
    $json_data['status'] = 's053';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
