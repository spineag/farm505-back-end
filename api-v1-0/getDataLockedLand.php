<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 7/15/15
 * Time: 11:57 AM
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();

$result = $mainDb->query("SELECT unlocked_land FROM users WHERE id = ".$_POST['userId']);
$u = $result->fetchAll();
$u = $u[0]['unlocked_land'];
$arrLocked = explode("&", $u);

$result = $mainDb->select('data_locked_land', '*');
if ($result) {
    $lands = $result->fetchAll();
} else {
    $json_data['id'] = 1;
    throw new Exception("Bad request to DB!");
}

try
{
    $resp = [];
    if (!empty($lands)) {
        foreach ($lands as $key => $land) {
            if ( in_array($land['map_building_id'], $arrLocked) ) continue;
            $resp[] = $land;
        }
    } else {
        $json_data['id'] = 2;
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