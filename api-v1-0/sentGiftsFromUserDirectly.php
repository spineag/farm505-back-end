<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $channelId = (int)$_POST['channelId'];
    $ar = explode(",", $_POST['users']);
    foreach ($ar as $value => $u) {
        $shardDb = $app->getShardDb($u, $channelId);
        $result = $shardDb->query('INSERT INTO user_ask_gift SET user_id = "'.$u.'", user2_id = "'.$_POST['userId'].'", is_send=1, resource_id='.$_POST['resId'].', time_ask='.time());
    }

}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's...';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}