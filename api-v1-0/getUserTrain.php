<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $result = $mainDb->query("SELECT * FROM user_train WHERE user_id =".$_POST['userId']);
            if ($result) {
                $arr = $result->fetch();
                $res = [];
                $res['id'] = $arr['id'];
                $res['state'] = $arr['state'];
                $res['count_items'] = $arr['count_items'];
                $res['time_work'] = time() - $arr['time_start'];

            } else {
                $json_data['id'] = 2;
                throw new Exception("Bad request to DB!");
            }

            $json_data['message'] = $res;
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's106';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
        }
    } else {
        $json_data['id'] = 13;
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's107';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
