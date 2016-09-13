<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

$result = $mainDb->query("SELECT * FROM data_level");
$dataLevel = [];
$dataLevel = $result->fetchAll();