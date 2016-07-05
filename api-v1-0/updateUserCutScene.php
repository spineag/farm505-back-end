<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        // $result = $mainDb->update(
        //         'users',
        //         ['cut_scene' => $_POST['cutScene']],
        //         ['id' => $_POST['userId']],
        //         ['int'],
        //         ['int']);
    
        $result = $mainDb->query('UPDATE users SET cut_scene="'.$_POST['cutScene'].'" WHERE id='.$_POST['userId']);
        if (!$result) {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = $arr;
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's178';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's179';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}