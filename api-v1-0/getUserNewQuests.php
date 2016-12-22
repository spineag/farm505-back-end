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
    $mainDb = $app->getMainDb($channelId);

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        $m = md5($_POST['userId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's...';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            try {
                $result = $shardDb->query("SELECT * FROM user_quest WHERE user_id =".$userId. " AND is_out_date = 0 AND get_award = 0");
                $unfinishedQuests = $result->fetchAll();
                foreach ($unfinishedQuests as $value => $dict) {
                    $unfinishedQuestsIDs[] = $dict['id'];
                }
                $result = $shardDb->query("SELECT * FROM user_quest WHERE user_id =".$userId. " AND is_out_date = 0 AND get_award = 1");
                $finishedQuests = $result->fetchAll();
                $finishedQuestsIDs = [];
                foreach ($finishedQuests as $value => $dict) {
                    $finishedQuestsIDs[] = $dict['id'];
                }
                $finishedQuestsIDs[] = '0';

                $time = time();
                $arF = implode(',', array_map('intval', $finishedQuestsIDs));
                $arUF = implode(',', array_map('intval', array_merge($unfinishedQuestsIDs, $finishedQuestsIDs)));
                $result =  $mainDb->query("SELECT * FROM quests WHERE level <= ".$_POST['level']." date_start < ".$time." AND date_finish > ".$time." AND  prev_id IN (".$arF.") AND id NOT IN (".$arUF.") AND id NOT IN (".$arF.")" );
                $quests = $result->fetchAll();

                $questsNew = [];
                $tasksNew = [];
                if (count($quests)) {
                    $ids = [];
                    foreach ($quests as $value => $dict) {
                        $ids[] = $dict['id'];
                        $result = $shardDb->queryWithAnswerId('INSERT INTO user_quest 
                            SET user_id='.$userId.', quest_id='.$dict['id'].', date_start='.$time.', date_finish=0, is_done=0, get_award=0, is_out_date=0');
                        $q = [];
                        $q['id'] = $result[1];
                        $q['quest_id'] = $dict['id'];
                        $q['is_done'] = 0;
                        $q['get_award'] = 0;
                        $q['quest_data'] = $dict;
                        $questsNew[] = $q;
                    }
                    $ar = implode(',', array_map('intval', $ids));
                    $result =  $mainDb->query("SELECT * FROM quest_task WHERE quest_id IN (".$ar.") ");
                    $tasks = $result->fetchAll();
                    foreach ($tasks as $value => $dict) {
                        $result = $shardDb->queryWithAnswerId('INSERT INTO user_quest_task 
                            SET user_id='.$userId.', quest_id='.$dict['quest_id'].', count_done=0, is_done=0');
                        $t = [];
                        $t['id'] = $result[1];
                        $t['quest_id'] = $dict['quest_id'];
                        $t['is_done'] = 0;
                        $t['count_done'] = 0;
                        $t['task_data'] = $dict;
                    }
                }

                $ar = [];
                $ar['quests'] = $questsNew;
                $ar['tasks'] = $tasksNew;
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