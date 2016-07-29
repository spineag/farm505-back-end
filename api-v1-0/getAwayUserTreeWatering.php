<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $result = $mainDb->query("SELECT * FROM users_tree WHERE social_id =".$_POST['awayId']." AND user_db_building_id = ".$_POST['userDbBuildingId']);
            $u = $result->fetch();
            $user = [];

            $user['state'] = $u['state'];
//            $user['social_id'] = $u['social_id'];

            $json_data['message'] = $user;
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's229';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
        }
    } else {
        $json_data['id'] = 13;
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's230';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
