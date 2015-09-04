<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        if ($_POST['isAmbar'] == 1) {
            $result = $mainDb->update(
                'users',
                ['ambar_max' => $_POST['newMaxCount'], 'ambar_level' => $_POST['newLevel']],
                ['id' => $_POST['userId']],
                ['int', 'int'],
                ['int']);
        } else {
            $result = $mainDb->update(
                'users',
                ['sklad_max' => $_POST['newMaxCount'], 'sklad_level' => $_POST['newLevel']],
                ['id' => $_POST['userId']],
                ['int', 'int'],
                ['int']);
        }

        if (!$result) {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = '';
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
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
