<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userSocialId']) && !empty($_POST['userSocialId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    $result = $mainDb->query("SELECT * FROM users WHERE social_id =".$_POST['userSocialId']);
    $arr = $result->fetch();
    $userId = $arr['id'];

    try {
        $result = $mainDb->query("SELECT * FROM user_train_pack WHERE user_id =".$userId);
        $arr = $result->fetch();
        if (empty($arr)) {
            $count = 3;
            $result = $mainDb->insert('user_train_pack',
                ['user_id' => $_POST['userId'], 'count_xp' => rand(20, 70) * 5, 'count_money' => rand(20, 80) * 5],
                ['int', 'int', 'int']);
            $result = $mainDb->query("SELECT * FROM user_train_pack WHERE user_id =".$userId);
            if (!$result) {
                $json_data['status'] = 'error';
                $json_data['message'] = 3;
                echo json_encode($json_data);
            }
            $arr = $result->fetch();
            for ($i = 1; $i <= $count; $i++) {
                $resId = $app->getRandomResource($userId);
                $count_resource = rand(2, 10);
                $count_xp = rand(10, 50) * 5;
                $count_money = rand(10, 80) * 5;
                for ($k = 1; $k <= 3; $k++) {
                    $result = $mainDb->insert('user_train_pack_item',
                        ['user_id' => $userId, 'user_train_pack_id' => $arr['id'], 'resource_id' => $resId, 'count_resource' => $count_resource, 'count_xp' => $count_xp, 'count_money' => $count_money, 'is_full' => 0],
                        ['int', 'int', 'int', 'int', 'int', 'int', 'int']);
                }
            }
        }

        $pack = [];
        $pack['id'] = $arr['id'];
        $pack['count_xp'] = $arr['count_xp'];
        $pack['count_money'] = $arr['count_money'];
        $pack['items'] = [];
        $result = $mainDb->query("SELECT * FROM user_train_pack_item WHERE user_id =".$userId." AND user_train_pack_id=".$arr['id']);
        $arr = $result->fetchAll();
        if (!empty($arr)) {
            foreach ($arr as $key => $d) {
                $item = [];
                $item['id'] = $d['id'];
                $item['resource_id'] = $d['resource_id'];
                $item['count_xp'] = $d['count_xp'];
                $item['count_money'] = $d['count_money'];
                $item['count_resource'] = $d['count_resource'];
                $item['is_full'] = $d['is_full'];
                $pack['items'][] = $item;
            }
        }

        $json_data['message'] = $pack;
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 'error';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 'error';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
