<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $resp = [];
        $result = $mainDb->query("SELECT * FROM user_tree WHERE user_id =".$_POST['userId']);
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
