<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->query("SELECT * FROM user_recipe_fabrica WHERE user_id =".$_POST['userId']." AND user_db_building_id =".$_POST['buildDbId']);
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                $result = $mainDb->update(
                    'user_recipe_fabrica',
                    ['delay_time' => $dict['delay_time'] - $_POST['leftTime']],
                    ['id' => $dict['id']],
                    ['int'],
                    ['int']);
            }
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = '';
        echo json_encode($json_data);
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
