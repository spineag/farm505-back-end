<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->query("SELECT count_cats FROM users WHERE id =".$_POST['userId']);
        if ($result) {
            $arr = $result->fetch();
            $count = $arr['count_cats'];
            $count = (int)$count + 1;
            $result = $mainDb->query('UPDATE users SET count_cats = '.$count.' WHERE id = '.$_POST['userId']);
            if (!$result) {
                $json_data['id'] = 2;
                throw new Exception("Bad request to DB!");
            }
        } else {
            $json_data['id'] = 1;
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
