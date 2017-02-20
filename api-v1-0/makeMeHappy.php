<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_GET['id']) && !empty($_GET['id'])) {  // id = social_id
    if (isset($_GET['channel']) && !empty($_GET['channel'])) {
        $app = Application::getInstance();
        $channelId = (int)$_GET['channel'];
        try {
            $mainDb = $app->getMainDb($channelId);
            $shardDb = $app->getShardDb($_GET['id'], $channelId);
        } catch (Exception $e) {
            echo 'popandosik (';
        }

        if (isset($_GET['a']) && $_GET['a'] == 'a') {
            try {
                $id = $app->getUserId($channelId, $_GET['id']);
            } catch (Exception $e) {
                echo 'halepa, valera (';
            }

            try {
                if ($id >0) {
                    if ($_POST['channelId'] == '3') $shardDb->query('DELETE FROM user_info WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_animal WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_cave WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_plant_ridge WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_recipe_fabrica WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_building WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_building_open WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_tree WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_resource WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_train_pack_item WHERE user_id=' . $id);
                    $shardDb->query('DELETE FROM user_train_pack WHERE user_id=' . $id);
                    $shardDb->query('DELETE FROM user_train WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_removed_wild WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_market_item WHERE user_id=' . $id);
                    $shardDb->query('DELETE FROM user_market_item WHERE buyer_id=' . $id);

                    $shardDb->query('DELETE FROM user_neighbor WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_order WHERE user_id=' . $id);

                    $shardDb->query('DELETE FROM user_papper_buy WHERE user_id=' . $id);

                    $mainDb->query('DELETE FROM users WHERE id=' . $id);

                    echo 'i"m dovolnuy)';
                } else {
                    echo 'it"s not funny';
                }
            } catch (Exception $e) {
                echo 'oops..';
            }
        } else {
            echo 'u make me nervous.. a=a';
        }
    } else {
        echo 'try again with channel .^/_\^.';
    }
} else {
    echo 'try again with id .^/_\^.';
}