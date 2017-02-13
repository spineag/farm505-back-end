<?php
include_once('../library/Application.php');
$mainDb = Application::getInstance()->getMainDb(3);
$socailNetwork = Application::getInstance()->getSocialNetwork(3);

$time = time();
$notif = array();

$r = rand(1, 3);
if ($r == 1) {
    $txt = 'Получи свой ежедневный подарок';
} elseif ($r == 2) {
    $txt = 'Забери свой бесплатный подарок';
} else {
    $txt = 'Твой подарок ждет тебя';
//} elseif ($r == 4) {
//    $txt = 'Все желают приобрести ваши продукты. Заходите в Умелые Лапки!';
//} else {
//    $txt = 'Долина Рукоделия ждет вас. Заходите в игру!';
}





$dLast1 = date("Y.m.d", $time - 3   * 60*60*24);
$dLast2 = date("Y.m.d", $time - 1.5 * 60*60*24);

$notif['date_end'] = $time + 60*60*24*12;
$notif['message'] = $txt;
$notif['last_access_range'] = $dLast1.'-'.$dLast2;
$result = $socailNetwork->sendNotification(array(), $notif);

