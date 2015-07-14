<?php
/**
 * Created by IntelliJ IDEA.
 * User: dima.tsyutsyura
 * Date: 1/16/15
 * Time: 10:07 AM
 */

require_once __DIR__ . "/../library/crud/Application.php";

$app = Application::getInstance();
$mainDb = $app->getMainDb();

$mainDb->query("UPDATE users SET is_daily_bonus = 0 ;");