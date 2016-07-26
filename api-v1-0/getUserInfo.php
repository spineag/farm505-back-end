<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if ($app->checkSessionKey($_POST['userId'], $_POST['sessionKey'])) {
        try {
            $result = $mainDb->query("SELECT * FROM users WHERE id =".$_POST['userId']);
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
            $user['xp'] = $u['xp'];
            $user['daily_bonus_day'] = date("d", $u['daily_bonus_day']);
            $user['count_daily_bonus'] = $u['count_daily_bonus'];
            $user['is_tester'] = $u['is_tester'];
            $user['count_cats'] = $u['count_cats'];
            $user['scale'] = $u['scale'];
            $user['music'] = $u['musics'];
            $user['sound'] = $u['sounds'];
            $user['time_paper'] = $u['time_paper'];
            $user['tutorial_step'] = $u['tutorial_step'];
            $user['market_cell'] = $u['market_cell'];
            $user['in_papper'] = $u['in_papper'];
            $user['chest_day'] = date("d", $u['chest_day']);
            $user['count_chest'] = $u['count_chest'];
            $user['cut_scene'] = $u['cut_scene'];
            $user['notification_new'] = $u['notification_new'];
            $user['wall_order_item_time'] = date("d", $u['wall_order_item_time']);
            $user['wall_train_item'] = date("d", $u['wall_train_item']);

            $json_data['message'] = $user;
            echo json_encode($json_data);
        }
        catch (Exception $e)
        {
            $json_data['status'] = 's092';
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
    $json_data['status'] = 's093';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}
