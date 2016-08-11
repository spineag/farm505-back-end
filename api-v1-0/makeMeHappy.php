<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    if (isset($_GET['a']) && $_GET['a'] == 'a') {
        try {
            $id = $app->getUserId(1, $_GET['id']);
            if ($id >0) {
                $result = $mainDb->query('DELETE FROM user_animal WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_cave WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_plant_ridge WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_recipe_fabrica WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_building WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_building_open WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_tree WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_resource WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_train_pack_item WHERE user_id=' . $id);
                $result = $mainDb->query('DELETE FROM user_train_pack WHERE user_id=' . $id);
                $result = $mainDb->query('DELETE FROM user_train WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_removed_wild WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_market_item WHERE user_id=' . $id);
                $result = $mainDb->query('DELETE FROM user_market_item WHERE buyer_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_neighbor WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_order WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM user_papper_buy WHERE user_id=' . $id);

                $result = $mainDb->query('DELETE FROM users WHERE id=' . $id);

                echo 'i"m dovolnuy)';
            } else {
                echo 'it"s not funny';
            }
        } catch (Exception $e) {
            echo 'oops..';
        }
    } else {
        echo 'u make me nervous..';
    }
}
else
{
    echo 'try again .^/_\^.';
}