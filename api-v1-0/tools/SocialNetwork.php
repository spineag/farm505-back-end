<?php
require_once 'vkapi.class.php';

interface SocialNetworkInterface
{
    /**
     *
     * @param array $socialNetworkUids
     * @return array
     */
    public function getUsers($socialNetworkUids);
    public function isGroupMember($socialNetworkUid, $socialNetworkGroupId);
    public function setUserLevel($socialNetworkUid, $socialNetworkLevel);

    /**
     *
     * @param array $socialNetworkUid
     * @param string $message
     * @return boolean
     */

    public function sendNotification($socialNetworkUid, $message);
    public function getJavaScript();
    public function transactionChek($transaction_id);
    public function getFriendCount();
    public function getFriends();
    public function getFriendsApp();
    public function getFriendsOnline();
    public function transactionCreate($price, $serviceId);
    public function addActivity($text);
    public function isBirthDay($date);
    public function getSocialObject();
    public function userSync($socialNetworkUid, $socialNetworkLevel, $socialNetworkXp);
    public function check_in_another_game($socialNetworkUid);
    public function setCounters($usersAndCounters);

    /**
     *
     * @param $socialNetworkUid
     * @param string $country (RU,UA)
     * @param string $age_range (18-25)
     * @param string $gender (0 / 1 / 2) 1-f, 2-m
     *
     * @return boolean
     */

    public function check_targeting($socialNetworkUid, $country, $age_range, $gender, $bdate);

    /**
     * for check connection with SN
     * @return ok
     */

    public function check_connection();

}

class VKSocialNetwork implements SocialNetworkInterface
{
    private $_vk;
    private $_socialNetworkParameters;
    function __construct($socialNetworkParameters)
    {
        $this->_socialNetworkParameters = $socialNetworkParameters;
    }

    public function getSocialObject()
    {
        if (empty($this->_vk))
        {
            $this->_vk = new vkapi(
                $this->_socialNetworkParameters["api_id"],
                $this->_socialNetworkParameters["secret_key"]);
        }
        return $this->_vk;
    }

    public function setCounters($usersAndCounters)
    {
        if($usersAndCounters === null)
        {
            return true;
        }

        $limit = 200;
        $requestArray = array();

        foreach($usersAndCounters as $user)
        {
            if($limit<=0)
            {
                break;
            }
            $requestArray[] = $user['id'].':'.$user['counter'];
            $limit--;
        }

        $requestString = implode(',', $requestArray);

//        $config = Application::getInstance()->getGearmanCfg();
//        if(class_exists('GearmanClient') && $config !== NULL)
//        {
//            $gmc = new GearmanClient();
//            $gmc->addServer($config['host'], $config['port']);
//            $gmc->doBackground('VKSetCounter', $requestString);
//            return true;
//        }

        $resultResponse = $this->getSocialObject()->api('secure.setCounter', array('counters' => $requestString));

        return $resultResponse['response'];
    }

    public function getUsers($socialNetworkUid)
    {
        $resultResponse = $this->getSocialObject()->api('users.get', array('uids'=>$socialNetworkUid, 'fields'=>'sex, bdate, city, country, first_name, last_name'));
        return $resultResponse['response'];
    }

    public function isGroupMember($socialNetworkUid, $socialNetworkGroupId)
    {
        return $this->getSocialObject()->api('groups.isMember', array('gid'=>$socialNetworkGroupId, 'uid'=>$socialNetworkUid));
    }

    public function setUserLevel($socialNetworkUid, $socialNetworkLevel)
    {
        return $this->getSocialObject()->api('secure.setUserLevel', array('user_id'=>$socialNetworkUid, 'level'=>$socialNetworkLevel));
    }

    public function sendNotification($socialNetworkUid, $message)
    {
//        $config = Application::getInstance()->getGearmanCfg();
//        if(class_exists('GearmanClient') && $config !== NULL)
//        {
//            $gmc = new GearmanClient();
//            $gmc->addServer($config['host'], $config['port']);
//            $gmc->doBackground('VKSendNotification', json_encode(array('timestamp'=>time(), 'random'=>rand(1, 10000), 'uids'=>$socialNetworkUid, 'message'=>$message)));
//
//            return true;
//        }
        return $this->getSocialObject()->api('secure.sendNotification', array('timestamp'=>time(), 'user_ids'=>$socialNetworkUid, 'message'=>$message));
    }

    public function getJavaScript()
    {
        return false;
    }

    public function transactionChek($transaction_id)
    {
        return false;
    }

    public function getFriendCount()
    {
        return 0;
    }

    public function getFriends()
    {
        return false;
    }

    public function getFriendsApp()
    {
        return false;
    }

    public function getFriendsOnline()
    {
        return false;
    }

    public function transactionCreate($price, $serviceId)
    {
        return false;
    }

    public function addActivity($text)
    {
        return false;
    }

    public function isBirthDay($date)
    {
        if (!empty($date))
        {
            $data = explode(".", $date);
            $day = date('j');
            $month = date('n');
            if ($data[0] == $day && $data[1] == $month){
                return true;
            }
        }

        return false;
    }

    public function userSync($socialNetworkUid, $socialNetworkLevel, $socialNetworkXp)
    {
        return false;
    }

    public function check_in_another_game($socialNetworkUid)
    {
        return false;
    }

    public function check_targeting($socialNetworkUid, $country, $age_range, $gender, $bdate)
    {
        $user = $this->getUsers($socialNetworkUid);
        $user = $user[0];

        if (!empty($country))
        {
            /*
            $res = $this->getSocialObject()->api('places.getCountries', array('need_full'=>0, 'code'=>$country));
            return $res;
            $code = array();
            if (!empty($res['response']))
            {
                foreach ($res['response'] as $k)
                {
                    $code[] = $c['cid'];
                }
            }
            */
            $targ = explode(",", $country);
            $country_array = array(1=>"RU", 2=>"UA");


            if (!in_array($country_array[$user['country']], $targ)) return false;
        }

        if (!empty($age_range))
        {
            //	if (empty($user['bdate'])) return false;
            //	new *
            if (empty($bdate)) return false;
            $bdate = explode(".", $bdate);

            //	$bdate = explode(".", $user['bdate']);

            if (empty($bdate[2])) return false;

            $year = date('Y');
            $u_age = $year - $bdate[2];
            $age = explode("-", $age_range);


            if ($u_age < $age[0] || $u_age > $age[1]) return false;

        }

        if (!empty($gender))
        {
            if ($user["sex"] != $gender) return false;
        }

        return true;
    }

    public function check_connection()
    {
        $res = $this->getUsers("26342690");
        if (!empty($res[0])){
            return "ok";
        } else {
            return "error";
        }
    }
}