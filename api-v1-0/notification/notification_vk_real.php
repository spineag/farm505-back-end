<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');

$mainDb = Application::getInstance()->getMainDb();
$socialNetwork = Application::getInstance()->getSocialNetwork();

$vkTimeRestriction = time() - 2592000; // 1 Month

$db = $mainDb->query("SELECT social_id FROM users WHERE last_visit_date > ".$vkTimeRestriction." ORDER BY RAND() DESC LIMIT 10000");
while ($r = $db->fetch())
{
    $users[] = $r['social_id'];
}

$userReadyAnimals = [];
$userReadyPlants = [];
$userReadyRecipe = [];
$userSimplePost = [];

$counter = count($users);
while ($counter >= 0) {
    if ($users[$counter]) {
        $r = rand(1, 4);
        if ($r == 1) {

        } elseif ($r == 2) {

        } elseif ($r == 3) {

        } elseif ($r == 4) {

        }
    }
    $counter--;
}