<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/api-v1-0/library/defaultResponseJSON.php');


$tables = array(
    'background',
    'decor',
    'object'
);
$app = Application::getInstance();
$mainDb = $app->getMainDb();
$appId = $channelId = 0;

$resourcesARR = $mainDb->query("SELECT * FROM building")->fetchAll();
$buildTypeARR = $mainDb->query("SELECT * FROM resource_type")->fetchAll();
$ambarARR = $mainDb->query("SELECT * FROM data_ambar")->fetchAll();
$fabricaARR = $mainDb->query("SELECT * FROM data_fabrica")->fetchAll();
$farmARR = $mainDb->query("SELECT * FROM data_farm")->fetchAll();
$ridgeARR = $mainDb->query("SELECT * FROM data_ridge")->fetchAll();
$treeARR = $mainDb->query("SELECT * FROM data_tree")->fetchAll();

try
{
    foreach ($tables as $tableName) {
        $dictData = $app->getMemcache()->get($tableName . '_dict');
        if (empty($dictData))
        {
            $result = $mainDb->query("SELECT $tableName.id, $tableName.file_name, " . $tableName . "_type.name AS type FROM dict_$tableName AS $tableName
                INNER JOIN dict_" . $tableName . "_type AS " . $tableName . "_type ON " . $tableName . "_type.id = $tableName.id_" . $tableName . "_type;");

            if (!$result)
            {
                $idAttr->value = 7;
                throw new Exception("Bad request to DB!");
            }

            $dictData = $result->fetchAll();
            $app->getMemcache()->set($tableName . '_dict', $dictData, MEMCACHED_DICT_TIME);
        }

        if (!empty($dictData))
        {
            $parentElement = $response->appendChild($dom->createElement($tableName . 's'));
            foreach ($dictData as $key => $dict)
            {
                $element = $parentElement->appendChild($dom->createElement($tableName));
                $id = $element->appendChild($dom->createElement('id'));
                $id->appendChild($dom->createTextNode($dict['id']));
                $fileName = $element->appendChild($dom->createElement('fileName'));
                $fileName->appendChild($dom->createTextNode($dict['file_name']));
                $typeFilter = $element->appendChild($dom->createElement('typeFilter'));
                $typeFilter->appendChild($dom->createTextNode($dict['type']));
            }
        }
    }
    echo $dom->saveXml();
}
catch (Exception $e)
{
    $messageAttr->value = $e->getMessage();
    echo $dom->saveXml();
}


