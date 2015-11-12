<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleksiy.stadnyk
 * Date: 10/7/14
 * Time: 4:54 PM
 * aFor TEst
 */

////// ORIGINAL

require_once($_SERVER['DOCUMENT_ROOT'] . "/public/api-v1-0/library/OwnMySQLI.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/public/api-v1-0/config/config.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/public/api-v1-0/config/configNew.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/public/api-v1-0/tools/SocialNetwork.php");

class Application
{
    private static $_instance;
    private $_cfg;
    private $_socialNetwork;
    private $_mainDbLink;
    private $_memcached;
    protected static $_settingsConst = array();

    /**
     * @return Application
     */
    final static public function getInstance()
    {
        if (static::$_instance == NULL)
        {
            static::$_instance = new Application();
        }

        return static::$_instance;
    }

    function __construct()
    {
        // get configuration from config file
//        $cfgs = $GLOBALS["cfgs"];
        $serverName = $_SERVER["SERVER_NAME"];
        //$this->_cfg = isset($cfgs[$serverName]) ? $cfgs[$serverName] : die("Wrong configuration  \n");

//        self::$_settingsConst = self::loadDefoultSettings();


    }

    final public function getSnCfg()
    {
        return $this->_cfg["sn"];
    }


    final public function getSettingsConst($key)
    {
        return self::$_settingsConst[$key];
    }
    final public function loadDefoultSettings()
    {
        $result = array();
        $mainDb = $this->getMainDb();
        $querySettings = $mainDb->query("SELECT * FROM dict_settings");
        $settingsAll = $querySettings->fetchAll();
        foreach($settingsAll as $settingsItem)
        {
            $result[$settingsItem["key"]] = $settingsItem["value"];
            define($settingsItem["key"],$settingsItem["value"]);
        }
        return $result;
    }

    /**
     * @return Memcached
     */
    final public function getMemcache()
    {
        if ($this->_memcached == NULL)
        {
            $this->_memcached = new Memcached();

            //Configure memcached
            $this->_memcached->addServer(MEMCACHED_HOST, MEMCACHED_PORT) or die ("Could not connect to memcached!");
        }

        return $this->_memcached;
    }

    final public function getMainDb()
    {
        return new OwnMySQLI(SERVER, USER, PASSWORD, DB);
//        return new mysqli(SERVER, USER, PASSWORD, DB, DBPORT);
//        return new mysqli(SERVER, USER, PASSWORD, DB);
    }

