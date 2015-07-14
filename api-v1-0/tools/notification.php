<?php

require_once __DIR__ . "/../library/crud/Application.php";
//require_once __DIR__ . "/../cli.php";

$app = Application::getInstance();
$mainDb = $app->getMainDb();
$socailNetwork = $app->getSocialNetwork();

$notif_all_id = array();
$notif = array();
$notif_all = array();
$notif_action = array();
$last_user_id = 0;
$time = time();

$db = $mainDb->query("SELECT id_notif, message, type
                      FROM dict_notification
                      WHERE notif_state = '0'
                      AND active='true' AND date_start <= " . $time . " AND date_end >= " . $time . " ORDER BY id_notif ASC");

while ($r = $db->fetch())
{
    if ($r['type'] == 'action')
    {
        $notif_action[$r['id_notif']] = $r['message'];
        $notif_action_id[] = $r['id_notif'];
    }
    elseif ($r['type'] == 'all')
    {
        $notif_all[$r['id_notif']] = $r['message'];
        $notif_all_id[] = $r['id_notif'];
    }
}

$notif = ($notif_action) ? $notif_action : $notif_all;
$notif_id = ($notif_action) ? $notif_action_id : $notif_all_id;

if (!empty($notif))
{
    $vkTimeRestriction = time() - 2592000; // 1 Month
    $d1 = mktime(0, 0 , 0, date("n"), date("j"), date('Y'));
    $pack = 1000;
    $pack_vk = 100;

    $db = $mainDb->query("SELECT user_id
                          FROM users
                          WHERE last_visit_date > " . $vkTimeRestriction . " ORDER BY user_id DESC LIMIT 1;");
    $row = $db->fetch();
    if (!empty($row))
    {
        $last_user_id = $row['user_id'];
    }

    $send_notif = array();
    $db = $mainDb->query("SELECT id_notif, last_user_id
                          FROM dict_notification_send
                          WHERE id_notif in (" . implode(",", $notif_id) . ") ORDER BY last_user_id DESC LIMIT 1;");
    $row = $db->fetch();
    if (!empty($row))
    {
        $send_notif = $row;
    }
    else
    {
        $send_notif = array
        (
            'id_notif'     => $notif_id[0],
            'last_user_id' => 0
        );
    }

    if ($send_notif['last_user_id'] < $last_user_id)
    {
        $f_uid = $send_notif['last_user_id'] + 1;

        $l_uid = $f_uid + $pack - 1;

        //if ($last_user_id < $l_uid) $l_uid = $last_user_id;

        $users = array();
        $db = $mainDb->query("SELECT user_social_id
                              FROM users
                              WHERE user_id >= '" . $f_uid . "' AND last_visit_date > " . $vkTimeRestriction . " LIMIT " . $pack);
        while ($r = $db->fetch())
        {
            $users[] = $r['user_social_id'];
        }

        $l_uid_social = end($users);
        //
        $db_result = $mainDb->query("SELECT user_id
                                     FROM users
                                     WHERE user_social_id = '" . $l_uid_social . "'");
        if($row = $db_result->fetch())
        {
            $l_uid = $row['user_id'];
        }
        if ($last_user_id < $l_uid) $l_uid = $last_user_id;

        if (!empty($users))
        {
            if ($last_user_id != $l_uid)
            {
                $mainDb->query("INSERT INTO dict_notification_send (`id_notif`, `date`, `first_user_id`, `last_user_id`)
                                VALUES ('" . $send_notif['id_notif'] . "', '" . $time . "', '" . $f_uid . "', '" . $l_uid."')");
            }
            else
            {
                $db_res = $mainDb->query("SELECT count(*) as count
                                          FROM dict_notification
                                          WHERE active = 'true' AND date_start <= " . $time . " AND date_end >= " . $time . "");
                if($row = $db_res->fetch())
                {
                    if($row['count'] > 1)
                    {
                        $mainDb->query("UPDATE dict_notification
                                        SET notif_state = '0'
                                        WHERE active='true' AND date_start <= " . $time . " AND date_end >= " . $time . "");
                        $mainDb->query("UPDATE dict_notification
                                        SET notif_state = '1'
                                        WHERE id_notif='" . $send_notif['id_notif'] . "'");
                        $mainDb->query("DELETE FROM dict_notification_send
                                        WHERE id_notif='" . $send_notif['id_notif'] . "'");
                    }
                }
            }

            $count_users = count($users);
            $k = $count_users/$pack_vk;
            $p = ceil($k);
            $i = 0;
            while($i<$p)
            {
                $uids = "";
                $a1 = $i*$pack_vk;
                $a2 = $a1+$pack_vk;
                if ($a2>$count_users) $a2 = $count_users;
                while($a1<$a2)
                {
                    $uids .= $users[$a1].",";
                    $a1++;
                }
                $result = $socailNetwork->getSocialObject()->api('secure.sendNotification',
                    array('timestamp'=>time(),
                        'random'=>rand(1, 10000),
                        'uids'=>$uids,
                        'message'=>$notif[$send_notif['id_notif']]
                    )
                );

                echo json_encode($result) . "\n";
                // echo "<pre>";
                // print_r($result);
                // echo "</pre>";

                usleep(400000);
                $i++;
            }
        }
    }
}

?>