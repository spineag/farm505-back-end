<?php


include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        $m = md5($_POST['userId'].$_POST['numberCell'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's392';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            $mainDb = $app->getMainDb($channelId);
            $userId = filter_var($_POST['userId']);
            $shardDb = $app->getShardDb($userId, $channelId);
            try {
                $time = time();
                $result = $shardDb->query('UPDATE user_market_item SET in_papper=' . $_POST['inPapper'] . ', time_in_papper =' . $time . ' WHERE number_cell=' . $_POST['numberCell'] . ' AND user_id = ' . $userId);
                if ($channelId == 2) {
                    $result = $mainDb->query('UPDATE users SET in_papper=' . $time . ' WHERE id=' . $_POST['userId']);
                } else { // == 3 || == 4
                    $result = $shardDb->query('UPDATE user_info SET in_papper=' . $time . ' WHERE user_id=' . $_POST['userId']);
                }
                if (!$result) {
                    $json_data['id'] = 2;
                    $json_data['status'] = 's329';
                    throw new Exception("Bad request to DB!");
                }

                $json_data['message'] = '';
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's170';
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
    $json_data['status'] = 's171';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}