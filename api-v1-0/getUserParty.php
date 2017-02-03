<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
if (isset($_POST['channelId'])) {
    $channelId = (int)$_POST['channelId'];
} else $channelId = 2; // VK
$mainDb = $app->getMainDb($channelId);

try {
    $result = $shardDb->query("SELECT * FROM user_party WHERE user_id =" . $userId);
    if ($result) {
        $arr = $result->fetch();
            $res = [];
            $res['id'] = $arr['id'];
            $res['count_resource'] = $arr['count_resource'];
            $res['took_gift'] = $arr['took_gift'];
        if ($res['id'] == null) {
            $result = $shardDb->query('INSERT INTO user_party SET user_id=' . $userId . ', count_resource =' . 0 .', took_gift =' . "0&0&0&0&0");
            $res['id'] = 0;
            $res['count_resource'] = 0;
            $res['took_gift'] = "0&0&0&0&0";
        }
    } else {
        $json_data['id'] = 2;
        $json_data['status'] = 's307';
        throw new Exception("Bad request to DB!");
    }

    $json_data['message'] = $res;
    echo json_encode($json_data);
}
catch (Exception $e)
{
    $json_data['status'] = 's098';
    $json_data['message'] = $e->getMessage();
    echo json_encode($json_data);
}

