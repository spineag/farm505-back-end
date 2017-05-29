<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$channelId = (int)$_POST['channelId'];
$mainDb = $app->getMainDb($channelId);

try {
    $result = $mainDb->query("SELECT * FROM data_invite_viral");
    if ($result) {
        $r = $result->fetch();

    } else {
        $json_data['id'] = 2;
        $json_data['status'] = 's...';
        throw new Exception("Bad request to DB!");
    }

    $json_data['message'] = $r;
    echo json_encode($json_data);
}
catch (Exception $e)
{
    $json_data['status'] = 's...';
    $json_data['message'] = $e->getMessage();
    echo json_encode($json_data);
}

