<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $resp = [];
        $result = $mainDb->query("SELECT * FROM user_order WHERE user_id =".$_POST['userId']);
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
                $res['left_time'] = (int)$dict['start_time'] - time();
                if ($res['left_time'] < 0) $res['left_time'] = 0;
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
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
