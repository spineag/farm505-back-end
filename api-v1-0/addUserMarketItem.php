<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $time = time();
        $result = $mainDb->insert('user_market_item',
            ['user_id' => $_POST['userId'], 'buyer_id' => 0, 'resource_id' => $_POST['resourceId'], 'time_start' => $time, 'time_sold' => 0, 'cost' => $_POST['cost'], 'resource_count' => $_POST['count'], 'in_papper' => $_POST['inPapper']],
            ['int', 'int', 'int', 'int', 'int', 'int', 'int', 'int']);

//        $result = $mainDb->query("SELECT * FROM user_market_item WHERE user_id =".$_POST['userId']." AND time_start=".$time);

        $result = $mainDb->query("SELECT * FROM user_market_item WHERE user_id =".$_POST['userId']." AND time_start = ".$time);
        if ($result) {
            $u = $result->fetch();
            $res = [];
            $res['id'] = $u['id'];
            $res['buyer_id'] = $u['buyer_id'];
            $res['time_start'] = $u['time_start'];
            $res['time_sold'] = $u['time_sold'];
            $res['cost'] = $u['cost'];
            $res['resource_id'] = $u['resource_id'];
            $res['resource_count'] = $u['resource_count'];
            $res['in_papper'] = $u['in_papper'];

        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = $res;
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
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}