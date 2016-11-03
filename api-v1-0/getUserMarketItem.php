<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

const TIME_GAP = 5 * 60 * 60;
if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) {
    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        $mainDb = $app->getMainDb($channelId);
        $userId = filter_var($_POST['userId']);
        $shardDb = $app->getShardDb($userId, $channelId);
        try {
            $resp = [];
            $result = $mainDb->query("SELECT market_cell, id FROM users WHERE social_id =".$_POST['userSocialId']);
            $arr = $result->fetch();
            $response['market_cell'] = $arr['market_cell'];
            $id = $arr['id'];
            $time = time() - TIME_GAP;
            $shardDb->query("UPDATE user_market_item SET in_papper=0 AND time_in_papper = 0 WHERE user_id = ". $id . " 
            AND in_papper = 1 AND time_in_papper < " . $time);

            $result = $shardDb->query("SELECT * FROM user_market_item WHERE user_id =" . $id);
            while ($res =  $result->fetch()) {
                if ($res['buyer_id'] > 0) {
                    $result2 = $mainDb->query("SELECT * FROM users WHERE id =".$res['buyer_id']);
                    $arr = $result2->fetch();
                    $res['buyer_social_id'] = $arr['social_id'];
                } else {
                    if ($id == $_POST['userId']) {
                        $result2 = $mainDb->query("SELECT * FROM users WHERE social_id = 1");
                        $arr = $result2->fetch();
                        if (time() - $res['time_start'] > 24*60*60) {
                            $result = $shardDb->update(
                                'user_market_item',
                                ['buyer_id' => $arr['id'], 'time_sold' => time(), 'in_papper' => 0],
                                ['id' => $res['id']],
                                ['int', 'int','int'],
                                ['int']);
                            $res['buyer_social_id'] = 1;
                            $res['buyer_id'] = $arr['id'];
                            $res['time_sold'] = time();
                            $res['in_papper'] = 0;
                        }
                    }
                }
                $resp[] = $res;
            }

            $response['items'] = $resp;
            $json_data['message'] = $response;
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's094';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
        }
    } else {
        $result = $mainDb->query('UPDATE users SET test='.$_POST['sessionKey'].' WHERE id='.$_POST['userId']);
        $json_data['id'] = 13;
        $json_data['status'] = 's221';
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's095';
    $json_data['message'] = 'bad POST[userSocialId]';
    echo json_encode($json_data);
}
