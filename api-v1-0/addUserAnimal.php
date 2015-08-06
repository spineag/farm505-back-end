<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $time = time();
        $result = $mainDb->insert('user_animal',
            ['user_id' => $_POST['userId'], 'user_db_building_id' => $_POST['farmDbId'], 'animal_id' => $_POST['animalId'], 'raw_time_start' => $time],
            ['int', 'int', 'int', 'int']);

        $result = $mainDb->query("SELECT * FROM user_animal WHERE user_id =".$_POST['userId']." AND raw_time_start=".$time);

        if ($result) {
            $arr= $result->fetch();
            $json_data['message'] = $arr['id'];
            $result = $mainDb->update(
                'user_animal',
                ['raw_time_start' => 0],
                ['id' => $arr['id']],
                ['int'],
                ['int']);
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 'error';
            $json_data['message'] = 'bad query';
        }

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