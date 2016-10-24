<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) {
    $app = Application::getInstance();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        $mainDb = $app->getMainDb();
        $userId = filter_var($_POST['userId']);
        $shardDb = $app->getShardDb($userId);
        try {
            $resp = [];
            $result = $mainDb->query("SELECT * FROM users WHERE social_id =".$_POST['userSocialId']);
            $arr = $result->fetch();
            $responce['market_cell'] = $arr['market_cell'];
            $id = $arr['id'];

            $result = $shardDb->query("SELECT * FROM user_market_item WHERE user_id =".$id);
            if ($result) {
                $arr = $result->fetchAll();
                foreach ($arr as $value => $dict) {
                    $res = [];
                    $res['id'] = $dict['id'];
                    $res['buyer_id'] = $dict['buyer_id'];
                    $res['time_start'] = $dict['time_start'];
                    $res['time_sold'] = $dict['time_sold'];
                    $res['cost'] = $dict['cost'];
                    $res['resource_id'] = $dict['resource_id'];
                    $res['resource_count'] = $dict['resource_count'];
                    $res['number_cell'] = $dict['number_cell'];
                    $res['time_in_papper'] = $dict['time_in_papper'];
                    $res['level'] = $dict['level'];
                    if ((int)$dict['in_papper'] == 1) {
                        if (time() > (int)$dict['time_in_papper'] + 5*60*60) {
                            $res['in_papper'] = 0;
                            $res['time_in_papper'] = 0;
                            $resultUpdate = $shardDb->query('UPDATE user_market_item SET in_papper=0 AND time_in_papper = 0 WHERE id='.$res['id']);
                        } else {
                            $res['in_papper'] = 1;
                        }
                    } else {
                        $res['in_papper'] = 0;
                    }

                    if ($dict['buyer_id'] > 0) {
                        $result2 = $mainDb->query("SELECT * FROM users WHERE id =".$dict['buyer_id']);
                        $arr = $result2->fetch();
                        $res['buyer_social_id'] = $arr['social_id'];
                    } else {
                        if ($id == $_POST['userId']) {
                            $result2 = $mainDb->query("SELECT * FROM users WHERE social_id = 1");
                            $arr = $result2->fetch();
                            if (time() - $dict['time_start'] > 24*60*60) {
                                $result = $mainDb->update(
                                    'user_market_item',
                                    ['buyer_id' => $arr['id'], 'time_sold' => time(), 'in_papper' => 0],
                                    ['id' => $dict['id']],
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
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's303';
                throw new Exception("Bad request to DB!");
            }

            $responce['items'] = $resp;
            $json_data['message'] = $responce;
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
