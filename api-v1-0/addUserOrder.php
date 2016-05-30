<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
       $result = $mainDb->queryWithAnswerId('INSERT INTO user_order SET user_id='.$_POST['userId'].', ids="'.$_POST['ids'].'", counts="'.$_POST['counts'].'", xp='.$_POST['xp'].', coins='.$_POST['coins'].
       ', add_coupone='.$_POST['addCoupone'].', start_time='.(time()+(int)$_POST['delay']).', place='.$_POST['place']);    

        if ($result) {
            $json_data['message'] = $result[1];
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 'error';
            $json_data['message'] = 'bad query';
        }
        
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
