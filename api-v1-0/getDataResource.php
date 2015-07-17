<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();

$result = $mainDb->select('resource', '*');
if ($result) {
    $resourcesALL = $result->fetchAll();
} else {
    $json_data['id'] = 1;
    throw new Exception("Bad request to DB!");
}

try
{
    $resp = [];
    if (!empty($resourcesALL)) {
        foreach ($resourcesALL as $key => $dict) {
            $resourceItem = [];
            $resourceItem['id'] = $dict['id'];
            $resourceItem['name'] = $dict['name'];
            $resourceItem['resource_type'] = $dict['resource_type'];
            $resourceItem['resource_place'] = $dict['resource_place'];
            $resourceItem['url'] = $dict['url'];
            $resourceItem['image_shop'] = $dict['image_shop'];
            $resourceItem['currency'] = $dict['currency'];
            $resourceItem['cost_max'] = $dict['cost_max'];
            $resourceItem['cost_min'] = $dict['cost_min'];
            $resourceItem['cost_hard'] = $dict['cost_hard'];
            $resourceItem['block_by_level'] = $dict['block_by_level'];

            switch ($dict['resource_type']) {
                case 5: // PLANT
                    //$result = $mainDb->select("data_plant", "*", "resource_id='".$dict['id']."'");
                    $result = $mainDb->query("SELECT * FROM data_plant WHERE resource_id ='".$dict['id']."'");

                    $plant = $result->fetch();
                    if (empty($plant)) {
                        $json_data['id'] = 2;
                        throw new Exception("Bad request to DB!");
                    }
                    $resourceItem['build_time'] = $plant['build_time'];
                    $resourceItem['craft_xp'] = $plant['craft_xp'];
                    $resourceItem['cost_skip'] = $plant['cost_skip'];
                    $resourceItem['image1'] = $plant['image1'];
                    $resourceItem['image2'] = $plant['image2'];
                    $resourceItem['image3'] = $plant['image3'];
                    $resourceItem['image4'] = $plant['image4'];
                    $resourceItem['image_harvested'] = $plant['image_harvested'];
                    $resourceItem['inner_positions'] = $plant['inner_positions'];
                    break;
                case 7: // INSTRUMENT
                    break;
                case 8: // RESOURCE
                    $result = $mainDb->select("data_resource", "*", "resource_id='".$dict['id']."'");
                    $resource = $result->fetch();
                    if (empty($resource)) {
                        $json_data['id'] = 3;
                        throw new Exception("Bad request to DB!");
                    }
                    $resourceItem['build_time'] = $resource['build_time'];
                    $resourceItem['craft_xp'] = $resource['craft_xp'];
                    $resourceItem['cost_skip'] = $resource['cost_skip'];
                    break;
                default:
                    break;
            }
            $resp[] = $resourceItem;
        }
    } else {
        $json_data['id'] = 4;
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


