<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['idSocial']) && !empty($_POST['idSocial'])) {
    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK
    $memcache = $app->getMemcache();
    $mainDb = $app->getMainDb($channelId);

    try {
        $socialUId = $_POST['idSocial'];
        $uid = $app->getUserId($channelId, $socialUId);
        if ($uid < 1) {
            try {
                $uid = $app->newUser($channelId, $socialUId, $_POST['name'], $_POST['lastName'], $_POST['sex'], $_POST['bornDate']);
            } catch (Exception $e) {
                $json_data['id'] = 2;
                $json_data['status'] = '00000';
                $json_data['message'] = $e->getMessage(); 
                echo json_encode($json_data);
            }
            if ($uid < 0) {
                $json_data['id'] = 2;
                $json_data['status'] = 's328';
                throw new Exception("Bad request to DB!");
            }
        }
        if (isset($_POST['sessionKey']) && !empty($_POST['sessionKey'])) {
            $sess = $_POST['sessionKey'];
            if ($sess == '') $sess = '0';
        } else {
            $sess = '0';
        }
        $result = $mainDb->query('UPDATE users SET session_key='.$sess.' WHERE id='.$uid);
        if (!$result) {
            $json_data['status'] = 's221';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
        }
        $memcache->set((string)$uid, (string)$sess, MEMCACHED_DICT_TIME);
        
        $json_data['message'] = $uid;
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's165';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's166';
    $json_data['message'] = 'bad POST[idSocial]';
    echo json_encode($json_data);
}
