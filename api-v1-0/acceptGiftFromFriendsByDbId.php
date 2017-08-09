<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $channelId = (int)$_POST['channelId'];

    $shardDb = $app->getShardDb($_POST['userId'], $channelId);
    $day24 = time() - 60*60*24;
    $arIds = $_POST['arDbIds'];
    $result = $shardDb->query('DELETE FROM user_ask_gift WHERE time_ask <'.$day24.' AND id IN ('.$arIds.')');
    $result = $shardDb->query('UPDATE user_ask_gift SET is_send = 2 WHERE id IN ('.$arIds.')');  // is accepted

}