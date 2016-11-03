<?php
include_once('../library/Application.php');

$mainDb = Application::getInstance()->getMainDb(2);
$socialNetwork = Application::getInstance()->getSocialNetwork();

$result = $socialNetwork->setUserLevel($_POST['id'], $_POST['level']);
