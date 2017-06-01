<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$channelId = (int)$_POST['channelId'];
$memcache = $app->getMemcache();
$mainDb = $app->getMainDb($channelId);

try {
    $res = $memcache->get('getDataViralInvite'.$channelId);
    if (!$res) {
        $result = $mainDb->query("SELECT * FROM data_invite_viral");
        if ($result) {
            $res = $result->fetch();
            $memcache->set('getDataViralInvite'.$channelId, $res, MEMCACHED_DICT_TIME);
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's...';
            throw new Exception("Bad request to DB!");
        }
    }

    $json_data['message'] = $res;
    echo json_encode($json_data);
}
catch (Exception $e)
{
    $json_data['status'] = 's...';
    $json_data['message'] = $e->getMessage();
    echo json_encode($json_data);
}
