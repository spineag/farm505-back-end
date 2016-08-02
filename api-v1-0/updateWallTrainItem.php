<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $time = time();
            // $result = $mainDb->update(
            //     'users',
            //     ['tutorial_step' => $_POST['step']],
            //     ['id' => $_POST['userId']],
            //     ['int'],
            //     ['int']);
            $result = $mainDb->query('UPDATE users SET wall_train_item = '.$time.' WHERE id = '.$_POST['userId']);
            if (!$result) {
                $json_data['id'] = 2;
                $json_data['status'] = 's347';
                throw new Exception("Bad request to DB!");
            }

            $json_data['message'] = '';
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's200';
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
    $json_data['status'] = 's201';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}