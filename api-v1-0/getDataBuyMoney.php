<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 7/14/15
 * Time: 4:01 PM
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();

$result = $mainDb->query("SELECT * FROM data_buy_money");
if ($result) {
    $dataMoney = $result->fetchAll();
} else {
    $json_data['id'] = 6;
    throw new Exception("Bad request to DB!");
}

try
{
    $resp = [];
    if (!empty($dataMoney)) {
        foreach ($dataMoney as $key => $m) {
            $resp[] = $m;
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

