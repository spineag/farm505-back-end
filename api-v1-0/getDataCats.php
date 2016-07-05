<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();

// $result = $mainDb->select('data_cat', '*');
$result = $mainDb->query("SELECT * FROM data_cat");

if ($result) {
    $cats = $result->fetchAll();
} else {
    $json_data['id'] = 1;
    throw new Exception("Bad request to DB!");
}

try
{
    $resp = [];
    if (!empty($cats)) {
        foreach ($cats as $key => $dict) {
            $item = [];
            $item['id'] = $dict['id'];
            $item['cost'] = $dict['cost'];
            $item['block_by_level'] = $dict['block_by_level'];
            $resp[] = $item;
        }
    } else {
        $json_data['id'] = 1;
        throw new Exception("Bad request to DB!");
    }

    $json_data['message'] = $resp;
    echo json_encode($json_data);
}
catch (Exception $e)
{
    $json_data['status'] = 's076';
    $json_data['message'] = $e;
    echo json_encode($json_data);
}


