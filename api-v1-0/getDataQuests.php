<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

        try {
            $guests = [];
            $result = $mainDb->query("SELECT * FROM quest");
            if ($result) {
                $arr = $result->fetchAll();
                foreach ($arr as $value => $dict) {
                    $res = [];
                    $res['id'] = $dict['id'];
                    $res['description'] = $dict['description'];
                    $res['quest_icon'] = $dict['quest_icon'];
                    $res['level'] = $dict['level'];
                    $res['prev_quest_id'] = $dict['prev_quest_id'];
                    $quests[] = $res;
                }
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's427';
                throw new Exception("Bad request to DB!");
            }

            $tasks = [];
            $result = $mainDb->query("SELECT * FROM quest_task");
            if ($result) {
                $arr = $result->fetchAll();
                foreach ($arr as $value => $dict) {
                    $res = [];
                    $res['id'] = $dict['id'];
                    $res['quest_id'] = $dict['quest_id'];
                    $res['description'] = $dict['description'];
                    $res['type_action'] = $dict['type_action'];
                    $res['count_action'] = $dict['count_action'];
                    $res['additional'] = $dict['additional'];
                    $tasks[] = $res;
                }
            } else {
                $json_data['id'] = 2;
                $json_data['status'] = 's428';
                throw new Exception("Bad request to DB!");
            }

            $res = [];
            $res[] = $quest;
            $res[] = $tasks;
            $json_data['message'] = $res;
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's106';
            $json_data['message'] = $e->getMessage();
            echo json_encode($json_data);
        }

}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's107';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
