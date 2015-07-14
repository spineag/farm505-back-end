<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 7/14/15
 * Time: 4:01 PM
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();

$result = $mainDb->query("SELECT * FROM data_level");
$dataLevel = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dataLevel[] = $row;
    }
    $result->close();
} else {
    $json_data['id'] = 6;
    throw new Exception("Bad request to DB!");
}

try
{
    $resp = [];
    if (!empty($dataLevel)) {
        foreach ($dataLevel as $key => $level) {
            $resp[] = $level;
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
    $json_data['status'] = 'error';
    $json_data['message'] = $e;
    echo json_encode($json_data);
}

