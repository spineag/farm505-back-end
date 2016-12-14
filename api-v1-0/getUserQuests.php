<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $userId = filter_var($_POST['userId']);
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK
    $shardDb = $app->getShardDb($userId, $channelId);

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        $m = md5($_POST['userId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's...';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
//                $result = $shardDb->query("SELECT * FROM user_quests WHERE user_id =".$userId. " AND get_award = 1");
//                $doneQuests = $result->fetchAll();
                $result = $shardDb->query("SELECT * FROM user_quest WHERE user_id =".$userId. " AND get_award = 0");
                $quests = $result->fetchAll();
                $qIDs = [];
                foreach ($quests as $value => $dict) {
                    $qIDs[] = $dict['id'];
                }

                $result = $shardDb->query("SELECT * FROM user_ques_task WHERE id IN (" .implode(',', array_map('intval', $qIDs)). ")");
                $tasks = $result->fetchAll();

                $ar = [];
                $ar['quests'] = $quests;
                $ar['tasks'] = $tasks;
                $json_data['message'] = $ar;
                echo json_encode($json_data);

            } catch (Exception $e) {
                $json_data['status'] = 's...';
                $json_data['message'] = $e->getMessage();
                echo json_encode($json_data);
            }
        }
    } else {
        $json_data['id'] = 13;
        $json_data['status'] = 's...';
        $json_data['message'] = 'bad sessionKey';
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's...';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}