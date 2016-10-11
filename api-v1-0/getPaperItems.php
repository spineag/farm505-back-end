<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $resp = [];
            $result = $mainDb->query("SELECT * FROM user_market_item WHERE in_papper = 1 AND buyer_id = 0 AND user_id <> ".$_POST['userId']." ORDER BY RAND() LIMIT 60");
            if ($result) {
                $arr = $result->fetchAll();
                foreach ($arr as $value => $dict) {
                    $res = [];
                    $res['id'] = $dict['id'];
                    $res['user_id'] = $dict['user_id'];
                    $res['cost'] = $dict['cost'];
                    $res['resource_id'] = $dict['resource_id'];
                    $res['resource_count'] = $dict['resource_count'];

                $result2 = $mainDb->query("SELECT * FROM users WHERE id =".$dict['user_id']);
                $arr = $result2->fetch();
                $res['user_social_id'] = $arr['social_id'];
                $res['level'] = $arr['level'];
                $res['need_help'] = $app->checkNeedHelp($dict['user_id']);

                    $resp[] = $res;
                }
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's297';
                throw new Exception("Bad request to DB!");
            }

            $json_data['message'] = $resp;
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's084';
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
    $json_data['status'] = 's085';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
