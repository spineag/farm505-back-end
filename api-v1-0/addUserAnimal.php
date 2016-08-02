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
            $result = $mainDb->queryWithAnswerId('INSERT INTO user_animal SET user_id='.$_POST['userId'].', user_db_building_id='.$_POST['farmDbId'].', animal_id='.$_POST['animalId'].', raw_time_start='.$time);
            if ($result) {
                $json_data['message'] = $result[1];
                $result = $mainDb->query('UPDATE user_animal SET raw_time_start = 0 WHERE id='.$result[1]);
                echo json_encode($json_data);
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's008';
                $json_data['message'] = 'bad query';
            }

        }
        catch (Exception $e)
        {
            $json_data['status'] = 's009';
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
    $json_data['status'] = 's010';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}