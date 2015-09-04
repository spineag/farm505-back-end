<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->query("SELECT count FROM user_resource WHERE user_id =".$_POST['userId']." AND resource_id=".$_POST['resourceId']);
        $arr = $result->fetch();
        if (count($arr) > 0) {
            // $arr = $result->fetch();
            $count = $arr['count'];
            $count = (int)$count + (int)$_POST['count'];
            $result = $mainDb->update(
                'user_resource',
                ['count' => $count],
                ['user_id' => $_POST['userId'], 'resource_id' => $_POST['resourceId']],
                ['int'],
                ['int', 'int']);
            $text = 'update';
        } else {
            $result = $mainDb->insert('user_resource',
                ['user_id' => $_POST['userId'], 'resource_id' => $_POST['resourceId'], 'count' => $_POST['count']],
                ['int', 'int', 'int']);
            $text = 'insert';
        }

        if ($result) {
            $json_data['message'] = $text." ".count($arr);
            echo json_encode($json_data);
        } else {
            $json_data['id'] = 2;
            $json_data['status'] = 'error';
            $json_data['message'] = 'bad query';
            echo json_encode($json_data);
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