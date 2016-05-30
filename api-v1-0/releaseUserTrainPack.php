<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        // $result = $mainDb->delete('user_train_pack',
        //     ['user_id' => $_POST['userId']],
        //     ['int']);
        $result = $mainDb->query('DELETE FROM user_train_pack WHERE user_id='.$_POST['userId']);
        // $result2 = $mainDb->delete('user_train_pack_item',
        //     ['user_id' => $_POST['userId']],
        //     ['int']);
        $result2 = $mainDb->query('DELETE FROM user_train_pack_item WHERE user_train_pack_id='.$_POST['id']);
        if ($result && $result2) {
            $json_data['message'] = '';
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
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