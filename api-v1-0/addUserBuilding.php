<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $result = $mainDb->queryWithAnswerId('INSERT INTO user_building SET user_id='.$_POST['userId'].', building_id='.$_POST['buildingId'].', in_inventory=0, pos_x='.$_POST['posX'].', pos_y='.$_POST['posY'].', count_cell='.$_POST['countCell']);
            if ($result) {
                $json_data['message'] = $result[1];
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's011';
                $json_data['message'] = 'bad query';
            }

            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's012';
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
    $json_data['status'] = 's013';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}