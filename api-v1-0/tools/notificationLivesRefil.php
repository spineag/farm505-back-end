<?php

require_once __DIR__ . "/../library/crud/Application.php";

$app = Application::getInstance();
$mainDb = $app->getMainDb();
$socailNetwork = $app->getSocialNetwork();

$time = time();
$uids = "";

$usersQuery = $mainDb->query("SELECT * from users");
$users = $usersQuery->fetchAll();

foreach($users as $user)
{
    $userId = $user['user_id'];
    $shardDb = $app->getShardDb($userId);
    $result = $shardDb->select( 'user_hearts',
                                'hearts_count, update_date',
                                ['user_id' => $userId],
                                ['int'] );

    if ($result && $userHearts = $result->fetch())
    {
        $count = $userHearts['hearts_count'];
        $updateDate = $userHearts['update_date'];
        $now = time();
        $interval = 0;

        if ($count < HEARTS_MAX_COUNT)
        {
            $oldHeartsCount = $count;

            $interval = $now - $updateDate;
            $genHearts = floor($interval / HEARTS_GENERATION_INTERVAL);

            $count += $genHearts;

            if($oldHeartsCount < HEARTS_MAX_COUNT && $count >= HEARTS_MAX_COUNT)
            {
                $uids .= $user['user_social_id'].",";
                $count = HEARTS_MAX_COUNT;
                $interval = 0;

                $updateDate = $now - $interval;
                $shardDb->update(
                    'user_hearts',
                    ['hearts_count' => $count, 'update_date' => $updateDate],
                    ['user_id' => $userId],
                    ['int', 'int'],
                    ['int']
                );
            }
        }
    }
}

$result = $socailNetwork->getSocialObject()->api('secure.sendNotification',
                                        array('timestamp'=>time(),
                                              'random'=>rand(1, 10000),
                                              'uids'=>$uids,
                                              'message'=> HEARTS_REFIL_NOTIFICATION) );
echo json_encode($result) . "\n";

?>