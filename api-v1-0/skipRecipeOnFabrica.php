<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $m = md5($_POST['userId'].$_POST['recipeDbId'].$_POST['leftTime'].$_POST['buildDbId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's385';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $result = $mainDb->query("SELECT * FROM user_recipe_fabrica WHERE user_id =" . $_POST['userId'] . " AND user_db_building_id =" . $_POST['buildDbId']);
                if ($result) {
                    $arr = $result->fetchAll();
                    foreach ($arr as $value => $dict) {
                        $result = $mainDb->query('UPDATE user_recipe_fabrica SET delay_time=' . ($dict['delay_time'] - $_POST['leftTime']) . ' WHERE id=' . $dict['id']);
                    }
                } else {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's321';
                    throw new Exception("Bad request to DB!");
                }

                $json_data['message'] = '';
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's153';
                $json_data['message'] = $e->getMessage();
                echo json_encode($json_data);
            }
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
    $json_data['status'] = 's154';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
