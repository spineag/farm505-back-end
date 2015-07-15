<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$mainDb = $app->getMainDb();

$result = $mainDb->select('building', '*');
if ($result) {
    $buildingsALL = $result->fetchAll();
} else {
    $json_data['id'] = 1;
    throw new Exception("Bad request to DB!");
}

try
{
    $resp = [];
    if (!empty($buildingsALL)) {
        foreach ($buildingsALL as $key => $dict) {
            $buildingItem = [];
            $buildingItem['id'] = $dict['id'];
            $buildingItem['name'] = $dict['name'];
            $buildingItem['width'] = $dict['width'];
            $buildingItem['height'] = $dict['height'];
            $buildingItem['build_type'] = $dict['build_type'];
            $buildingItem['url'] = $dict['url'];
            $buildingItem['image'] = $dict['image'];
            $buildingItem['inner_x'] = $dict['inner_x'];
            $buildingItem['inner_y'] = $dict['inner_y'];

            switch ($dict['build_type']) {
                case 2: // RIDGE
                    $result = $mainDb->select("data_ridge", "*", "building_id='".$dict['id']."'");
                    $ridge = $result->fetch();
                    if (empty($ridge)) {
                        $json_data['id'] = 2;
                        throw new Exception("Bad request to DB!");
                    }
                    $buildingItem['cost'] = $ridge['cost'];
                    $buildingItem['currency'] = $ridge['currency'];
                    $buildingItem['block_by_level'] = $ridge['block_by_level'];
                    unset($ridge);
                    break;
                case 3: // TREE
                    $result = $mainDb->select("data_tree", "*", "building_id='".$dict['id']."'");
                    $tree = $result->fetch();
                    if (empty($tree)) {
                        $json_data['id'] = 3;
                        throw new Exception("Bad request to DB!");
                    }
                    $buildingItem['cost'] = $tree['cost'];
                    $buildingItem['currency'] = $tree['currency'];
                    $buildingItem['block_by_level'] = $tree['block_by_level'];
                    $buildingItem['cost_skip'] = $tree['cost_skip'];
                    $buildingItem['build_time'] = $tree['build_time'];
                    $buildingItem['image_s'] = $tree['image_s'];
                    $buildingItem['image_s_flower'] = $tree['image_s_flower'];
                    $buildingItem['image_s_growed'] = $tree['image_s_growed'];
                    $buildingItem['image_m'] = $tree['image_m'];
                    $buildingItem['image_m_flower'] = $tree['image_m_flower'];
                    $buildingItem['image_m_growed'] = $tree['image_m_growed'];
                    $buildingItem['image_b'] = $tree['image_b'];
                    $buildingItem['image_b_flower'] = $tree['image_b_flower'];
                    $buildingItem['image_b_growed'] = $tree['image_b_growed'];
                    $buildingItem['image_dead'] = $tree['image_dead'];
                    $buildingItem['inner_position_s'] = $tree['inner_position_s'];
                    $buildingItem['inner_position_m'] = $tree['inner_position_m'];
                    $buildingItem['inner_position_b'] = $tree['inner_position_b'];
                    $buildingItem['craft_resource_id'] = $tree['craft_resource_id'];
                    $buildingItem['count_craft_resource'] = $tree['count_craft_resource'];
                    unset($tree);
                    break;
                case 4: // DECOR
                    break;
                case 9: // DECOR_FULL_FENCE
                    break;
                case 10: // DECOR_POST_FENCE
                    break;
                case 11: // FABRICA
                    $result = $mainDb->select("data_fabrica", "*", "building_id='".$dict['id']."'");
                    $fabrica = $result->fetch();
                    if (empty($fabrica)) {
                        $json_data['id'] = 11;
                        throw new Exception("Bad request to DB!");
                    }
                    $buildingItem['cost'] = $fabrica['cost'];
                    $buildingItem['currency'] = $fabrica['currency'];
                    $buildingItem['cost_skip'] = $fabrica['cost_skip'];
                    $buildingItem['build_time'] = $fabrica['build_time'];
                    $buildingItem['block_by_level'] = $fabrica['block_by_level'];
                    unset($fabrica);
                    break;
                case 12: // WILD
                    // додаткового функціоналу немає
                    break;
                case 13: // AMBAR
                    $result = $mainDb->select("data_ambar", "*", "building_id='".$dict['id']."'");
                    $ambar = $result->fetch();
                    if (empty($ambar)) {
                        $json_data['id'] = 13;
                        throw new Exception("Bad request to DB!");
                    }
                    $buildingItem['start_count_resources'] = $ambar['start_count_resources'];
                    $buildingItem['start_count_instruments'] = $ambar['start_count_instruments'];
                    $buildingItem['delta_count_resources'] = $ambar['delta_count_resources'];
                    $buildingItem['delta_count_instruments'] = $ambar['delta_count_instruments'];
                    $buildingItem['up_instrument_id_1'] = $ambar['up_instrument_id_1'];
                    $buildingItem['up_instrument_id_2'] = $ambar['up_instrument_id_2'];
                    $buildingItem['up_instrument_id_3'] = $ambar['up_instrument_id_3'];
                    break;
                case 14: // SKLAD
                    $result = $mainDb->select("data_ambar", "*", "building_id='".$dict['id']."'");
                    $sklad = $result->fetch();
                    if (empty($sklad)) {
                        $json_data['id'] = 13;
                        throw new Exception("Bad request to DB!");
                    }
                    $buildingItem['start_count_resources'] = $sklad['start_count_resources'];
                    $buildingItem['start_count_instruments'] = $sklad['start_count_instruments'];
                    $buildingItem['delta_count_resources'] = $sklad['delta_count_resources'];
                    $buildingItem['delta_count_instruments'] = $sklad['delta_count_instruments'];
                    $buildingItem['up_instrument_id_1'] = $sklad['up_instrument_id_1'];
                    $buildingItem['up_instrument_id_2'] = $sklad['up_instrument_id_2'];
                    $buildingItem['up_instrument_id_3'] = $sklad['up_instrument_id_3'];
                    break;
                case 15: // DECOR_TAIL
                    break;
                case 16: // FARM
                    $result = $mainDb->select("data_farm", "*", "building_id='".$dict['id']."'");
                    $farm = $result->fetch();
                    if (empty($farm)) {
                        $json_data['id'] = 16;
                        throw new Exception("Bad request to DB!");
                    }
                    $buildingItem['cost'] = $farm['cost'];
                    $buildingItem['currency'] = $farm['currency'];
                    $buildingItem['block_by_level'] = $farm['block_by_level'];
                    $buildingItem['inner_house_x'] = $farm['inner_house_x'];
                    $buildingItem['inner_house_y'] = $farm['inner_house_y'];
                    $buildingItem['image_house'] = $farm['image_house'];
                    $buildingItem['max_count'] = $farm['max_count'];
                    break;
                default:
                    break;
            }
            $resp[] = $buildingItem;
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


