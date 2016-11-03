<?php

require_once 'init_tools.php';

$mainDb = Application::getInstance()->getMainDb(2);
$socailNetwork = Application::getInstance()->getSocialNetwork();

$notif_all_id = array();
$notif = array();
$notif_all = array();
$notif_action = array();
$last_user_id = 0;
$time = time();

$db = $mainDb->query("SELECT id_notif, message, type FROM game_notification WHERE active='true' and date_start<=" . $time . " and date_end>=" . $time . " ORDER BY RAND()");
while ($r = $db->fetch()) {
    if ($r['type'] == 'action') {
        $notif_action[$r['id_notif']] = $r['message'];
        $notif_action_id[] = $r['id_notif'];
    }
    elseif ($r['type'] == 'all') {
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
    $pack = 50000;
    $pack_vk = 100;

    $db = $mainDb->query("SELECT user_id FROM game_user WHERE date_last_visit > ".$vkTimeRestriction." ORDER BY user_id DESC LIMIT 1;");
    $row = $db->fetch();
    if (!empty($row))
    {
        $last_user_id = $row['user_id'];
    }
    $send_notif = array();
    $db = $mainDb->query("SELECT id_notif, last_user_id FROM game_notification_send WHERE id_notif in (".implode(",", $notif_id).") ORDER BY last_user_id DESC LIMIT 1;");
    $row = $db->fetch();
    if (!empty($row))
    {
        $send_notif = $row;
    }
    else
    {
        $send_notif = array('id_notif'=>$notif_id[0], 'last_user_id'=>0);
    }

    if ($send_notif['last_user_id']<$last_user_id)
    {
        $f_uid = $send_notif['last_user_id']+1;
        $l_uid = $f_uid+$pack-1;
        if ($last_user_id<$l_uid) $l_uid = $last_user_id;
        $users = array();
        $db = $mainDb->query("SELECT id_social FROM game_user WHERE user_id>='".$f_uid."' AND user_id<='".$l_uid."' AND date_last_visit > ".$vkTimeRestriction."");
        while ($r = $db->fetch())
        {
            $users[] = $r['id_social'];
        }

        if (!empty($users))
        {
            if ($last_user_id!=$l_uid)
            {
                $mainDb->query("INSERT INTO game_notification_send (`id_notif`, `date`, `first_user_id`, `last_user_id`) VALUES ('".$send_notif['id_notif']."', '".$time."', '".$f_uid."', '".$l_uid."')");
            }
            else
            {
                $mainDb->query("DELETE FROM game_notification_send WHERE id_notif='".$send_notif['id_notif']."'");
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
                $result = $socailNetwork->sendNotification($uids, $notif[$send_notif['id_notif']]);
                /*
                                echo "<pre>";
                                print_r($result);
                                echo "</pre>";
                                */
                sleep(1);
                $i++;
            }
        }
    }
}