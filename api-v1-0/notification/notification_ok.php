<?php
include_once('../library/Application.php');
$mainDb = Application::getInstance()->getMainDb(3);
$socailNetwork = Application::getInstance()->getSocialNetwork(3);

$time = time();
$vkTimeRestriction = $time - 2592000; // 1 Month
$vkTimeOffline = $time - 86400; // 1 day
$notif = array();

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

$dLast1 = date("Y.m.d", $time - 3 * 60*60*24);
$dLast2 = date("Y.m.d", $time - 2 * 60*60*24);

$notif['date_end'] = $time_ + 60*60*24*10;
$notif['message'] = $txt;
$notif['last_access_range'] = $dLast1.'-'.$dLast2;
$result = $socailNetwork->sendNotification(array(), $notif);

