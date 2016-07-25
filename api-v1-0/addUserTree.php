<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $time = time();
            $result = $mainDb->queryWithAnswerId('INSERT INTO user_tree SET user_id='.$_POST['userId'].', user_db_building_id='.$_POST['dbId'].', state=1, time_start='.$time);
            if ($result) {
                $json_data['message'] = $result[1];
                echo json_encode($json_data);
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's030';
                $json_data['message'] = 'bad query';
            }

        }
        catch (Exception $e)
        {
            $json_data['status'] = 's031';
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
    $json_data['status'] = 's032';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}