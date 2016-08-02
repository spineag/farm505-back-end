<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            // $result = $mainDb->delete('user_building',
            //     ['user_id' => $_POST['userId'], 'id' => $_POST['dbId']],
            //     ['int', 'int']);
            $result = $mainDb->query('DELETE FROM user_building WHERE user_id='.$_POST['userId'].' AND id = '.$_POST['dbId']);
             if ($result) {
                $json_data['message'] = '';
                echo json_encode($json_data);
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's144';
                $json_data['message'] = 'bad query';
            }
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's145';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
        }
    } else {
        $json_data['id'] = 13;
        $json_data['status'] = 's221';
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's146';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
