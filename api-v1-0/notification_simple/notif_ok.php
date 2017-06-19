<?php
include_once('../library/Application.php');
$mainDb = Application::getInstance()->getMainDb(3);
try {
    $socialNetwork = Application::getInstance()->getSocialNetwork(3);
} catch (Exception $e) {
    echo $e;
}
$time = time();
$notif = array();

$txt = 'Новое событие в игре “Умелые Лапки”. Выполняй заказы и получай в 2 раза больше опыта!';

$dLast1 = date("Y.m.d", $time - 15 * 60*60*24);
$dLast2 = date("Y.m.d", $time);

$notif['date_end'] = $time + 60*60*24*3;
$notif['message'] = $txt;
$notif['last_access_range'] = $dLast1.'-'.$dLast2;
$result = $socialNetwork->sendNotification(array(), $notif);

echo $result;