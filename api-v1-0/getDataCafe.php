<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
if (isset($_POST['channelId'])) {
    $channelId = (int)$_POST['channelId'];
} else $channelId = 2; // VK
$mainDb = $app->getMainDb($channelId);

try {
    $result = $mainDb->query("SELECT * FROM data_cafe");
    if ($result) {
        $r = [];
        $r = $result->fetchALL();

    } else {
        $json_data['id'] = 2;
        $json_data['status'] = 's307';
        throw new Exception("Bad request to DB!");
    }

    $json_data['message'] = $r;
    echo json_encode($json_data);
}
catch (Exception $e)
{
    $json_data['status'] = 's098';
    $json_data['message'] = $e->getMessage();
    echo json_encode($json_data);
}

