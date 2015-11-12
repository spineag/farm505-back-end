<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->insert('data_map_wild',
            ['wild_id' => $_POST['wildId'], 'pos_x' => $_POST['posX'], 'pos_y' => $_POST['posY']],
            ['int', 'int', 'int']);

        $result = $mainDb->query("SELECT id FROM data_map_wild WHERE wild_id =".$_POST['wildId']." AND pos_x=".$_POST['posX']." AND pos_y=".$_POST['posY'] );
        if ($result) {
            $arr = $result->fetch();
            $json_data['message'] = $arr['id'];
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