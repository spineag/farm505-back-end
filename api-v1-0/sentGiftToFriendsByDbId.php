<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$app = Application::getInstance();
$channelId = (int)$_POST['channelId'];

$stI = $_POST['arDbIds'];
$stU = $_POST['arUserIds'];
$arI = explode(',', $stI);
$arU = explode(',', $stU);
$count = count($arI);
for ($i = 0; $i < $count; $i++) {
    $dbId = $arI[$i];
    $userId = $arU[$i];
    $shard = $app->getShardDb($userId, $channelId);
    $result = $shard->query('UPDATE user_ask_gift SET is_send=1 WHERE id='.$dbId);
}