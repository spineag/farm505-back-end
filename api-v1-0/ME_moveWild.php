<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        // $result = $mainDb->update(
        //     'data_map_wild',
        //     ['pos_x' => $_POST['posX'], 'pos_y' => $_POST['posY']],
        //     ['id' => $_POST['dbId']],
        //     ['int', 'int'],
        //     ['int']);
        $result = $mainDb->query('UPDATE data_map_wild SET pos_x='.$_POST['posX'].', pos_y='.$_POST['posY'].' WHERE id='.$_POST['dbId']);
        if ($result) {
            $json_data['message'] = '';
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 's131';
            $json_data['message'] = 'bad query';
        }

        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's132';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's133';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}