<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK
    $mainDb = $app->getMainDb($channelId);

    try {
        $resp = [];
        $result = $mainDb->query("SELECT * FROM data_starter_pack");
        if ($result) {
            $arr = $result->fetchAll();
            foreach ($arr as $value => $dict) {
                $res = [];
                $res['id'] = $dict['id'];
                $res['soft_count'] = $dict['soft_count'];
                $res['hard_count'] = $dict['hard_count'];
                $res['object_id'] = $dict['object_id'];
                $res['object_type'] = $dict['object_type'];
                $res['object_count'] = $dict['object_count'];
                $res['old_cost'] = $dict['old_cost'];
                $res['new_cost'] = $dict['new_cost'];
            }
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's301';
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = $res;
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's227';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }


