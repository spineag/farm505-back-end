<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 7/15/15
 * Time: 11:57 AM
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();
$memcache = $app->getMemcache();

try {
//    $resp = $memcache->get('getDataAnimal');
//    if (!$resp) {
        $result = $mainDb->query("SELECT * FROM data_animal");
        if ($result) {
            $animalALL = $result->fetchAll();
        } else {
            $json_data['id'] = 1;
            $json_data['status'] = 's259';
            throw new Exception("Bad request to DB!");
        }
        $resp = [];
        if (!empty($animalALL)) {
            foreach ($animalALL as $key => $recipe) {
                $resp[] = $recipe;
            }
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's260';
            throw new Exception("Bad request to DB!");
        }
//        $memcache->set('getDataAnimal', $resp, false, 300);
//    }
    $json_data['message'] = $resp;
    echo json_encode($json_data);
    
} catch (Exception $e) {
    $json_data['status'] = 's074';
    $json_data['message'] = $e;
    echo json_encode($json_data);
}

