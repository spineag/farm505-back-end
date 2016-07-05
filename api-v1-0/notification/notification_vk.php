<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

$mainDb = Application::getInstance()->getMainDb();
$socialNetwork = Application::getInstance()->getSocialNetwork();

$r = rand(1, 6);
if ($r == 1) {
    $txt = 'Купила мама коника';
} elseif ($r == 2) {
    $txt = 'Про нас забыли уже, да?';
} elseif ($r == 3) {
    $txt = 'Бананы! Апельсины! Свежая мякоть березы!';
} elseif ($r == 4) {
    $txt = 'Мы приглашаем Вас прослушать композицию Надежды Кадешевой';
} elseif ($r == 5) {
    $txt = 'Взываю к тебе, мой господин, сорви пшеничку!';
} else {
    $txt = 'Ну что ж, друзья, а не пора ли перекусить?)';
}

//$db = $mainDb->query("SELECT social_id FROM users WHERE last_visit_date > ".$vkTimeRestriction." ORDER BY RAND()");
//while ($r = $db->fetch())
//{
//    $users[] = $r['social_id'];
//}
//$result = $socialNetwork->sendNotification($users, $txt);

$result = $socialNetwork->sendNotification(['191561520'], $txt);
