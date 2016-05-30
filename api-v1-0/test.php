<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');
$app = Application::getInstance();
$mainDb = $app->getMainDb();

$result = $mainDb->query("SELECT * FROM available_users WHERE social_id =".$_POST['idSocial']);

$json_data['message'] = $result->fetchAll();
echo json_encode($json_data);

?>