<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $time = time();
        // $result = $mainDb->update(
        //     'users',
        //     ['tutorial_step' => $_POST['step']],
        //     ['id' => $_POST['userId']],
        //     ['int'],
        //     ['int']);
        $result = $mainDb->query('UPDATE users SET wall_order_item_time='.$time.' WHERE id='.$_POST['userId']);
        if (!$result) {
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