<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $time = time();
        $result = $mainDb->queryWithAnswerId('INSERT INTO user_plant_ridge SET user_id='.$_POST['userId'].', user_db_building_id='.$_POST['dbId'].', plant_id='.$_POST['plantId'].', time_start='.$time);    
        if ($result) {
            $json_data['message'] = $result[1];
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's136';
            $json_data['message'] = 'bad query';
        }
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's137';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's138';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}