<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2;//VK
    try {
        if ($channelId == 2) {
            $mainDb = $app->getMainDb($channelId);
            $result = $mainDb->query("UPDATE users SET language_id=" . $_POST['languageId']. ' WHERE id=' . $_POST['userId']);
            if (!$result) {
                $json_data['id'] = 2;
                $json_data['status'] = 's341';
                throw new Exception("Bad request to DB!");
            }
        } else { // == 3 || == 4
            $shardDb = $app->getShardDb($_POST['userId'], $channelId);
            $result = $shardDb->query("UPDATE user_info SET language_id=" . $_POST['languageId']. ' WHERE user_id=' . $_POST['userId']);
            if (!$result) {
                $json_data['id'] = 2;
                $json_data['status'] = 's341';
                throw new Exception("Bad request to DB!");
            }
        }
        if (!$result) {
            $json_data['id'] = 2;
            $json_data['status'] = 's341';
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = '';
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's098';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's023';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}