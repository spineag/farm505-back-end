<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    if (isset($_POST['channelId'])) {
        $channelId = (int)$_POST['channelId'];
    } else $channelId = 2; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'], $channelId)) {
        $m = md5($_POST['userId'].$app->md5Secret());
        if ($m != $_POST['hash']) {
            $json_data['id'] = 6;
            $json_data['status'] = 's413';
            $json_data['message'] = 'wrong hash';
            echo json_encode($json_data);
        } else {
            $mainDb = $app->getMainDb($channelId);
            $userId = filter_var($_POST['userId']);
            $shardDb = $app->getShardDb($userId, $channelId);
            try {
                $result = $mainDb->query("SELECT * FROM users WHERE id =" . $_POST['userId']);
                $u = $result->fetch();
                $user = [];
                $user['ambar_max'] = $u['ambar_max'];
                $user['sklad_max'] = $u['sklad_max'];
                $user['ambar_level'] = $u['ambar_level'];
                $user['sklad_level'] = $u['sklad_level'];
                $user['hard_count'] = $u['hard_count'];
                $user['soft_count'] = $u['soft_count'];
                $user['yellow_count'] = $u['yellow_count'];
                $user['green_count'] = $u['green_count'];
                $user['red_count'] = $u['red_count'];
                $user['blue_count'] = $u['blue_count'];
                $user['level'] = $u['level'];
                $user['xp'] = $u['xp'];
                $user['is_tester'] = $u['is_tester'];
                $user['count_cats'] = $u['count_cats'];
                $user['scale'] = $u['scale'];
                $user['music'] = $u['musics'];
                $user['sound'] = $u['sounds'];
                $user['tutorial_step'] = $u['tutorial_step'];
                $user['market_cell'] = $u['market_cell'];
                $user['day_daily_gift'] = $u['day_daily_gift'];
                $user['count_daily_gift'] = $u['count_daily_gift'];
                $user['starter_pack'] = $u['starter_pack'];
                $user['day'] = time();
                if ($channelId == 2) {
                    $user['cut_scene'] = $u['cut_scene'];
                    $user['mini_scene'] = $u['mini_scene'];
                    $user['daily_bonus_day'] = gmdate("d", $u['daily_bonus_day']);
                    $user['count_daily_bonus'] = $u['count_daily_bonus'];
                    if ($u['mouse_day'] == '0') {
                        $user['mouse_day'] = 0;
                    } else {
                        $user['mouse_day'] = gmdate("d", $u['mouse_day']);
                    }
                    $user['mouse_count'] = $u['mouse_count'];
                    $user['time_paper'] = $u['time_paper'];
                    $user['in_papper'] = $u['in_papper'];
                    $user['chest_day'] = gmdate("d", $u['chest_day']);
                    $user['count_chest'] = $u['count_chest'];
                    $user['notification_new'] = $u['notification_new'];
                    $user['wall_order_item_time'] = gmdate("d", $u['wall_order_item_time']);
                    $user['wall_train_item'] = gmdate("d", $u['wall_train_item']);
                    $user['open_order'] = $u['open_order'];
                } else {
                    $result = $shardDb->query("SELECT * FROM user_info WHERE user_id =" . $_POST['userId']);
                    $uS = $result->fetch();
                    $user['cut_scene'] = $uS['cutscene'];
                    $user['mini_scene'] = $uS['miniscene'];
                    $user['daily_bonus_day'] = gmdate("d", $uS['daily_bonus_day']);
                    $user['count_daily_bonus'] = $uS['count_daily_bonus'];
                    if ($uS['mouse_day'] == '0') {
                        $user['mouse_day'] = 0;
                    } else {
                        $user['mouse_day'] = gmdate("d", $uS['mouse_day']);
                    }
                    $user['mouse_count'] = $uS['mouse_count'];
                    $user['time_paper'] = $uS['time_paper'];
                    $user['in_papper'] = $uS['in_papper'];
                    $user['chest_day'] = gmdate("d", $uS['chest_day']);
                    $user['count_chest'] = $uS['count_chest'];
                    $user['notification_new'] = $uS['notification_new'];
                    $user['wall_order_item_time'] = gmdate("d", $uS['wall_order_item_time']);
                    $user['wall_train_item'] = gmdate("d", $uS['wall_train_item']);
                    $user['open_order'] = $u['open_order'];
                }

                $check = (int)$user['ambar_max'] + (int)$user['sklad_max'] + (int)$user['ambar_level'] + (int)$user['sklad_level'] + (int)$user['hard_count'] + (int)$user['soft_count'] +
                    (int)$user['yellow_count'] + (int)$user['green_count'] + (int)$user['red_count'] + (int)$user['blue_count'] + (int)$user['level'] + (int)$user['xp'] + (int)$user['count_cats'] +
                    (int)$user['tutorial_step'] + (int)$user['count_chest'] + (int)$user['count_daily_bonus'];
                $user['test_date'] = $check;

//                $quests = [];
//                $result = $shardDb->query("SELECT * FROM user_quests_temp WHERE user_id = ".$_POST['userId']);
//                if ($result) {
//                    $arr = $result->fetchAll();
//                    foreach ($arr as $value => $dict) {
//                        $res = [];
//                        $res['quest_id'] = $dict['quest_id'];
//                        $res['is_done'] = $dict['is_done'];
//                        $res['get_award'] = $dict['get_award'];
//                        $quests[] = $res;
//                    }
//                    $user['quests'] = $quests;
//                }

                $json_data['message'] = $user;
                echo json_encode($json_data);
            } catch (Exception $e) {
                $json_data['status'] = 's092';
                $json_data['message'] = $e->getMessage();
                echo json_encode($json_data);
            }
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
    $json_data['status'] = 's093';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
