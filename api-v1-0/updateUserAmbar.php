<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            if ($_POST['isAmbar'] == 1) {
                // $result = $mainDb->update(
                //     'users',
                //     ['ambar_max' => $_POST['newMaxCount'], 'ambar_level' => $_POST['newLevel']],
                //     ['id' => $_POST['userId']],
                //     ['int', 'int'],
                //     ['int']);
                $result = $mainDb->query('UPDATE users SET ambar_max='.$_POST['newMaxCount'].', ambar_level='.$_POST['newLevel'].' WHERE id='.$_POST['userId']);
            } else {
                // $result = $mainDb->update(
                //     'users',
                //     ['sklad_max' => $_POST['newMaxCount'], 'sklad_level' => $_POST['newLevel']],
                //     ['id' => $_POST['userId']],
                //     ['int', 'int'],
                //     ['int']);
                $result = $mainDb->query('UPDATE users SET sklad_max='.$_POST['newMaxCount'].', sklad_level='.$_POST['newLevel'].' WHERE id='.$_POST['userId']);
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
            $json_data['status'] = 's173';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
        }
    } else {
        $json_data['id'] = 13;
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's174';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
