<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->insert('user_building',
            ['user_id' => $_POST['userId'], 'building_id' => $_POST['buildingId'], 'in_inventory' => 0, 'pos_x' => $_POST['posX'], 'pos_y' => $_POST['posY']],
            ['int', 'int', 'int', 'int', 'int']);

        $result = $mainDb->query("SELECT id FROM user_building WHERE user_id =".$_POST['userId']." AND building_id=".$_POST['buildingId']);
        if ($result) {
            $arr = $result->fetchAll();
            $json_data['message'] = array_pop($arr)['id'];
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 'error';
            $json_data['message'] = 'bad query';
        }

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