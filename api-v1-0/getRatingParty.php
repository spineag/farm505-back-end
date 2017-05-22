<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 7/15/15
 * Time: 11:57 AM
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
if (isset($_POST['channelId'])) {
    $channelId = (int)$_POST['channelId'];
} else $channelId = 2; // VK
$shardDb = $app->getAllShardsDb($channelId);
//$memcache = $app->getMemcache();
$mainDb = $app->getMainDb($channelId);

try {
    foreach ($shardDb as $key => $shard) {
        $result = $shard->query("SELECT * FROM user_party");
    }
        if ($result) {
            $partyALL = $result->fetchAll();
        } else {
            $json_data['id'] = 1;
            $json_data['status'] = 's291';
            throw new Exception("Bad request to DB!");
        }
        $countYour = 1;
    uasort($partyALL, 'cmp');
    if (!empty($partyALL)) {
            foreach ($partyALL as $key => $party) {
                if((string)$party['user_id'] == (string)$_POST['userId']) break;
                $countYour ++;
            }
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's292';
            throw new Exception("Bad request to DB!");
    }
        array_splice($partyALL, 20);
    $resp = [];
    foreach ($partyALL as $key => $party) {
        $result2 = $mainDb->query('SELECT social_id FROM users WHERE id =' . $party['user_id']);
        $partyTWO = $result2->fetch();
        $res = [];
        $res['id'] = $party['id'];
        $res['user_id'] = $party['user_id'];
        $res['show_window'] = $party['show_window'];
        $res['took_gift'] = $party['took_gift'];
        $res['count_resource'] = $party['count_resource'];
        $res['social_id'] = $partyTWO['social_id'];
        $resp[] = $res;
    }
        array_push($resp, $countYour);

    $json_data['message'] = $resp;
    echo json_encode($json_data);
}
catch (Exception $e)
{
    $json_data['status'] = 's080';
    $json_data['message'] = $e;
    echo json_encode($json_data);
}

function cmp($a, $b) {
    if ((int)$a['count_resource'] > (int)$b['count_resource']) {
        return -1;
    }
    return 1;
}