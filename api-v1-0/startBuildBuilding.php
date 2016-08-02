<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            // $result = $mainDb->insert('user_building_open',
            //     ['user_id' => $_POST['userId'], 'building_id' => $_POST['buildingId'], 'user_db_building_id' => $_POST['dbId'], 'date_start_build' => time(), 'is_open' => 0],
            //     ['int', 'int', 'int', 'int', 'int']);
            $result = $mainDb->query('INSERT INTO user_building_open SET user_id='.$_POST['userId'].', user_db_building_id='.$_POST['dbId'].', building_id='.$_POST['buildingId'].', is_open=0, date_start_build='.time());

            if ($result) {
                $json_data['message'] = '';
                echo json_encode($json_data);
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's167';
                $json_data['message'] = 'bad query';
            }

        }
        catch (Exception $e)
        {
            $json_data['status'] = 's168';
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
    $json_data['status'] = 's169';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}