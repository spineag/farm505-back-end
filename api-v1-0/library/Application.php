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

    /** get user Id and create/update his data
     *
     * @param $channelId
     * @param $socialUId
     * @param $sex
     * @param $bDate
     * @return int
     * @throws Exception
     */
    final public function getUserId($channelId, $socialUId, $chackViewer = false)
    {
        $mainDb = $this->getMainDb();
        $tableName = 'users';

//        $result = $mainDb->select($tableName, 'user_id', ['user_social_id' => $socialUId, 'channel_id' => $channelId], ['int', 'int']);
        $result = $mainDb->query("SELECT id FROM users WHERE social_id =".$socialUId);
        $userId = $result->fetch();
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

    /**
     * @param $userId
     * @return int
     */
    public function getSocialId($userId)
    {
        $mainDb = $this->getMainDb();
        $tableName = 'users';

        $result = $mainDb->select($tableName, 'social_id', ['id' => $userId], ['int']);

        $userSocialId = (int)$result->f('social_id');

        return $userSocialId;
    }

    /** create new user and insert empty user data
     *
     * @param $channelId
     * @param $socialUId
     * @param $sex
     * @param $bDate
     * @return int
     */
    final public function newUser($channelId, $socialUId, $name = 'Vasia', $lname = 'Pupkin')
    {
        $mainDb = $this->getMainDb();
        $tableName = 'users';

        $result = $mainDb->insert( $tableName,
                                   ['social_id' => $socialUId, 'created_date' => time(), 'last_visit_date' => time(),
                                    'name' => $name, 'last_name' => $lname, 'channel_id' => $channelId, 'tutorial_step' => 1,
                                    'ambar_max' => DEFAULT_AMBAR_MAX, 'sklad_max' => DEFAULT_SKLAD_MAX,
                                    'ambar_level' => DEFAULT_AMBAR_LEVEL, 'sklad_level' => DEFAULT_SKLAD_LEVEL,
                                    'hard_count' => DEFAULT_HARD_COUNT, 'soft_count' => DEFAULT_SOFT_COUNT,
                                    'yellow_count' => DEFAULT_YELLOW_COUNT, 'red_count' => DEFAULT_RED_COUNT,
                                    'green_count' => DEFAULT_GREEN_COUNT, 'blue_count' => DEFAULT_BLUE_COUNT,
                                    'xp' => 0, 'level' => 1],
                                   ['int', 'int', 'int', 'str', 'str', 'int', 'int', 'int', 'int', 'int',
                                    'int', 'int', 'int', 'int', 'int', 'int', 'int', 'int']);

        if ($result)
        {
            $userId = $this->getUserId($channelId, $socialUId);
            return $userId;
        }
        return 0;
    }

    /** get app data by guid
     *
     * @param $appGuid
     * @return array
     */
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

    /** get channel data by guid
     *
     * @param $chGuid
     * @return array
     */
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

    /** put session key to memcache
     *
     * @param int $userSocialId
     * @param string $appName
     * @param string $chName
     * @return int|string
     */
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

    final public function getUserInfo($channelId = 0, $userSocialId = 0)
    {
        if ($userSocialId > 0)
        {
            $mainDb = $this->getMainDb();
            $result = $mainDb->query("SELECT u.user_id, u.volume, u.music, u.payer, u.tutorial_step, u.is_daily_bonus, u.is_ab_test, count(t.user_id) AS tester, t.megatester AS megatester
                                      FROM users u
                                      LEFT JOIN testers t ON t.user_id = u.user_id
                                      WHERE u.user_social_id = '" . mysqli_real_escape_string($userSocialId) . "' AND u.channel_id = '" . mysqli_real_escape_string($channelId) . "';");
            $result = $result->fetch();

            $viewerId = $this->haveViewerId($result["user_id"]);
            if($viewerId > 0)
            {
                $result = $mainDb->query("SELECT u.user_id, u.volume, u.music, u.payer, u.tutorial_step, u.is_daily_bonus, u.is_ab_test, count(t.user_id) AS tester, t.megatester AS megatester
                                          FROM users u
                                          LEFT JOIN testers t ON t.user_id = u.user_id
                                          WHERE u.user_social_id = '" . mysqli_real_escape_string($viewerId) . "' AND u.channel_id = '" . mysqli_real_escape_string($channelId) . "';");
                $result = $result->fetch();
            }
            return $result;
        }
        else
        {
            return [];
        }
    }

    final public function countHearts($userId)
    {
        $shardDb = $this->getShardDb($userId);
        $result = $shardDb->select(
            'user_hearts',
            'hearts_count, update_date',
            ['user_id' => $userId],
            ['int']
        );
        if ($result && $userHearts = $result->fetch())
        {
            $count = $userHearts['hearts_count'];
            $updateDate = $userHearts['update_date'];
            $now = time();
            $interval = 0;

            if ($count < HEARTS_MAX_COUNT)
            {
                // calc interval to next generation of heart & current count of hearts
                $interval = $now - $updateDate;
                $genHearts = floor($interval / HEARTS_GENERATION_INTERVAL);

                $count += $genHearts;
                if ($count >= HEARTS_MAX_COUNT)
                {
                    $count = HEARTS_MAX_COUNT;
                    $interval = 0;
                }
                else
                {
                    $interval =  $interval - ($genHearts * HEARTS_GENERATION_INTERVAL);
                }

                $updateDate = $now - $interval;
                $shardDb->update(
                    'user_hearts',
                    ['hearts_count' => $count, 'update_date' => $updateDate],
                    ['user_id' => $userId],
                    ['int', 'int'],
                    ['int']
                );

                $interval = HEARTS_GENERATION_INTERVAL - $interval;
            }
            else
            {
                $shardDb->update(
                    'user_hearts',
                    ['update_date' => $now],
                    ['user_id' => $userId],
                    ['int'],
                    ['int']
                );
            }

            return [
                'count' => $count,
                'interval'  => $interval,
                'time'  => $updateDate
            ];

        }
        else
        {
            return [
                'count' => 0,
                'interval'  => 0,
                'time'  => 0
            ];
        }
    }

    final public function getDailyBonus($userId) //fixme
    {
        $sqlQuery = "SELECT day_number, update_date FROM " . DB . ".user_daily_bonus
                            WHERE user_id = '" . mysqli_real_escape_string($userId) . "';";
        $sqlRes = mysqli_query($sqlQuery);
        $data = mysqli_fetch_object($sqlRes);

        $dayNumber = $data->day_number;
        $updateDate = $data->update_date;
        $interval = 0;

        return [
            'number' => $dayNumber,
            'interval' => $interval
        ];
    }

    /** check and subtract 1 heart
     *
     * @param $userId
     * @return bool
     * @throws Exception
     */
    final public function getHeart($userId)
    {
        $shardDb = $this->getShardDb($userId);
        $result = $shardDb->select(
            'user_hearts',
            'hearts_count, update_date',
            ['user_id' => $userId],
            ['int'],
            '',
            1
        );

        if ($result && $data = $result->fetchObj())
        {
            $count = $data->hearts_count;
            $updateDate = $data->update_date;
            $now = time();
            $interval = 0;

            if ($count < HEARTS_MAX_COUNT)
            {
                // calc interval to next generation of heart & current count of hearts
                $interval = $now - $updateDate;
                $genHearts = floor($interval / HEARTS_GENERATION_INTERVAL);

                $count += $genHearts;
                if ($count >= HEARTS_MAX_COUNT)
                {
                    $count = HEARTS_MAX_COUNT;
                    $interval = 0;
                }
                else
                {
                    $interval =  $interval - ($genHearts * HEARTS_GENERATION_INTERVAL);
                }

                $updateDate = $now - $interval;

                $update = $shardDb->update(
                    'user_hearts',
                    ['hearts_count' => $count, 'update_date' => $updateDate],
                    ['user_id' => $userId],
                    ['int', 'int'],
                    ['int']
                );
                if (!$update)
                {
                    throw new Exception("Can't update data in user_hearts table!");
                }

                $interval = HEARTS_GENERATION_INTERVAL - $interval;
            }
            else
            {
                $update = $shardDb->update(
                    'user_hearts',
                    ['update_date' => $now],
                    ['user_id' => $userId],
                    ['int'],
                    ['int']
                );
                if (!$update)
                {
                    throw new Exception("Can't update data in user_hearts table!");
                }
            }

            // subtract 1 heart
            if ($count > 0)
            {
                $update = $shardDb->update(
                    'user_hearts',
                    ['hearts_count' => ($count - 1)],
                    ['user_id' => $userId],
                    ['int'],
                    ['int']
                );
                if (!$update)
                {
                    throw new Exception("Can't update data in user_hearts table!");
                }

                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /** add some count of hearts
     *
     * @param int $userId
     * @param int $count
     * @return bool
     */
    final function addHearts($userId = 0, $count = 0)
    {
        $result = false;

        if ($count > 0 && $userId > 0)
        {
            $shardDb = $this->getShardDb($userId);
            $heartsData = $this->countHearts($userId);

            $countHearts = $heartsData['count'];

            if (($countHearts + $count) >= HEARTS_MAX_COUNT)
            {
                $update = $shardDb->update(
                    'user_hearts',
                    ['hearts_count' => ($countHearts + $count), 'update_date' => time()],
                    ['user_id' => $userId],
                    ['int', 'int'],
                    ['int']
                );

                if ($update)
                {
                    $result = true;
                }
            }
            else
            {
                $time = (int) (time() - (HEARTS_GENERATION_INTERVAL - $heartsData['interval']));

                $update = $shardDb->update(
                    'user_hearts',
                    ['hearts_count' => ($countHearts + $count), 'update_date' => $time],
                    ['user_id' => $userId],
                    ['int', 'int'],
                    ['int']
                );

                if ($update)
                {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /** inc sublevel of current level
     *
     * @param $userId
     * @param $levelId
     * @return bool
     * @throws Exception
     */
    final public function incSublevel($userId, $levelId)
    {
        $sublevels = [];
        $mainDb = $this->getMainDb();
        $shardDb = $this->getShardDb($userId);

        $result = $mainDb->select(
            'dict_sublevel',
            'id',
            ['id_level' => $levelId],
            ['int'],
            'number_sublevel'
        );
        if (!$result)
        {
            throw new Exception("Bad request to DB!");
        }
        while ($row = $result->f('id'))
        {
            $sublevels[] = $row;
        }

        $userSublevel = $shardDb->select(
            'user_level',
            'sublevel_id',
            ['user_id' => $userId, 'level_id' => $levelId],
            ['int', 'int'],
            '',
            1
        );

        $currentSublevel = $userSublevel->f('sublevel_id');

        $sublevel = (isset($sublevels[array_search($currentSublevel, $sublevels)]) && array_search($currentSublevel, $sublevels) < 4 ) ?
            $sublevels[array_search($currentSublevel, $sublevels) + 1] : $sublevels[0];

        if ($sublevel > 0)
        {
            $update = $shardDb->update(
                'user_level',
                ['sublevel_id' => (int) $sublevel],
                ['user_id' => $userId, 'level_id' => $levelId],
                ['int'],
                ['int', 'int']
            );
            if (!$update)
            {
                throw new Exception("Can't inc sublevel for current level!");
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    /** subtract user booster
     *
     * @param $userId
     * @param $boosterId
     * @return bool
     * @throws Exception
     */
    final public function getBooster($userId, $boosterId)
    {
        $shardDb = $this->getShardDb($userId);
        $result = $shardDb->select(
            'user_boosters',
            'booster_count',
            ['user_id' => $userId, 'booster_id' => $boosterId],
            ['int', 'int'],
            '',
        1
        );
        $boosterCount = (int) $result->f('booster_count');
        $boosters = $boosterCount - 1;

        if ($boosters >= 0)
        {
            $update = $shardDb->update(
                'user_boosters',
                ['booster_count' => $boosters],
                ['user_id' => $userId, 'booster_id' => $boosterId],
                ['int'],
                ['int', 'int']
            );
            if (!$update)
            {
                throw new Exception("Can't update data in user_hearts table!");
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    /** verify security key
     *
     * @param string $securityKey
     * @param $appGuid
     * @param $chGuid
     * @param $userSocialId
     * @param $scriptName
     * @return bool
     */
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

    final public function accessToEditor($userId)
    {
        $mainDb = $this->getMainDb();

        $testerData = $mainDb->select(
            'testers',
            'editor_access',
            ['user_id' => $userId],
            ['int'],
            '',
            1
        );

        $access = $testerData->f('editor_access');

        return $access > 0;
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

    /** returns number of daily accepted viral gifts
     *
     * @param $userId
     * @return bool|int
     */
    final public function getDailyAcceptedGifts($userId)
    {
        $shard = $this->getShardDb($userId);
        $today = new \DateTime('now');
        $count = 0;

        $result = $shard->select('user_daily_viral_gifts',
                                 'gifts_count',
                                 ['user_id' => $userId, 'day' => $today->format('Y-m-d 0:0:0')],
                                 ['int', 'str'] );

        if ($result)
        {
            $count = $result->f('gifts_count');
            if (empty($count))
            {
                $count = 0;
            }
        }

        return $count;
    }


    final public function getCurrentUserHearts($userId)
    {
        $shard = $this->getShardDb($userId);
        $count = 0;

        $result = $shard->select('user_hearts',
            'hearts_count',
            ['user_id' => $userId],
            ['int'] );

        if ($result)
        {
            $count = $result->f('hearts_count');
            if (empty($count))
            {
                $count = 0;
            }
        }

        return $count;
    }


    /** returns store pack data by Id
     *
     * @param int $packId
     * @return array|bool
     */
    final function getStorePack($packId = 0)
    {
        $data = false;

        if ($packId > 0)
        {
            $mainDb = $this->getMainDb();

            $result = $mainDb->select(
                'dict_store_packs',
                '*',
                ['pack_id' => $packId, 'deleted' => 0],
                ['int', 'int'],
                '',
                1
            );

            if ($result)
            {
                $data = $result->fetch();
            }
        }

        return $data;
    }

    /** open user island
     *
     * @param $userId
     * @param $islandId
     * @return bool
     */
    final function openIsland($userId, $islandId)
    {
        $result = false;
        $shardDb = $this->getShardDb($userId);
        $mainDb = $this->getMainDb();

        if (!is_null($shardDb))
        {
            $select = $shardDb->select(
                'user_islands_data',
                'count(1) AS tickets',
                ['user_id' => $userId, 'ticket_status' => 0, 'island_id' => $islandId],
                ['int', 'int', 'int'],
                '',
                1
            );

            if ($select->f('tickets') == 3)
            {
                $update = $shardDb->update(
                    'user_islands_data',
                    ['ticket_status' => 1],
                    ['user_id' => $userId, 'island_id' => $islandId],
                    ['int'],
                    ['int', 'int']
                );

                $result = true;

                $mainDb->delete(
                    'user_gifts_ask',
                    ['user_id' => $userId, 'gift_id' => 6],
                    ['int', 'int']
                );

                $mainDb->delete(
                    'user_gifts_send',
                    ['friend_id' => $userId, 'gift_id' => 6],
                    ['int', 'int']
                );
            }
        }

        return $result;
    }

    /** add ticket from friend
     *
     * @param $userId
     * @param $friendId
     * @return bool
     */
    final function addTicket($userId, $friendId)
    {
        $result = false;
        $shardDb = $this->getShardDb($userId);
        $mainDb = $this->getMainDb();

        if (!is_null($shardDb))
        {
            // check if user given a ticket before
            $select = $shardDb->select(
                'user_islands_data',
                'count(1) AS cnt',
                ['user_id' => $userId, 'friend_id' => $friendId],
                ['int', 'int'],
                '',
                1
            );

            if($select->f('cnt') == 0)
            {
                $selectMaxIsland = $shardDb->query("SELECT max(island_id) AS island_id FROM user_islands_data WHERE user_id = '$userId' AND ticket_status = 1;");
                $islandId = (int) ($selectMaxIsland->f('island_id') + 1);

                $select = $shardDb->query("SELECT count(1) AS tickets FROM user_islands_data
                WHERE user_id = '$userId' AND island_id = '$islandId';");

                $ticketsData = $select->fetchObj();
                $tickets = $ticketsData->tickets;

                if ($tickets >= 0 && $tickets < 3)
                {
                    $insert = $shardDb->query("INSERT IGNORE INTO user_islands_data (user_id, friend_id, island_id) VALUES ('$userId', '$friendId', '$islandId');");

                    $delete = $mainDb->delete(
                        'user_gifts_send',
                        ['user_id' => $friendId, 'friend_id' => $userId, 'gift_id' => 6], // ID == 6 for ticket
                        ['int', 'int', 'int']
                    );

                    $selectTickets = $shardDb->query(
                        "SELECT count(1) AS tickets FROM user_islands_data
                            WHERE user_id = '$userId' AND island_id = '$islandId';"
                    );

                    if ($selectTickets->f('tickets') == 3)
                    {
                        $delete = $mainDb->delete(
                            'user_gifts_ask',
                            ['user_id' => $userId, 'gift_id' => 6], // ID == 6 for ticket
                            ['int', 'int']
                        );
                        $delete = $mainDb->delete(
                            'user_gifts_send',
                            ['friend_id' => $userId, 'gift_id' => 6], // ID == 6 for ticket
                            ['int', 'int']
                        );
                    }

                    $result = true;
                }
            }
            //if friend is tester -> ignore if has given a ticket in the past
//            elseif ($this->isTester($friendId))
//            {
//                $select = $shardDb->query("SELECT count(1) AS tickets, island_id AS current_island FROM user_islands_data
//                WHERE user_id = '$userId' AND island_id = (SELECT max(island_id) FROM user_islands_data);");
//
//                $ticketsData = $select->fetchObj();
//                $tickets = $ticketsData->tickets;
//                $islandId = $ticketsData->current_island;
//
//                if ($tickets > 0 && $tickets < 3)
//                {
//                    $select = $shardDb->select(
//                        'user_islands_data',
//                        'count(1) AS finded',
//                        ['user_id' => $userId, 'friend_id' => $friendId, 'island_id' => $islandId],
//                        ['int', 'int', 'int'],
//                        '',
//                        1
//                    );
//
//                    if ($select && $select->f('finded') == 0)
//                    {
//                        $insert = $shardDb->query("INSERT IGNORE INTO user_islands_data (user_id, friend_id, island_id) VALUES ('$userId', '$friendId', '$islandId');");
//
//                        $delete = $mainDb->delete(
//                            'user_gifts_send',
//                            ['user_id' => $friendId, 'friend_id' => $userId, 'gift_id' => 6], // ID == 6 for ticket
//                            ['int', 'int', 'int']
//                        );
//
//                        $delete = $mainDb->delete(
//                            'user_gifts_ask',
//                            ['user_id' => $userId, 'friend_id' => $friendId, 'gift_id' => 6], // ID == 6 for ticket
//                            ['int', 'int', 'int']
//                        );
//
//                        $result = true;
//                    }
//                }
//                if ($tickets == 0 && $islandId == 0)
//                {
//                    $insert = $shardDb->query("INSERT IGNORE INTO user_islands_data (user_id, friend_id, island_id) VALUES ('$userId', '$friendId', '1');");
//
//                    $delete = $mainDb->delete(
//                        'user_gifts_send',
//                        ['user_id' => $friendId, 'friend_id' => $userId, 'gift_id' => 6], // ID == 6 for ticket
//                        ['int', 'int', 'int']
//                    );
//
//                    $delete = $mainDb->delete(
//                        'user_gifts_ask',
//                        ['user_id' => $userId, 'friend_id' => $friendId, 'gift_id' => 6], // ID == 6 for ticket
//                        ['int', 'int', 'int']
//                    );
//
//                    $result = true;
//                }
//                elseif ($islandId > 0 && $tickets == 3)
//                {
//                    $islandId++;
//                    $insert = $shardDb->query("INSERT IGNORE INTO user_islands_data (user_id, friend_id, island_id) VALUES ('$userId', '$friendId', '$islandId');");
//
//                    $delete = $mainDb->delete(
//                        'user_gifts_send',
//                        ['user_id' => $friendId, 'friend_id' => $userId, 'gift_id' => 6], // ID == 6 for ticket
//                        ['int', 'int', 'int']
//                    );
//
//                    $delete = $mainDb->delete(
//                        'user_gifts_ask',
//                        ['user_id' => $userId, 'friend_id' => $friendId, 'gift_id' => 6], // ID == 6 for ticket
//                        ['int', 'int', 'int']
//                    );
//
//                    $result = true;
//                }
//            }
        }

        return $result;
    }

    final function addKey($userId, $friendSocialId)
    {
        $result = false;
        $shardDb = $this->getShardDb($userId);

        if (!is_null($shardDb))
        {
            $select = $shardDb->query("SELECT chest_id, friend_social_id_1, friend_social_id_2, friend_social_id_3, friend_social_id_4, friend_social_id_5 FROM user_chests
                                       WHERE user_id = '$userId' AND status = '0'
                                       AND
                                       (friend_social_id_1 IS NULL OR friend_social_id_2 IS NULL OR
                                        friend_social_id_3 IS NULL OR friend_social_id_4 IS NULL OR
                                        friend_social_id_5 IS NULL) ORDER BY chest_id;" );
            $chestsData = $select->fetchAll();
            if (count($chestsData) > 0)
            {
                foreach ($chestsData as $key => $chest)
                {
                    $friend1 = $chest['friend_social_id_1'];
                    $friend2 = $chest['friend_social_id_2'];
                    $friend3 = $chest['friend_social_id_3'];
                    $friend4 = $chest['friend_social_id_4'];
                    $friend5 = $chest['friend_social_id_5'];

                    if ($friend1 != $friendSocialId && $friend2 != $friendSocialId && $friend3 != $friendSocialId &&
                        $friend4 != $friendSocialId && $friend5 != $friendSocialId)
                    {
                        $field = null;

                        if (is_null($friend1))
                        {
                            $field = 'friend_social_id_1';
                        }
                        elseif (is_null($friend2))
                        {
                            $field = 'friend_social_id_2';
                        }
                        elseif (is_null($friend3))
                        {
                            $field = 'friend_social_id_3';
                        }
                        elseif (is_null($friend4))
                        {
                            $field = 'friend_social_id_4';
                        }
                        elseif (is_null($friend5))
                        {
                            $field = 'friend_social_id_5';
                        }

                        if (!is_null($field))
                        {
                            $shardDb->query("INSERT INTO user_chests
                                            (chest_id, user_id, $field)
                                            VALUES
                                            ('" . $chest['chest_id'] . "', '$userId', '$friendSocialId')
                                            ON DUPLICATE KEY UPDATE $field = '$friendSocialId';");

                            $mainDb = $this->getMainDb();
                            if( $field == 'friend_social_id_5')
                            {
                                $mainDb->query("DELETE
                                                FROM user_gifts_ask
                                                WHERE user_id = '" . $userId . "' AND gift_id = 7 ;");
                                $mainDb->query("DELETE
                                                FROM user_gifts_send
                                                WHERE friend_id = '" . $userId . "' AND gift_id = 7 ;");
                            }
                            $result = true;

                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /** returns true if user is tester
     *
     * @param $userId
     * @return bool
     */
    final function isTester($userId)
    {
        $result = false;

        if ($userId > 0)
        {
            $mainDb = $this->getMainDb();
            $select = $mainDb->select(
                'testers',
                'count(1) AS tester',
                ['user_id' => $userId],
                ['int'],
                '',
                1
            );

            if ($select && $select->f('tester') == 1)
            {
                $result = true;
            }
        }

        return $result;
    }

    final function haveViewerId($userId)
    {
        $result = false;

        if ($userId > 0)
        {
            $mainDb = $this->getMainDb();
            $select = $mainDb->select(
                'testers',
                'viewer_id',
                ['user_id' => $userId],
                ['int'],
                '',
                1
            );

            $viewerId = $select->f('viewer_id');
            if ($select && $viewerId != 0)
            {
                $result = $viewerId;
            }
        }

        return $result;
    }

    /** returns true if user is MEGA tester
     *
     * @param $userId
     * @return bool
     */
    final function isMegaTester($userId)
    {
        $result = false;

        if ($userId > 0)
        {
            $mainDb = $this->getMainDb();
            $select = $mainDb->select(
                'testers',
                'count(1) AS tester',
                ['user_id' => $userId, 'megatester' => 1],
                ['int', 'int'],
                '',
                1
            );

            if ($select && $select->f('tester') == 1)
            {
                $result = true;
            }
        }

        return $result;
    }

    final public function getEmail(){
        return $this->_cfg["support_email"];
    }

    /**
     * @return SocialNetworkInterface
     */
    final public function getSocialNetwork()
    {
        if ($this->_socialNetwork == NULL)
        {
            $socialNetwork = $this->_cfg["sn"]["socialNetworkClass"];
            $this->_socialNetwork = new $socialNetwork($this->_cfg["sn"]);
        }
        return $this->_socialNetwork;
    }
}