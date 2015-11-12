<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/public/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
//        $result = $mainDb->delete('user_animal',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_animal WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_plant_ridge',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_plant_ridge WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_recipe_fabrica',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_recipe_fabrica WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_building',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_building WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_building_open',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_building_open WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_tree',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_tree WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_resource',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_resource WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_train_pack_item',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_train_pack_item WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_train_pack',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_train_pack WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_train',
//            ['user_id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_train WHERE user_id='.$_POST['userId']);
        $result = $mainDb->query('DELETE FROM user_removed_wild WHERE user_id='.$_POST['userId']);

//        $result = $mainDb->delete('user_market_item',
//            ['id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM user_market_item WHERE user_id='.$_POST['userId']);
        $result = $mainDb->query('DELETE FROM user_market_item WHERE buyer_id='.$_POST['userId']);

//        $result = $mainDb->delete('users',
//            ['id' => $_POST['userId']],
//            ['int']);
        $result = $mainDb->query('DELETE FROM users WHERE id='.$_POST['userId']);
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