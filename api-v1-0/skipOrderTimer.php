<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            // $result = $mainDb->update(
            //         'user_order',
            //         ['start_time' => time() - 30*60],
            //         ['id' => $_POST['dbId']],
            //         ['int'],
            //         ['int']);
           $result = $mainDb->query('UPDATE user_order SET start_time='.(time()-30*60).' WHERE id='.$_POST['dbId']);
            if (!$result) {
                $json_data['id'] = 2;
                $json_data['status'] = 's320';
                throw new Exception("Bad request to DB!");
            }

            $json_data['message'] = '';
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's151';
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
    $json_data['status'] = 's152';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
