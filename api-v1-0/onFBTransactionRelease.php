<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

$app = Application::getInstance();
$requestId = filter_var($_POST['requestId']);
$status = filter_var($_POST['status']);
$channelId = 4;

$mainDb = $app->getMainDb($channelId);
$result = $mainDb->query('UPDATE transactions SET status="'.$status.'" WHERE request_id="'.$requestId.'"');  // not use userSocialId because it has bugs.. hz why
if (!$result) {
    $result = $mainDb->query('INSERT INTO transactions SET request_id="'.$requestId.'", status="wtf_error"');
}
echo '';