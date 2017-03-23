<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK
    $mainDb = $app->getMainDb($channelId);
    try {
        $result = $mainDb->query("SELECT * FROM data_achievement");
        if ($result) {
            $arr = $result->fetchAll();
//            foreach ($arr as $value => $dict) {
//                $res = [];
//                $res['id'] = $dict['id'];
//                $res['text_id_name'] = $dict['text_id_name'];
//                $res['text_id_description'] = $dict['text_id_description'];
//                $res['count_to_gift'] = $dict['count_to_gift'];
//                $res['count_xp'] = $dict['count_xp'];
//                $res['count_hard'] = $dict['count_hard'];
//                $res['type_action'] = $dict['type_action'];
//                $res['id_resource'] = $dict['id_resource'];
//            }
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's301';
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = $arr;
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's098';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
