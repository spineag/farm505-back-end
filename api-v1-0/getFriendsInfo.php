<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $result = $mainDb->query("SELECT * FROM users WHERE social_id =".$_POST['userSocialId']);
            $u = $result->fetch();
            $user = [];

            $user['level'] = $u['level'];
            $user['social_id'] = $u['social_id'];

            $json_data['message'] = $user;
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's082';
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
    $json_data['status'] = 's083';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
