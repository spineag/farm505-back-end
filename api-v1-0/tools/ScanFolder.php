<?php
/**
 * Created by IntelliJ IDEA.
 * User: volodymyr.ivchyk
 * Date: 8/4/14
 * Time: 12:44 PM
 */

require_once($_SERVER['DOCUMENT_ROOT'] . "/api-v1-0/config/config.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/api-v1-0/config/connection.php");

$scanPath = $_SERVER['DOCUMENT_ROOT'] . 'content';
$filesToDb = [];
if (is_dir($scanPath))
{
    $rows = 0;
    $data_folders = scandir($scanPath);
    unset($data_folders[0]);
    unset($data_folders[1]);

    foreach ($data_folders as $folder)
    {
        if ($dir = opendir($scanPath . "/" . $folder))
        {
            while (false !== ($file = readdir($dir)))
            {
                if (is_dir($scanPath . "/" . $folder . "/" . $file))
                {
                    continue;
                }
                $dbTable = getTable($folder);
                $push = pushToDb($dbTable, $file, $link);
                if ($push > 0)
                {
                    $filesToDb[$folder][sizeof($filesToDb[$folder])] = $file;
                    $rows += $push;
                }

            }
            closedir($dir);
        }
    }
  echo  $rows . "rows were inserted " . "<br />";
    echo "<pre>";
    print_r($filesToDb);
    echo "</pre>";
}

function getTable($folder)
{
    $table = false;
    if ($folder)
    {
        switch($folder)
        {
            case 'backgrounds' :
                $table = 'dict_background';
                break;
            case 'decors' :
                $table = 'dict_decor';
                break;
            case 'objects' :
                $table = 'dict_object';
                break;
        }
    }

    return $table;
}

function pushToDb($table, $filename, $link)
{
    $insert = 0;

    $result = mysql_query("SELECT count(1) as cnt FROM " . $table . " WHERE file_name = '" . $filename . "'");
    $count = mysql_fetch_assoc($result);

    if($count['cnt'] == 0)
    {
        switch ($table)
        {
            case 'dict_background' : $column = 'id_background_type';
                break;
            case 'dict_object' : $column = 'id_object_type';
                break;
            case 'dict_decor' : $column = 'id_decor_type';
                break;
            default : $column = false;
        }
        if ($column)
        {
            if (mysql_query("INSERT INTO " . $table . " (`file_name`, `" .  $column . "`) values ('" . $filename ."', '1')"))
            {
                $insert = 1;
            }
        }
    }
    return $insert;
}