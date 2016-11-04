<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

$mainDb = Application::getInstance()->getMainDb(2);
$socialNetwork = Application::getInstance()->getSocialNetwork(2);

$r = rand(1, 6);
if ($r == 1) {
    $txt = '123';
} elseif ($r == 2) {
    $txt = '234';
} elseif ($r == 3) {
    $txt = '345';
} elseif ($r == 4) {
    $txt = '456';
} elseif ($r == 5) {
    $txt = '567';
} else {
    $txt = '678';
}

//$vkTimeRestriction = time() - 2592000; // 1 Month
//$db = $mainDb->query("SELECT social_id FROM users WHERE last_visit_date > ".$vkTimeRestriction);
//while ($r = $db->fetch())
//{
//    $users[] = $r['social_id'];
//}
//$result = $socialNetwork->sendNotification($users, $txt);

$result = $socialNetwork->sendNotification('191561520', $txt);

$answer = json_encode($result);
echo $answer;
