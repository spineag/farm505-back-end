<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            if ((int)$_POST['buildingId'] == 47 || (int)$_POST['buildingId'] == 49) {
                //  $result = $mainDb->update(
                // 'user_building_open',
                // ['is_open' => 1],
                // ['user_id' => $_POST['userId'], 'building_id' => $_POST['buildingId']],
                // ['int'],
                // ['int', 'int']);
                $result = $mainDb->query('UPDATE user_building_open SET is_open=1 WHERE building_id='.$_POST['buildingId'].' AND user_id = '.$_POST['userId']);
            } else {
                // $result = $mainDb->update(
                // 'user_building_open',
                // ['is_open' => 1],
                // ['user_id' => $_POST['userId'], 'building_id' => $_POST['buildingId'], 'user_db_building_id' => $_POST['dbId']],
                // ['int'],
                // ['int', 'int', 'int']);
                $result = $mainDb->query('UPDATE user_building_open SET is_open=1 WHERE building_id='.$_POST['buildingId'].' AND user_id = '.$_POST['userId'].' AND user_db_building_id='.$_POST['dbId']);
            }

            if ($result) {
                $json_data['message'] = '';
                echo json_encode($json_data);
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's065';
                $json_data['message'] = 'bad query';
            }

        }
        catch (Exception $e)
        {
            $json_data['status'] = 's066';
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
    $json_data['status'] = 's067';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}