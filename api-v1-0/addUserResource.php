<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->query("SELECT count FROM user_resource WHERE user_id =".$_POST['userId']." AND resource_id=".$_POST['resourceId']);
        $arr = $result->fetch();
        if (count($arr) > 0) {
            $count = $arr['count'];
            $count = (int)$count + (int)$_POST['count'];
            $result = $mainDb->query('UPDATE user_resource SET count = '.$count.' WHERE user_id='.$_POST['userId'].' AND resource_id = '.$_POST['resourceId']);            
            $text = 'update';    
        } else {
            $result = $mainDb->query('INSERT INTO user_resource SET user_id='.$_POST['userId'].', resource_id='.$_POST['resourceId'].', count='.$_POST['count']);    
            $text = 'insert';    
        }

        if ($result) {
            $json_data['message'] = '';
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's024';
            $json_data['message'] = 'bad query:: '.$text;
            echo json_encode($json_data);
        } 
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's025';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's026';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}