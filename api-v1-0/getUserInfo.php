<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->query("SELECT * FROM users WHERE id =".$_POST['userId']);
        $u = $result->fetch();
        $user = [];
        $user['ambar_max'] = $u['ambar_max'];
        $user['sklad_max'] = $u['sklad_max'];
        $user['ambar_level'] = $u['ambar_level'];
        $user['sklad_level'] = $u['sklad_level'];
        $user['hard_count'] = $u['hard_count'];
        $user['soft_count'] = $u['soft_count'];
        $user['yellow_count'] = $u['yellow_count'];
        $user['green_count'] = $u['green_count'];
        $user['red_count'] = $u['red_count'];
        $user['blue_count'] = $u['blue_count'];
        $user['xp'] = $u['xp'];
        $user['is_tester'] = $u['is_tester'];
        $user['count_cats'] = $u['count_cats'];

        $json_data['message'] = $user;
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