    final public function getShardDb($userId)
    {
        $memcached = $this->getMemcache();
        $shardKey = "shard_" . $userId;
        $dbCfgShard = $memcached->get($shardKey);

        if (empty($dbCfgShard))
        {
            $mainDb = $this->getMainDb();

            $result = $mainDb->query("SELECT id, shard_host, shard_user, shard_password as pass, db_name as `database` FROM shard
            WHERE first_user_id <= '" . $userId . "' AND last_user_id >= '" . $userId . "';");

            $dbCfgShard = $result->fetch();
            $time_out = 5 * 60;
            $memcached->set($shardKey, $dbCfgShard, $time_out);
        }

        if (!empty($dbCfgShard))
        {
            return new OwnMySQLI($dbCfgShard["shard_host"], $dbCfgShard["shard_user"], $dbCfgShard["pass"], $dbCfgShard["database"]);
        }
        else
        {
            return null;
        }
    }

    final public function getUserId($channelId, $socialUId, $chackViewer = false)
    {
        $mainDb = $this->getMainDb();
        $tableName = 'users';

//        $result = $mainDb->select($tableName, 'user_id', ['user_social_id' => $socialUId, 'channel_id' => $channelId], ['int', 'int']);
        $result = $mainDb->query("SELECT id FROM users WHERE social_id =".$socialUId);
        $arr = $result->fetch();
        $userId= $arr['id'];
        if ($userId == false) $userId = 0;

//        if($chackViewer)
//        {
//            $viewerId = $this->haveViewerId($userId);
//            if($viewerId > 0)
//            {
//                $result = $mainDb->select($tableName, 'user_id', ['user_social_id' => $viewerId, 'channel_id' => $channelId], ['int', 'int']);
//                $userData = $result->fetchObj();
//                $userId = ($userData == false) ? 0 : (int)$userData->user_id;
//            }
//        }

        if ($userId > 0)
        {
            // update user last activity date
            $result = $mainDb->update($tableName, ['last_visit_date' => time()], ['id' => $userId], ['int'], ['int']);
        }

        return $userId;
    }

    public function getSocialId($userId)
    {
        $mainDb = $this->getMainDb();
        $tableName = 'users';

        $result = $mainDb->select($tableName, 'social_id', ['id' => $userId], ['int']);

        $userSocialId = (int)$result->f('social_id');

        return $userSocialId;
    }

    final public function newUser($channelId, $socialUId, $name = 'Vasia', $lname = 'Pupkin')
    {
        $mainDb = $this->getMainDb();

        $const = [];
        $result = $mainDb->query('SELECT * FROM const');
        $c = $result->fetchAll();
        for ($i=0; $i<count($c); $i++) {
            $const[$c[$i]['name']] = $c[$i]['value'];
        }

        $result = $mainDb->insert( 'users',
            ['social_id' => $socialUId, 'created_date' => time(), 'last_visit_date' => time(),
                'name' => $name, 'last_name' => $lname, 'channel_id' => $channelId, 'tutorial_step' => 1,
                'ambar_max' => $const['AMBAR_MAX'], 'sklad_max' => $const['SKLAD_MAX'],
                'ambar_level' => 1, 'sklad_level' => 1,
                'hard_count' => $const['HARD_COUNT'], 'soft_count' => $const['SOFT_COUNT'],
                'yellow_count' => $const['YELLOW_COUNT'], 'red_count' => $const['RED_COUNT'],
                'green_count' => $const['GREEN_COUNT'], 'blue_count' => $const['BLUE_COUNT'],
                'xp' => 0, 'level' => 1],
            ['int', 'int', 'int', 'str', 'str', 'int', 'int', 'int', 'int', 'int',
                'int', 'int', 'int', 'int', 'int', 'int', 'int', 'int']);

        if ($result)
        {
            $userId = $this->getUserId($channelId, $socialUId);
            $arr = [31, 32, 33, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118];
            foreach ($arr as $value) {
                $result = $mainDb->insert('user_resource',
                    ['user_id' => $userId, 'resource_id' => $value, 'count' => 1],
                    ['int', 'int', 'int']);
            }
            $resultAmbar = $mainDb->insert('user_building',
                ['user_id' => $userId, 'building_id' => 12, 'in_inventory' => 0, 'pos_x' => $const['AMBAR_POS_X'], 'pos_y' => $const['AMBAR_POS_Y']],
                ['int', 'int', 'int', 'int', 'int']);
            $resultSklad = $mainDb->insert('user_building',
                ['user_id' => $userId, 'building_id' => 13, 'in_inventory' => 0, 'pos_x' => $const['SKLAD_POS_X'], 'pos_y' => $const['SKLAD_POS_Y']],
                ['int', 'int', 'int', 'int', 'int']);

            $arr = [];
            $arrIns = [2, 3, 4, 7, 8, 9];
            $arrInsIds = implode(',', array_values($arrIns));
            $result = $mainDb->query("SELECT id FROM resource WHERE resource_type = 7 AND block_by_level <=".$level." AND id NOT IN (".$arrInsIds.") ORDER BY RAND() LIMIT 1");
            $instrument = $result->fetch();
            if ($instrument['id']) { $arr[] = $instrument['id']; }

            $result = $mainDb->query("SELECT * FROM data_tree ORDER BY RAND()");
            $t = $result->fetchAll();
            $count = 0;
            foreach ($t as $value => $r) {
                $tArr = explode('&', $r['block_by_level']);
                if ($tArr[0] <= 1) {
                    $arr[] = $r['craft_resource_id'];
                    $count++;
                    if ($count >=2) break;
                }
            }

            $result = $mainDb->query("SELECT id FROM resource WHERE resource_type = 5 AND block_by_level <=".$level." ORDER BY RAND() LIMIT 6");
            $plants = $result->fetchAll();
            foreach ($plants as $value => $p) {
                $arr[] = $p['id'];
            }
            for ($i = 0; $i < 6; $i++) {
                $arr[]= -1;
            }

            $resultNeighbor = $mainDb->insert('user_neighbor',
                ['user_id' => $userId, 'last_update' => date('j'), 'resource_id1' => $arr[0], 'resource_id2' => $arr[1], 'resource_id3' => $arr[2], 'resource_id4' => $arr[3], 'resource_id5' => $arr[4], 'resource_id6' => $arr[5]],
                ['int', 'int', 'int', 'int', 'int', 'int', 'int', 'int']);

            return $userId;
        }
        return -1;
    }

    final public function getAppId($appGuid)
    {
        if ($appGuid != '')
        {
            $appData = $this->getMemcache()->get('appData_' . $appGuid);
            if (empty($appData))
            {
                $mainDb = $this->getMainDb();
                $result = $mainDb->select(
                    'application',
                    'id, application_name',
                    ['guid' => $appGuid],
                    ['str'],
                    'id',
                    1
                );

                if ($row = $result->fetchObj())
                {
                    $appData = [
                        'appId'     => $row->id,
                        'appName'   => $row->application_name
                    ];
                    $this->getMemcache()->set('appData_' . $appGuid, $appData, 300);
                }
            }

            return $appData;
        }

        return [];
    }

    final public function getChannelId($chGuid)
    {
        if ($chGuid != '')
        {
            $chData = $this->getMemcache()->get('chData_' . $chGuid);
            if (empty($chData))
            {
                $mainDb = $this->getMainDb();
                $result = $mainDb->select(
                    'channel',
                    'id, channel_name',
                    ['guid' => $chGuid],
                    ['str'],
                    'id',
                    1
                );

                if ($row = $result->fetchObj())
                {
                    $chData = [
                        'chId'=> $row->id,
                        'chName' => $row->channel_name
                    ];
                    $this->getMemcache()->set('chData_' . $chGuid, $chData, 300);
                }
            }

            return $chData;
        }

        return [];
    }

    final public function setSessionKey($userSocialId = 0, $appName = '', $chName = '')
    {
        // create sessionKey and push it to memcache
        $sessionKey = rand(0, 10000);
        $sessionKey = md5($sessionKey);

        $k = 'sess' . $userSocialId . $appName . $chName;
        $this->getMemcache()->set($k, $sessionKey);

        return $sessionKey;
    }

    /** get session key from memcache
     *
     * @param int $userSocialId
     * @param string $appName
     * @param string $chName
     * @return mixed
     */
    final public function getSessionKey($userSocialId = 0, $appName = '', $chName = '')
    {
        return $this->getMemcache()->get("sess" . $userSocialId . $appName . $chName);
    }

    final public function verifySecurityKey($securityKey = '', $appGuid, $chGuid, $userSocialId, $scriptName)
    {
        return (!empty($securityKey) && ($securityKey == (md5($appGuid . $chGuid . $userSocialId . substr($scriptName, strrpos($scriptName, '/') + 1) . GAME_SECRET)) ||
                $securityKey == (md5($appGuid . $chGuid . $userSocialId . substr($scriptName, strrpos($scriptName, '/') + 1) . EDITOR_SECRET))));
    }

    /** remove all user data
     *
     * @param $userId
     */
    final public function deleteUser($userId)
    {
        $shardDb = $this->getShardDb($userId);
        $mainDb = $this->getMainDb();

        // user xp
        $shardDb->delete(
            'user_xp',
            ['user_id' => $userId],
            ['int']
        );

        // user resources
        $shardDb->delete(
            'user_resources',
            ['user_id' => $userId],
            ['int']
        );

        // user hearts
        $shardDb->delete(
            'user_hearts',
            ['user_id' => $userId],
            ['int']
        );

        // user daily bonus
        $shardDb->delete(
            'user_daily_bonus',
            ['user_id' => $userId],
            ['int']
        );

        // user quest tasks
        $shardDb->delete(
            'user_quest_tasks',
            ['user_id' => $userId],
            ['int']
        );

        // user quests
        $shardDb->delete(
            'user_quests',
            ['user_id' => $userId],
            ['int']
        );

        // user level
        $shardDb->delete(
            'user_level',
            ['user_id' => $userId],
            ['int']
        );

        // user gifts send
        $shardDb->delete(
            'user_gifts_send',
            ['user_id' => $userId],
            ['int']
        );

        // user gifts ask
        $shardDb->delete(
            'user_gifts_ask',
            ['user_id' => $userId],
            ['int']
        );

        // user chests
        $shardDb->delete(
            'user_chests',
            ['user_id' => $userId],
            ['int']
        );

        // user boosters
        $shardDb->delete(
            'user_boosters',
            ['user_id' => $userId],
            ['int']
        );

        // testers
        $mainDb->delete(
            'testers',
            ['user_id' => $userId],
            ['int']
        );

        // users
        $mainDb->delete(
            'users',
            ['user_id' => $userId],
            ['int']
        );
    }

    final public function getViewerId($userId)
    {
        $mainDb = $this->getMainDb();
        $id = $userId;

        $testerData = $mainDb->select(
            'testers',
            'viewer_id',
            ['user_id' => $userId],
            ['int'],
            '',
            1
        );

        $viewerSocialId = $testerData->f('viewer_id');

        if ($viewerSocialId > 0)
        {
            $channelData = $mainDb->select(
                'users',
                'channel_id',
                ['user_id' => $userId],
                ['int'],
                '',
                1
            );
            $channelId = $channelData->f('channel_id');

            $viewerId = $this->getUserId($channelId, $viewerSocialId);

            if ($viewerId > 0)
            {
                $id = $viewerId;
            }
        }

        return $id;
    }

    final public function getSocialNetwork()
    {
        if ($this->_socialNetwork == NULL)
        {
            $socialNetwork = $this->_cfg["sn"]["socialNetworkClass"];
            $this->_socialNetwork = new $socialNetwork($this->_cfg["sn"]);
        }
        return $this->_socialNetwork;
    }

    final public function getRandomResource($userId)
    {
        $mainDb = $this->getMainDb();
        $result = $mainDb->query("SELECT * FROM users WHERE id =".$userId);
        if ($result) {
            $arr = $result->fetch();
            $level = $arr['level'];
        } else {
            $level = 1;
        }
        $result = $mainDb->query("SELECT id FROM resource WHERE block_by_level <=".$level." AND url <> 'instrumentAtlas'");
        if ($result) {
            $arr = $result->fetchAll();
            return array_rand($arr, 1);
        } else {
            return 10;
        }
    }
}