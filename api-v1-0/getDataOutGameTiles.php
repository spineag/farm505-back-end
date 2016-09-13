<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();
$memcache = $app->getMemcache();

try {
    $resp = $memcache->get('getDataOutGameTiles3');
    if (!$resp) {
        $result = $mainDb->query("SELECT pos_x, pos_y FROM data_outgame_tile");
        if ($result) {
            $data = $result->fetchAll();
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's290';
            throw new Exception("Bad request to DB!");
        }
        $resp = [];
        if (!empty($data)) {
            foreach ($data as $key => $tile) {
                $resp[] = $tile;
            }
        }
        $memcache->set('getDataOutGameTiles3', $resp, MEMCACHED_DICT_TIME);
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

