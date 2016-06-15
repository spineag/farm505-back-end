<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();

$result = $mainDb->query("SELECT pos_x, pos_y FROM data_outgame_tile");
if ($result) {
    $data = $result->fetchAll();
} else {
    $json_data['id'] = 2;
    throw new Exception("Bad request to DB!");
}

try
{
    $resp = [];
    if (!empty($data)) {
        foreach ($data as $key => $tile) {
            $resp[] = $tile;
        }
    }

    $json_data['message'] = $resp;
    echo json_encode($json_data);
}
catch (Exception $e)
{
    $json_data['status'] = 's079';
    $json_data['message'] = $e;
    echo json_encode($json_data);
}

