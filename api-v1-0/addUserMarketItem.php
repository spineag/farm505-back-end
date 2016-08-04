<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $m = md5($_POST['userId'].$_POST['id'].$_POST['count'].$_POST['cost'].$_POST['numberCell'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's356';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $time = time();
                $result = $mainDb->queryWithAnswerId('INSERT INTO user_market_item SET user_id=' . $_POST['userId'] . ', buyer_id=0, resource_id=' . $_POST['resourceId'] . ', time_start=' . $time . ', time_sold=0, cost=' . $_POST['cost'] . ', resource_count=' . $_POST['count'] . ', in_papper=' . $_POST['inPapper'] . ', number_cell=' . $_POST['numberCell'] . ', time_in_papper=' . $_POST['timeInPapper']);
                if ($result) {
                    $res = [];
                    $res['id'] = $result[1];
                    $res['buyer_id'] = 0;
                    $res['time_start'] = $time;
                    $res['time_sold'] = 0;
                    $res['cost'] = $_POST['cost'];
                    $res['resource_id'] = $_POST['resourceId'];
                    $res['resource_count'] = $_POST['count'];
                    $res['in_papper'] = $_POST['inPapper'];
                    $res['number_cell'] = $_POST['numberCell'];
                    $res['time_in_papper'] = $_POST['timeInPapper'];
                } else {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's233';
                    throw new Exception("Bad request to DB!");
                }

                $json_data['message'] = $res;
                echo json_encode($json_data);

            } catch (Exception $e) {
                $json_data['status'] = 's014';
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
    $json_data['status'] = 's015';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}