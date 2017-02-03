<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
if (isset($_POST['channelId'])) {
    $channelId = (int)$_POST['channelId'];
} else $channelId = 2; // VK
$mainDb = $app->getMainDb($channelId);

try {
    $result = $mainDb->query("SELECT * FROM data_party");
    if ($result) {
        $r = $result->fetch();
            $res = [];
            $res['id'] = $r['id'];
            $res['id_gift'] = $r['id_gift'];
            $res['type_gift'] = $r['type_gift'];
            $res['count_gift'] = $r['count_gift'];
            $res['count_to_gift'] = $r['count_to_gift'];
            $res['time_to_end'] = $r['time_to_end'];
            $res['name'] = $r['name'];
            $res['description'] = $r['description'];
            $res['party_on'] = $r['party_on'];

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

