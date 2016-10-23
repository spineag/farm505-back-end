<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $userId = filter_var($_POST['userId']);
        $shardDb = $app->getShardDb($userId);
        try {
            $resp = [];
            $result = $shardDb->query("SELECT * FROM user_order WHERE user_id =" . $userId);
            if ($result) {
                $arr = $result->fetchAll();
                foreach ($arr as $value => $dict) {
                    $res = [];
                    $res['id'] = $dict['id'];
                    $res['ids'] = $dict['ids'];
                    $res['counts'] = $dict['counts'];
                    $res['xp'] = $dict['xp'];
                    $res['coins'] = $dict['coins'];
                    $res['add_coupone'] = $dict['add_coupone'];
                    $res['start_time'] = $dict['start_time'];
                    $res['place'] = $dict['place'];
                    $res['faster_buyer'] = $dict['faster_buyer'];
                    $resp[] = $res;
                }
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's307';
                throw new Exception("Bad request to DB!");
            }

            $json_data['message'] = $resp;
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's098';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
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
    $json_data['status'] = 's099';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
