<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

     try {
        $time = time();
        // $result = $mainDb->update(
        //     'user_building_open',
        //     ['date_start_build' =>  $_POST['leftTime']],
        //     ['user_db_building_id' => $_POST['buildDbId']],
        //     ['int'], 
        //     ['int']);
        $result = $mainDb->query('UPDATE user_building_open SET date_start_build='.$_POST['leftTime'].' WHERE user_db_building_id='.$_POST['buildDbId']);                
        if (!$result) {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = '';
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's157';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's158';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}