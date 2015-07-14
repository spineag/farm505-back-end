<?php
/**
 * Created by IntelliJ IDEA.
 * User: volodymyr.ivchyk
 * Date: 9/24/14
 * Time: 3:42 PM
 */
require_once $_SERVER['DOCUMENT_ROOT'] . "/api-v1-0/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api-v1-0/config/connection.php";

$tableNames =  array("dict_level", "dict_sublevel", "dict_sublevel_background", "dict_sublevel_object", "dict_sublevel_decor");

foreach($tableNames as $tableName)
{
    $backupFile = $_SERVER['DOCUMENT_ROOT'] . "/backup/" . $tableName . "_" . time() . ".sql";
    exec('mysqldump -u' . USER . ' -p' . PASSWORD . ' -t ' . DB . ' ' . $tableName . ' > ' . $backupFile);
}

echo "Backedup  data successfully\n";