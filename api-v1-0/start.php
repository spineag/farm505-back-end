<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['idSocial']) && !empty($_POST['idSocial'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $socialUId = $_POST['idSocial'];
        //check is user available for game
        $result = $mainDb->query("SELECT * FROM available_users WHERE social_id =".$socialUId);
        $arr = $result->fetch();
        if (!$arr['id']) {
            $json_data['id'] = 3;
            throw new Exception("U are not an available user");
        }
        
        // create user if not exist
        $uid = $app->getUserId($channelId, $socialUId);
        if ($uid < 1) {
            $uid = $app->newUser($channelId, $socialUId, $_POST['name'], $_POST['lastName']);
            if ($uid < 0) {
                $json_data['id'] = 2;
                throw new Exception("Bad request to DB!");
            }
        }

        $json_data['message'] = $uid;
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
    $json_data['message'] = 'bad POST[idSocial]';
    echo json_encode($json_data);
}
