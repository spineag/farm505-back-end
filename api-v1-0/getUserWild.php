<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $arrRemoved=[];
        $result = $mainDb->query("SELECT wild_db_id FROM user_removed_wild WHERE user_id = ".$_POST['userId']);
        $u = $result->fetchAll();
        foreach ($u as $value => $dict) {
            $arrRemoved[] = $dict['wild_db_id'];
        }

        $resp = [];
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
                $resp[] = $build;
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
