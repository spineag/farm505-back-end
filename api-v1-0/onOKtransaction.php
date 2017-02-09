<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    $channelId = 3; // only for OK for now

    $mainDb = $app->getMainDb($channelId);

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        try {
            if ($_POST['isPayed'] == '1') {
                $result = $mainDb->query('SELECT * FROM transactions_lost WHERE uid=' . $userId . ' AND product_code=' . $_POST['productCode']);
                $info = $result->fetch();
                if ($info) {
                    $res = 'FIND';
                    $result = $mainDb->query('DELETE FROM transactions_lost WHERE id=' . $info['id']);
                } else {
                    $res = 'NO_ROW'; // no row in BD
                }
            } else {
                $res = 'DELETED';
                $result = $mainDb->query('DELETE FROM transactions_lost WHERE uid='.$userId.' AND product_code='.$_POST['productCode'].' LIMIT 1');
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
        $json_data['id'] = 13;
        $json_data['status'] = 's...';
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
} else {
    $json_data['id'] = 1;
    $json_data['status'] = 's...';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}