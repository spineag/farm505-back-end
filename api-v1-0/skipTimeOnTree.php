<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        //  $result = $mainDb->update(
        //         'user_tree',
        //         ['state' => $_POST['state'], 'time_start' => time()],
        //         ['user_db_building_id' => $_POST['id']],
        //         ['int', 'int'],
        //         ['int']);
        $result = $mainDb->query('UPDATE user_tree SET state='.$_POST['state'].', time_start='.time().' WHERE user_db_building_id='.$_POST['id']);                
        if (!$result) {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = '';
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's161';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's162';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}