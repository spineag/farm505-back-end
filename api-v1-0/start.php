<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['idSocial']) && !empty($_POST['idSocial'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK
    $memcache = $app->getMemcache();

    try {
        $socialUId = $_POST['idSocial'];
        //check is user available for game
//        $result = $mainDb->query("SELECT * FROM available_users WHERE social_id =".$socialUId);
//        $arr = $result->fetch();
//        if (!$arr['id']) {
//            $json_data['id'] = 3;
//            throw new Exception("U are not an available user");
//        }
        
        // create user if not exist
        $uid = $app->getUserId($channelId, $socialUId);
        if ($uid < 1) {
            $uid = $app->newUser($channelId, $socialUId, $_POST['name'], $_POST['lastName']);
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
        $memcache->set($uid, $sess, false, 300);
        
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
