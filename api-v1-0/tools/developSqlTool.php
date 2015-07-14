<?php

require_once __DIR__ . "/../library/crud/Application.php";

    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
//    $shardDb = $app->getShardDb(123075);


    $levelObjectsQuery = $mainDb->query("SELECT dict_sublevel.id, dict_sublevel.id_level, dict_sublevel_object.id_object, COUNT( dict_sublevel_object.id_object ) as 'count'
                                         FROM dict_sublevel
                                         INNER JOIN dict_sublevel_object ON dict_sublevel_object.id_sublevel = dict_sublevel.id
                                         WHERE dict_sublevel.number_sublevel =1
                                         GROUP BY  dict_sublevel.id_level , dict_sublevel_object.id_object;");
    $levelObjects = $levelObjectsQuery->fetchAll();
    foreach($levelObjects as $levelObject)
    {
        $mainDb->query("INSERT INTO dict_sublevel_object_count (id_sublevel, id_object, count)
                        VALUES (". $levelObject['id'] .", ". $levelObject['id_object'] .", ". $levelObject['count'] .") ; ");
    }

//    $count = 0;
//    $uniqCount = 0;
//
//
//    echo "<table>
//                <tr>
//                    <th>ID USER   </th>
//                    <th>  ID bad LEVEL</th>
//                </tr>";
//    $userLevelsQuery = $shardDb->query("SELECT user_id, MAX(  level_id ) as 'max' , COUNT(  level_id ) as 'count'
//                                            FROM  user_level
//                                            GROUP BY  user_id");
//    $userLevels = $userLevelsQuery->fetchAll();
//    foreach($userLevels as $level)
//    {
//        if($level['max'] > $level['count'])
//        {
//            $userBADLevelsQuery = $mainDb->query("SELECT id
//                                                      FROM hidden_objects_db.dict_level
//                                                      WHERE id NOT IN ( SELECT level_id
//                                                                        FROM hidden_objects_db_1.user_level
//                                                                        WHERE user_id =".$level['user_id'].")
//                                                            AND id < ".$level['max']." ; ");
//            if($userBADLevelsQuery->num() > 0)
//            {
//                $uniqCount++;
//            }
//            $userBADLevels = $userBADLevelsQuery->fetchAll();
//            foreach($userBADLevels as $badLevel)
//            {
//                    $shardDb->query("INSERT INTO user_level (user_id, level_id, score, sublevel_id, update_date, time_left, objects_count, stars_count)
//                                                      VALUES(". $level['user_id'] .", ". $badLevel['id'] .", 1000, 0, ". time() .", 0, 0, 3) ; ");
//                $count++;
//                echo " <tr>
//                            <td>".$level["user_id"]."</td>
//                            <td>".$badLevel["id"]."</td>
//                           </tr>";
//            }
//        }
//    }
//    echo "</table>";
//    echo "<br/><b> COUNT : ".$count."</b>";
//    echo "<br/><b> COUNT UNIQ : ".$uniqCount."</b>";


//    $objectsArr = array();
//    $backsArr = array();
//    $decorsArr = array();
//
//    $path = __DIR__ . '/../../content/backgrounds/';
//    $dh = opendir($path);
//    while (false !== ($filename = readdir($dh)))
//    {
//        if ($filename != "." && $filename != "..")
//        {
//            $backsArr[] = ["name"=>$filename , "size"=>round((filesize($path.$filename) / 1024) , 2)];
//        }
//    }
//    closedir($dh);
//
//
//    $path = __DIR__ . '/../../content/objects/';
//    $dh = opendir($path);
//    while (false !== ($filename = readdir($dh)))
//    {
//    if ($filename != "." && $filename != "..")
//    {
//        $objectsArr[] = ["name"=>$filename , "size"=>round((filesize($path.$filename) / 1024) , 2)];
//    }
//    }
//    closedir($dh);
//
//
//    $path = __DIR__ . '/../../content/decors/';
//    $dh = opendir($path);
//    while (false !== ($filename = readdir($dh)))
//    {
//    if ($filename != "." && $filename != "..")
//    {
//        $decorsArr[] = ["name"=>$filename , "size"=>round((filesize($path.$filename) / 1024) , 2)];
//    }
//    }
//    closedir($dh);
//
//    echo "<table>
//                <tr>
//                    <th>Size Decor</th>
//                    <th>Size BACKs</th>
//                    <th>Size OBJTs</th>
//                    <th>Size SUMM</th>
//                </tr>";
//
//    $sizeDecs = 0;
//    $sizeObjs = 0;
//    $sizeBack = 0;
//    $sizeSumm = 0;
//
//    $levelDecorQuery = $mainDb->query("SELECT DISTINCT dict_object.id, dict_object.file_name
//                                       FROM dict_sublevel
//                                       INNER JOIN dict_sublevel_object ON dict_sublevel_object.id_sublevel = dict_sublevel.id
//                                       INNER JOIN dict_object ON dict_object.id = dict_sublevel_object.id_object
//                                       WHERE dict_sublevel.id_level < 25  AND dict_sublevel.number_sublevel = 1;");
//    $levelDecors = $levelDecorQuery->fetchAll();
//    foreach($levelDecors as $levelDecor)
//    {
//        foreach($decorsArr as $icon)
//        {
//            if($icon["name"] == $levelDecor["file_name"])
//            {
//                $sizeDecs += $icon["size"];
//            }
//        }
//    }
//    if($sizeDecs != 0)
//    {
//        echo "<td>". $sizeDecs ." KB</td>";
//    }
//    else
//    {
//        echo "<td>not found</td>";
//    }
//
//
//    $levelBackQuery = $mainDb->query("SELECT DISTINCT dict_background.id, dict_background.file_name
//                                      FROM dict_sublevel
//                                      INNER JOIN dict_sublevel_background ON dict_sublevel_background.id_sublevel = dict_sublevel.id
//                                      INNER JOIN dict_background ON dict_background.id = dict_sublevel_background.id_background
//                                      WHERE dict_sublevel.id_level < 25  AND dict_sublevel.number_sublevel = 1;");
//    $levelBacks = $levelBackQuery->fetchAll();
//    foreach($levelBacks as $levelBack)
//    {
//        foreach($backsArr as $icon)
//        {
//            if($icon["name"] == $levelBack["file_name"])
//            {
//                $sizeBack += $icon["size"];
//            }
//        }
//    }
//    if($sizeBack != 0)
//    {
//        echo "<td>". $sizeBack ." KB</td>";
//    }
//    else
//    {
//        echo "<td>not found</td>";
//    }
//
//    $levelObjectQuery = $mainDb->query("SELECT DISTINCT dict_object.id, dict_object.file_name
//                                        FROM dict_sublevel
//                                        INNER JOIN dict_sublevel_object ON dict_sublevel_object.id_sublevel = dict_sublevel.id
//                                        INNER JOIN dict_object ON dict_object.id = dict_sublevel_object.id_object
//                                        WHERE dict_sublevel.id_level < 25  AND dict_sublevel.number_sublevel = 1;");
//    $levelObjects = $levelObjectQuery->fetchAll();
//    foreach($levelObjects as $levelObject)
//    {
//        foreach($objectsArr as $icon)
//        {
//            if($icon["name"] == $levelObject["file_name"])
//            {
//                $sizeObjs += $icon["size"];
//            }
//        }
//    }
//    if($sizeObjs != 0)
//    {
//        echo "<td>". $sizeObjs ." KB</td>";
//    }
//    else
//    {
//        echo "<td>not found</td>";
//    }
//
//    $sizeSumm = $sizeBack + $sizeObjs + $sizeDecs;
//    if($sizeSumm != 0)
//    {
//        echo "<td>". $sizeSumm ." KB</td>";
//    }
//    else
//    {
//        echo "<td>not found on server</td>";
//    }
//
//    echo "</tr>
//    </table>";

//  ADDED LUST LEVEL WITH OUT STARS IF NOT EXIST +-----------------------
//
//$usersMaxLevels = $shardDb->query("SELECT DISTINCT user_id, max(level_id) as level_id FROM user_level GROUP BY user_id");
//$allUsersMaxLevels = $usersMaxLevels->fetchAll();
//
//foreach($allUsersMaxLevels as $userLevel)
//{
//    echo($userLevel['user_id'] . " and level_id : " .  $userLevel['level_id'] . "/n");
//    $queryLevel = $shardDb->query("SELECT * FROM user_level WHERE user_id = ".$userLevel['user_id']." AND level_id = ".$userLevel['level_id']." ");
//    $level = $queryLevel->fetch();
//
//    if($level['stars_count'] != 0)
//    {
//        echo($userLevel['user_id'] . " and level_id : " .  $userLevel['level_id'] . "and stars_count : " .$level['stars_count']. "/n");
//
//        $level_id = $userLevel['level_id'];
//        $level_id ++;
//
//        $dataQuery = $mainDb->query("SELECT id FROM dict_sublevel WHERE id_level = '".$level_id."' LIMIT 1;");
//        $data = $dataQuery->fetchObj();
//        $shardDb->query("INSERT INTO user_level (user_id, level_id, stars_count, update_date, score, sublevel_id)
//                        VALUES ('".$userLevel['user_id']."', '".$level_id."', '0', '" . time() . "', 0, '" . $data->id . "')
//                        ON DUPLICATE KEY UPDATE stars_count = 0, score = 0, update_date = '".time()."' ;");
//    }
//}


// RECALCULATE LEVELS STARS FOR ALL USERS
//    $result = $shardDb->select( 'user_level',
//                                'level_id, score, sublevel_id, time_left, objects_count, stars_count',
//                                ['user_id' => $userId],
//                                ['int'] );
//    $userLevels = $result->fetchAll();
//
//    foreach($userLevels as $userLevel)
//    {
//        $timeLeft = (int)$userLevel['time_left'];
//        $timeLeft = $timeLeft * 1000;
//        $score = $timeLeft * 5 + $userLevel['objects_count'] * 1321;
//        $countObjects = $userLevel['objects_count'];
//        $sublevelId = $userLevel['sublevel_id'];
//        $levelId = $userLevel['level_id'];
//
//        $getLevelStars = $mainDb->select('dict_level',
//                                         'number, star1, star2, star3, endlife',
//                                         ['id' => $levelId],
//                                         ['int'],
//                                         '',
//                                         1);
//        $star = $getLevelStars->fetch();
//
//        $nextNumber = (int) $star['number'] + 1;
//        $endLife = (int) $star['endlife'];
//        $star1 = (int) $star['star1'];
//        $star2 = (int) $star['star2'];
//        $star3 = (int) $star['star3'];
//
//        $getLevelType = $mainDb->select('dict_level',
//                                        'id_level_type',
//                                        ['id' => $levelId],
//                                        ['int'],
//                                        '',
//                                        1);
//        $levelType = (int) $getLevelType->f('id_level_type');
//
//        $countStars = 0;
//        if ($levelType == 1 || $levelType == 3)
//        {
//            if ($timeLeft > ($endLife - $star1))
//            {
//                $countStars = 3;
//            }
//            elseif ($timeLeft > ($endLife - $star2))
//            {
//                $countStars = 2;
//            }
//            else
//            {
//                $countStars = 1;
//            }
//        }
//        elseif ($levelType == 2 || $levelType == 4)
//        {
//            if ($countObjects >= $star3)
//            {
//                $countStars = 3;
//            }
//            elseif ($countObjects >= $star2)
//            {
//                $countStars = 2;
//            }
//            else
//            {
//                $countStars = 1;
//            }
//        }
//
//        echo("user : ".$userId." LevelId : ".$levelId." starsCount : ".$countStars." old stars count : ".$userLevel['stars_count']." score : ".$score);
//
//        if($userLevel['stars_count'] <= $countStars)
//        {
//            $update = $shardDb->update( 'user_level',
//                                        ['score' => $score, 'update_date' => time(), 'stars_count' => $countStars],
//                                        ['user_id' => $userId, 'level_id' => $levelId],
//                                        ['int', 'int', 'int'],
//                                        ['int', 'int']
//            );
//        }
//        else
//        {
//            $update = $shardDb->update( 'user_level',
//                                        ['score' => $score],
//                                        ['user_id' => $userId, 'level_id' => $levelId],
//                                        ['int'],
//                                        ['int', 'int']
//            );
//        }
//    }
//}