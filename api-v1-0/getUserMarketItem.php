<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $resp = [];
        $result = $mainDb->query("SELECT * FROM users WHERE social_id =".$_POST['userSocialId']);
        $arr = $result->fetch();
        $id = $arr['id'];

        $result = $mainDb->query("SELECT * FROM user_market_item WHERE user_id =".$id);
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                $res = [];
                $res['id'] = $dict['id'];
                $res['buyer_id'] = $dict['buyer_id'];
                $res['time_start'] = $dict['time_start'];
                $res['time_sold'] = $dict['time_sold'];
                $res['cost'] = $dict['cost'];
                $res['resource_id'] = $dict['resource_id'];
                $res['resource_count'] = $dict['resource_count'];
                $res['in_papper'] = $dict['in_papper'];
                $resp[] = $res;
            }
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = $resp;
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 'error';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 'error';
    $json_data['message'] = 'bad POST[userSocialId]';
    echo json_encode($json_data);
}
