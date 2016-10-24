<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $m = md5($_POST['userId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's420';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            $userId = filter_var($_POST['userId']);
            $shardDb = $app->getShardDb($userId);
            try {
                $resp = [];
                $result = $shardDb->query("SELECT * FROM user_tree WHERE user_id =" . $userId);
                if ($result) {
                    $arr = $result->fetchAll();
                    foreach ($arr as $value => $dict) {
                        $res = [];
                        $res['id'] = $dict['id'];
                        $res['state'] = $dict['state'];
                        $res['user_db_building_id'] = $dict['user_db_building_id'];
                        $res['time_work'] = time() - $dict['time_start'];
                        $res['time_start'] = $dict['time_start'];
                        $res['fixed_user_id'] = $dict['fixed_user_id'];
                        $res['crafted_count'] = $dict['crafted_count'];
                        $resp[] = $res;
                    }
                } else {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's312';
                    throw new Exception("Bad request to DB!");
                }

                $json_data['message'] = $resp;
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's111';
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
    $json_data['status'] = 's112';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
