<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $channelId = (int)$_POST['channelId'];

    $resp = [];
    $userId = filter_var($_POST['userId']);
    $shardDb = $app->getShardDb($userId, $channelId);
    $day24 = time() - 60*60*24;
    $result = $shardDb->query('DELETE FROM user_ask_gift WHERE time_ask <'.$day24.' AND is_send = 2');
    $result = $shardDb->query('SELECT * FROM user_ask_gift WHERE user_id = '.$userId);
    $ar = $result->fetchAll();
    $asks = [];
    if ($ar) {
        foreach ($ar as $value => $u) {
            $qw = [];
            $qw['id'] = $u['id'];
            $qw['user_id'] = $u['user_id'];
            $qw['user2_id'] = $u['user2_id'];
            $qw['resource_id'] = $u['resource_id'];
            $qw['time_ask'] = $u['time_ask'];
            $qw['is_send'] = $u['is_send'];
            $asks[] = $qw;
        }
    }
    $resp['ask'] = $asks;

    $shards = $app->getAllShardsDb($channelId);
    $sends = [];
    foreach ($shards as $value => $shard) {
        try {
            $result = $shard->query('SELECT * FROM user_ask_gift WHERE user2_id = ' . $userId);
            $ar = $result->fetchAll();
            if ($ar) {
                foreach ($ar as $value => $u) {
                    $qw = [];
                    $qw['id'] = $u['id'];
                    $qw['user_id'] = $u['user_id'];
                    $qw['user2_id'] = $u['user2_id'];
                    $qw['resource_id'] = $u['resource_id'];
                    $qw['time_ask'] = $u['time_ask'];
                    $qw['is_send'] = $u['is_send'];
                    $sends[] = $qw;
                }
            }
        } catch (Exception $e) {}
    }
    $resp['send'] = $sends;

    $json_data['message'] = $resp;
    echo json_encode($json_data);

} else {
    $json_data['id'] = 1;
    $json_data['status'] = 's091';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}