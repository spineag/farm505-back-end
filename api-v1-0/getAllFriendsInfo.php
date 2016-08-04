<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userSocialIds']) && !empty($_POST['userSocialIds'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $m = md5($_POST['userId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's415';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            $ids = explode("&", $_POST['userSocialIds']);
            $ids = join(',', $ids);
            $result = $mainDb->query("SELECT social_id, level FROM users WHERE social_id IN (" . $ids . ")");
            $arr = $result->fetchAll();

            $json_data['message'] = $arr;
            echo json_encode($json_data);


            // try {
            //     $result = $mainDb->query("SELECT * FROM users WHERE social_id =".$_POST['userSocialId']);
            //     $u = $result->fetch();
            //     $user = [];

            //     $user['level'] = $u['level'];
            //     $user['social_id'] = $u['social_id'];

            //     $json_data['message'] = $user;
            //     echo json_encode($json_data);
            // }
            // catch (Exception $e)
            // {
            //     $json_data['status'] = 'error';
            //     $json_data['message'] = $e->getMessage();
            //     echo json_encode($json_data);
            // }
        }
    } else {
        $json_data['id'] = 13;
        $json_data['status'] = 's221';
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's073';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
