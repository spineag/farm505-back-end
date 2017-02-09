<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userSocialId = filter_var($_POST['userId']);
    $channelId = 3; // only for OK for now

    $mainDb = $app->getMainDb($channelId);
    try {
        if ($_POST['isPayed'] == '0') {
            $result = $mainDb->query('SELECT * FROM transaction_lost WHERE uid=' . $userSocialId . ' AND product_code=' . $_POST['productCode']);
            if ($result) {
                $info = $result->fetch();
                if ($info) {
                    $res = 'FIND';
                    $result = $mainDb->query('DELETE FROM transaction_lost WHERE id=' . $info['id']);
                } else {
                    $res = 'NO_ROW'; // no row in BD
                }
            } else {
                $json_data['id'] = 3;
                $json_data['status'] = 's...';
                throw new Exception("Bad request to DB!");
            }
        } else {
            $res = 'DELETED';
            $result = $mainDb->query('DELETE FROM transaction_lost WHERE uid='.$userSocialId.' AND product_code='.$_POST['productCode'].' LIMIT 1');
        }

        if ($result) {
            $json_data['message'] = $res;
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's...';
            throw new Exception("Bad request to DB!");
        }
    } catch (Exception $e){
        $json_data['status'] = 's...';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
} else {
    $json_data['id'] = 1;
    $json_data['status'] = 's...';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}