<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $time = time();
        $result = $mainDb->insert('user_recipe_fabrica',
            ['user_id' => $_POST['userId'], 'user_db_building_id' => $_POST['dbId'], 'recipe_id' => $_POST['recipeId'], 'delay_time' => $_POST['delay'], 'time_start' => $time],
            ['int', 'int', 'int', 'int', 'int']);

        $result = $mainDb->query("SELECT * FROM user_recipe_fabrica WHERE user_id =".$_POST['userId']." AND time_start=".$time);

        if ($result) {
            $arr= $result->fetch();
            $json_data['message'] = $arr['id'];
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