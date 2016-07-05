<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->query('UPDATE user_animal SET raw_time_start='.time().' WHERE id='.$_POST['anDbId']);
        if ($result) {
            $json_data['message'] = '';
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's139';
            $json_data['message'] = 'bad query';
        }

    }
    catch (Exception $e)
    {
        $json_data['status'] = 's140';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's141';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}