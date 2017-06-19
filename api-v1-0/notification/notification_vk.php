<?php
include_once('../library/Application.php');

$d = date("w");
if ($d == 1) exit;          // monday

$curHour = gmdate('G');
$curHour = $curHour + 3;  // Moscow UTC +3
if ($curHour == 8 || $curHour == 14) {
    $mainDb = Application::getInstance()->getMainDb(2);
    $socialNetwork = Application::getInstance()->getSocialNetwork(2);

    $vkTimeRestriction = time() - 2592000; // 1 Month
    $vkTimeOffline = time() - 172800; // 2 days
    $db = $mainDb->query("SELECT social_id FROM users WHERE last_visit_date > " . $vkTimeRestriction . " AND last_visit_date < " . $vkTimeOffline . " ORDER BY RAND() DESC LIMIT 1000");
    $ar = $db->fetchAll();
    foreach ($ar as $key => $value) {
        $users[] = $value['social_id'];
    }

    $r = rand(1, 5);
    if ($r == 1) {
        $txt = 'В Умелых Лапках все по тебе соскучились. Скорее возвращайся в игру.';
    } elseif ($r == 2) {
        $txt = 'Долина Рукоделия ждет вас. Заходите в игру!';
    } elseif ($r == 3) {
        $txt = 'Чего бы еще такого произвести? Заходите в Умелые Лапки!';
    } elseif ($r == 4) {
        $txt = 'Все желают приобрести ваши продукты. Заходите в Умелые Лапки!';
    } else {
        $txt = 'Долина Рукоделия ждет вас. Заходите в игру!';
    }

    while (count($users) > 1) {
        $arr = array_splice($users, 0, 100);
        $sArr = implode(",", $arr);
        $result = $socialNetwork->sendNotification($sArr, $txt);
    }
}