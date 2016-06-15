<?php


include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

     try {
        $time = time();
        // $result = $mainDb->update(
        //     'user_market_item',
        //     ['in_papper' =>  $_POST['inPapper']],
        //     ['user_id' => $_POST['userId'], 'number_cell' => $_POST['numberCell']],
        //     ['int'],
        //     ['int', 'int']);
        $result = $mainDb->query('UPDATE user_market_item SET in_papper='.$_POST['inPapper'].', time_in_papper ='.$time.' WHERE number_cell='.$_POST['numberCell'].' AND user_id = '.$_POST['userId']);                
        //  $result = $mainDb->update(
        //     'users',
        //     ['in_papper' =>  time()],
        //     ['id' => $_POST['userId']],
        //     ['int'],
        //     ['int']);
        $result = $mainDb->query('UPDATE users SET in_papper='.time().' WHERE id='.$_POST['userId']);            
        if (!$result) {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = '';
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's170';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's171';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}