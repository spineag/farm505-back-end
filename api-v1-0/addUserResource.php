<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK
    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $m = md5($_POST['userId'] . $_POST['resourceId'] . $_POST['countAll'] . $app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's360';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $result = $mainDb->query("SELECT * FROM user_resource WHERE user_id =" . $_POST['userId'] . " AND resource_id=" . $_POST['resourceId']);
                if ($result) {
                    $result = $mainDb->query('UPDATE user_resource SET count = ' . $_POST['countAll'] . ' WHERE user_id=' . $_POST['userId'] . ' AND resource_id = ' . $_POST['resourceId']);
                    $text = 'update';
                } else {
                    $result = $mainDb->query('INSERT INTO user_resource SET user_id=' . $_POST['userId'] . ', resource_id=' . $_POST['resourceId'] . ', count=' . $_POST['countAll']);
                    $text = 'insert';
                }

                if ($result) {
                    $json_data['message'] = '';
                    echo json_encode($json_data);
                } else {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's024';
                    $json_data['message'] = 'bad query:: ' . $text;
                    echo json_encode($json_data);
                }
            } catch (Exception $e) {
                $json_data['status'] = 's025';
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
} else {
    $json_data['id'] = 1;
    $json_data['status'] = 's026';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}