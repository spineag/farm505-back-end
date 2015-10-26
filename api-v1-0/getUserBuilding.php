<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $resp = [];
        $result = $mainDb->query("SELECT * FROM user_building WHERE user_id =".$_POST['userId']);
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                $build = [];
                $build['id'] = $dict['id'];
                $build['building_id'] = $dict['building_id'];
                $build['pos_x'] = $dict['pos_x'];
                $build['pos_y'] = $dict['pos_y'];
                $build['is_flip'] = $dict['is_flip'];
                $build['in_inventory'] = $dict['in_inventory'];
                $startBuild = $mainDb->query("SELECT * FROM user_building_open WHERE user_id =".$_POST['userId']." AND building_id =".$dict['building_id']." AND user_db_building_id =".$dict['id']);
                $date = $startBuild->fetch();
                if ($date) {
                    $build['time_build_building'] = (int)time() - (int)$date['date_start_build'];
                    $build['is_open'] = $date['is_open'];
                }
                $resp[] = $build;
            }
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $result = $mainDb->query("SELECT unlocked_land FROM users WHERE id = ".$_POST['userId']);
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
                $startBuild = $mainDb->query("SELECT * FROM user_building_open WHERE user_id =".$_POST['userId']." AND building_id =".$dict['building_id']);
                $date = $startBuild->fetch();
                if ($date) {
                    $build['time_build_building'] = (int)time() - (int)$date['date_start_build'];
                    $build['is_open'] = $date['is_open'];
                }
                $resp[] = $build;
            }
        } else {
            $json_data['id'] = 3;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = $resp;
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
