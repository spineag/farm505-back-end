<?php
date_default_timezone_set('Europe/Moscow');

require_once __DIR__ . "/../library/crud/Application.php";

$app = Application::getInstance();
$mainDb = $app->getMainDb();

if ($_GET['key'] != '')
{
    $count = 0;
    $key = $_GET['key'];

    if ($key == md5("joyrocks"))
    {
        $result = $mainDb->query('SELECT count(*) AS count
                                  FROM users
                                  WHERE users.last_visit_date >(unix_timestamp(now()) - 2073600)');
        if($row = $result->fetch())
        {
            $count = $row['count'];
        }

    }
    else
    {
        echo "Wrong key!";
        die;
    }

    echo json_encode(array(
        'count' => $count
    ));
}
