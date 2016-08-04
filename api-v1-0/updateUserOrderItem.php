<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    $m = md5($_POST['userId'].$_POST['id'].$_POST['place'].$app->md5Secret());
    if ($m != $_POST['hash']) {
        $json_data['id'] = 6;
        $json_data['status'] = 's401';
        $json_data['message'] = 'wrong hash';
        echo json_encode($json_data);
    } else {
        try {
            $result = $mainDb->query('UPDATE user_order SET place= ' . $_POST['place'] . ' WHERE id=' . $_POST['id']);
            if (!$result) {
                $json_data['id'] = 2;
                $json_data['status'] = 's338';
                throw new Exception("Bad request to DB!");
            }

            $json_data['message'] = '';
            echo json_encode($json_data);
        } catch (Exception $e) {
            $json_data['status'] = 's209';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
        }
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's210';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
