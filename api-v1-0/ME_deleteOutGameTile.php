<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        // $result = $mainDb->delete('data_outgame_tile',
        //     ['pos_x' => $_POST['posX'], 'pos_y' => $_POST['posY']],
        //     ['int',  'int']);
        $result = $mainDb->query('DELETE FROM data_outgame_tile WHERE pos_x='.$_POST['posX'].' AND pos_y='.$_POST['posY']);
        if ($result) {
            $json_data['message'] = '';
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }
    }

    catch (Exception $e)
    {
        $json_data['status'] = 's123';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's124';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}