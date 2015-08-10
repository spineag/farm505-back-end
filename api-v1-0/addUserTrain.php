<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $time = time();
        $result = $mainDb->insert('user_train',
            ['user_id' => $_POST['userId'], 'count_items' => 1, 'state' => 0, 'time_start' => $time],
            ['int', 'int', 'int', 'int']);

        $result = $mainDb->query("SELECT * FROM user_train WHERE user_id =".$_POST['userId']);

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