<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $channelId = (int)$_POST['channelId'];
    $shardDb = $app->getShardDb($_POST['userId'], $channelId);
    $ar = explode(",", $_POST['users']);
    foreach ($ar as $value => $u) {
        $result = $shardDb->query('INSERT INTO user_ask_gift SET user_id = "'.$_POST['userId'].'", user2_id = "'.$u.'", resource_id='.$_POST['resId'].', time_ask='.time());
    }

}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's...';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}