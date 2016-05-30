<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        if ($_POST['buildId'] == 12) {
            // $result = $mainDb->update('const',
            //     ['value' => $_POST['posX']],
            //     ['name' => 'AMBAR_POS_X'],
            //     ['int'],
            //     ['int']);
                $result = $mainDb->query('UPDATE const SET value = '.$_POST['posX'].' WHERE name=AMBAR_POS_X');
            // $result = $mainDb->update('const',
            //     ['value' => $_POST['posY']],
            //     ['name' => 'AMBAR_POS_Y'],
            //     ['int'],
            //     ['int']);
                $result = $mainDb->query('UPDATE const SET value='.$_POST['posY'].' WHERE name='.$_POST['AMBAR_POS_Y']);
        } else if ($_POST['buildId'] == 13) {
            // $result = $mainDb->update('const',
            //     ['value' => $_POST['posX']],
            //     ['name' => 'SKLAD_POS_X'],
            //     ['int'],
            //     ['int']);
            $result = $mainDb->query('UPDATE const SET value = '.$_POST['posX'].' WHERE name=SKLAD_POS_X');
            // $result = $mainDb->update('const',
            //     ['value' => $_POST['posY']],
            //     ['name' => 'SKLAD_POS_Y'],
            //     ['int'],
            //     ['int']);
            $result = $mainDb->query('UPDATE const SET value = '.$_POST['posY'].' WHERE name=SKLAD_POS_Y');
            $result = $mainDb->query('UPDATE const SET value = '.$_POST['posY'].' WHERE name=SKLAD_POS_Y');
        } else {
            // $result = $mainDb->update('map_building',
            //     ['pos_x' => $_POST['posX'], 'pos_y' => $_POST['posY']],
            //     ['building_id' => $_POST['buildId']],
            //     ['int', 'int'],
            //     ['int']);
            $result = $mainDb->query('UPDATE map_building SET pos_x='.$_POST['posX'].', pos_y='.$_POST['posY'].' WHERE building_id='.$_POST['buildingId']);
        }

        if ($result) {
            $json_data['message'] = '';
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 'error';
            $json_data['message'] = 'bad query';
        }

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