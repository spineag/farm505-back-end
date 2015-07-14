<?php
/**
 * Created by IntelliJ IDEA.
 * User: dima.tsyutsyura
 * Date: 1/20/15
 * Time: 11:16 AM
 */

date_default_timezone_set('Europe/Moscow');

require_once __DIR__ . "/../library/crud/Application.php";

$app = Application::getInstance();
$mainDb = $app->getMainDb();


if ($_GET['sd'] != '' AND $_GET['ed'] != '' AND $_GET['key'] != '')
{
    $sd = (int) $_GET['sd'];
    $ed = (int) $_GET['ed'];
    $key = $_GET['key'];
    if ($key == md5($sd . "joyrocks" . $ed))
    {
        $query = "SELECT SUM(vote) AS sum, count(order_id) AS count FROM dict_payments WHERE date >= " . $sd . " AND date < " . $ed . " ";
        $db_r = $mainDb->query($query);
        if ($db_r->getResult())
        {
            while ($r = $db_r->fetch())
            {
                $count = $r['count'] ? $r['count'] : 0 ;
                $sum = $r['sum'] ? $r['sum'] : 0 ;
            }
        }
    }
    else
    {
        echo "Wrong key!";
        die;
    }
    $result = json_encode(array(
        purchase => array(sum => $sum, count => $count),
    ));
    echo $result;
}
?>