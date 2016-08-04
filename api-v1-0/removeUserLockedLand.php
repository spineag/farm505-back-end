<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $m = md5($_POST['userId'].$_POST['mapBuildingId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's382';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $result = $mainDb->query("SELECT unlocked_land FROM users WHERE id =" . $_POST['userId']);
                $u = $result->fetchAll();
                $u = $u[0]['unlocked_land'];
                $u = $u . "&" . $_POST['mapBuildingId'];
                $result = $mainDb->query('UPDATE users SET unlocked_land="' . $u . '" WHERE id=' . $_POST['userId']);
                if (!$result) {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's318';
                    throw new Exception("Bad request to DB!");
                }

                $json_data['message'] = $u;
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's147';
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
    $json_data['status'] = 's148';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
