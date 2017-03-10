<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
        $shardDb = $app->getShardDb($userId, $channelId);
    } else {
        $channelId = 2;
        $mainDb = $app->getMainDb($channelId);
    } // VK
    try {
        if ($channelId == 2) $result = $mainDb->query("UPDATE users SET language=" . $_POST['languageId']. ' WHERE id=' . $_POST['userId']);
        else $result = $shardDb->query("UPDATE user_info SET language=" . $_POST['languageId']. ' WHERE id=' . $_POST['userId']);
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