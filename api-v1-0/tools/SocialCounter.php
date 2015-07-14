<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/api-v1-0/library/Application.php');

class SocialCounter
{
    /**
     * @param $friendID
     * @param int $count
     */
    public static function increaseCounter($friendID, $count = 1)
    {
        $app = Application::getInstance();
        $social = $app->getSocialNetwork();

        if($social->setCounters(null))
        {
            $main_db  = $app->getMainDb();
            $memcache = $app->getMemcache();

            if(is_array($friendID))
            {
                $usersAndCounters = array();

                $result = $main_db->query("SELECT user_social_id
                                           FROM users
                                           WHERE user_id IN (".implode(',', $friendID).")");
                while($row = $result->fetch())
                {
                    $userCounter = $memcache->get('vk_menu_'.$row['user_social_id']);

                    $savedCount = (!empty($userCounter)) ? $userCounter['count'] : 0;
                    $savedCount = ($count == 0) ? 0 : $savedCount + $count;

                    $usersAndCounters[] = array(
                        'id' => $row['user_social_id'],
                        'counter' => $savedCount
                    );
                    $memcache->set('vk_menu_'.$row['user_social_id'], array('count' => $savedCount), 84600);
                }
                $social->setCounters($usersAndCounters);
            }
            else
            {

                $result =  $main_db->query("SELECT user_social_id
                                            FROM users
                                            WHERE user_id = '" . $friendID . "'");
                if($row = $result->fetch())
                {
                    $userCounter = $memcache->get('vk_menu_'.$row['user_social_id']);
                    $savedCount = (!empty($userCounter)) ? $userCounter['count'] : 0;
                    $savedCount = ($count == 0) ? 0 : $savedCount + $count;
                    $social->setCounters(array(array(
                        'id' => $row['user_social_id'],
                        'counter' => $savedCount
                    )));
                    $memcache->set('vk_menu_'.$row['user_social_id'], array('count' => $savedCount), 84600);
                }
            }
        }
    }
}