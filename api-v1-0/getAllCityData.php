<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->query("SELECT * FROM users WHERE social_id =".$_POST['userSocialId']);
        $arr = $result->fetch();
        $userId = $arr['id'];

// buildings
        $respBuildings = [];
        $result = $mainDb->query("SELECT * FROM user_building WHERE user_id =".$userId);
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                if ($build['in_inventory']) continue;
                $build = [];
                $build['id'] = $dict['id'];
                $build['building_id'] = $dict['building_id'];
                $build['pos_x'] = $dict['pos_x'];
                $build['pos_y'] = $dict['pos_y'];
                $build['is_flip'] = $dict['is_flip'];
                $startBuild = $mainDb->query("SELECT * FROM user_building_open WHERE user_id =".$userId." AND building_id =".$dict['building_id']." AND user_db_building_id =".$dict['id']);
                $date = $startBuild->fetch();
                if ($date) {
                    $build['time_build_building'] = (int)time() - (int)$date['date_start_build'];
                    $build['is_open'] = $date['is_open'];
                }
                $respBuildings[] = $build;
            }
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $result = $mainDb->query("SELECT unlocked_land FROM users WHERE id = ".$userId);
        $u = $result->fetchAll();
        $u = $u[0]['unlocked_land'];
        $arrLocked = explode("&", $u);

        $result = $mainDb->query("SELECT * FROM map_building");
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                if ( in_array($dict['id'], $arrLocked) ) continue;
                $build = [];
                $build['id'] = $dict['id'];
                $build['building_id'] = $dict['building_id'];
                $build['pos_x'] = $dict['pos_x'];
                $build['pos_y'] = $dict['pos_y'];
                $startBuild = $mainDb->query("SELECT * FROM user_building_open WHERE user_id =".$userId." AND building_id =".$dict['building_id']);
                $date = $startBuild->fetch();
                if ($date) {
                    $build['time_build_building'] = (int)time() - (int)$date['date_start_build'];
                    $build['is_open'] = $date['is_open'];
                }
                $respBuildings[] = $build;
            }
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

// plants
        $respPlants = [];
        $result = $mainDb->query("SELECT * FROM user_plant_ridge WHERE user_id =".$userId);
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                $res = [];
                $res['id'] = $dict['id'];
                $res['plant_id'] = $dict['plant_id'];
                $res['user_db_building_id'] = $dict['user_db_building_id'];
                $res['time_work'] = time() - $dict['time_start'];
                $respPlants[] = $res;
            }
        } else {
            $json_data['id'] = 3;
            throw new Exception("Bad request to DB!");
        }

// trees
        $respTrees = [];
        $result = $mainDb->query("SELECT * FROM user_tree WHERE user_id =".$userId);
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                $res = [];
                $res['id'] = $dict['id'];
                $res['state'] = $dict['state'];
                $res['user_db_building_id'] = $dict['user_db_building_id'];
                $res['time_work'] = time() - $dict['time_start'];
                $respTrees[] = $res;
            }
        } else {
            $json_data['id'] = 4;
            throw new Exception("Bad request to DB!");
        }

// animals
        $respAnimals = [];
        $result = $mainDb->query("SELECT * FROM user_animal WHERE user_id =".$userId);
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                $res = [];
                $res['id'] = $dict['id'];
                $res['animal_id'] = $dict['animal_id'];
                $res['user_db_building_id'] = $dict['user_db_building_id'];
                if ($dict['raw_time_start'] == 0) {
                    $res['time_work'] = 0;
                } else {
                    $res['time_work'] = time() - $dict['raw_time_start'];
                }
                $respAnimals[] = $res;
            }
        } else {
            $json_data['id'] = 5;
            throw new Exception("Bad request to DB!");
        }

//recipes
        $respRecipes = [];
        $result = $mainDb->query("SELECT * FROM user_recipe_fabrica WHERE user_id =".$userId);
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                $res = [];
                $res['id'] = $dict['id'];
                $res['recipe_id'] = $dict['recipe_id'];
                $res['user_db_building_id'] = $dict['user_db_building_id'];
                $res['delay'] = $dict['delay_time'];
                $res['time_work'] = time() - $dict['time_start'];
                $respRecipes[] = $res;
            }
        } else {
            $json_data['id'] = 6;
            throw new Exception("Bad request to DB!");
        }

//wild
        $arrRemoved=[];
        $result = $mainDb->query("SELECT wild_db_id FROM user_removed_wild WHERE user_id = ".$userId);
        $u = $result->fetchAll();
        foreach ($u as $value => $dict) {
            $arrRemoved[] = $dict['wild_db_id'];
        }

        $respWilds = [];
        $result = $mainDb->query("SELECT * FROM data_map_wild");
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                if ( in_array($dict['id'], $arrRemoved) ) continue;
                $build = [];
                $build['id'] = $dict['id'];
                $build['building_id'] = $dict['wild_id'];
                $build['pos_x'] = $dict['pos_x'];
                $build['pos_y'] = $dict['pos_y'];
                $build['is_flip'] = $dict['is_flip'];
                $respWilds[] = $build;
            }
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $arr = [];
        $arr['building'] = $respBuildings;
        $arr['plant'] = $respPlants;
        $arr['tree'] = $respTrees;
        $arr['animal'] = $respAnimals;
        $arr['recipe'] = $respRecipes;
        $arr['wild'] = $respWilds;
        $json_data['message'] = $arr;
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 'error';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 'error';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
