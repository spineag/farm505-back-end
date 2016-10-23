<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    $shardDb = $app->getShardDb($userId);
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $time = time();
            // $result = $mainDb->insert('user_removed_wild',
            //     ['user_id' => $_POST['userId'], 'wild_db_id' => $_POST['dbId']],
            //     ['int', 'int']);
            $result = $shardDb->query('INSERT INTO user_removed_wild SET user_id='.$userId.', wild_db_id='.$_POST['dbId']);

           if ($result) {
                $json_data['message'] = '';
                echo json_encode($json_data);
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's062';
                $json_data['message'] = 'bad query';
            }

        }
        catch (Exception $e)
        {
            $json_data['status'] = 's063';
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
    $json_data['status'] = 's064';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}