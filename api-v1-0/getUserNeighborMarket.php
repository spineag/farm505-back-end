<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $result = $mainDb->query("SELECT level FROM users WHERE id =".$_POST['userId']);
        if ($result) {
            $arr = $result->fetch();
            $level = $arr['level'];
        } else {
            $json_data['id'] = 3;
            throw new Exception("Bad request to DB!");
        }
        $result = $mainDb->query("SELECT * FROM user_neighbor WHERE user_id =".$_POST['userId']);
        if ($result) {
            $arr = $result->fetch();
            if ($arr['last_update'] <> date('j')) {
                $arr2 = [];
                $arrIns = [2, 3, 4, 7, 8, 9];
                $arrInsIds = implode(',', array_values($arrIns));
                $result = $mainDb->query("SELECT id FROM resource WHERE resource_type = 7 AND block_by_level <=".$level." AND id NOT IN (".$arrInsIds.") ORDER BY RAND() LIMIT 1");
                $instrument = $result->fetch();
                if ($instrument['id']) { $arr2[] = $instrument['id']; }

                $result = $mainDb->query("SELECT * FROM data_tree ORDER BY RAND()");
                $t = $result->fetchAll();
                $count = 0;
                foreach ($t as $value => $r) {
                    $tArr = explode('&', $r['block_by_level']);
                    if ($tArr[0] <= $level) {
                        $arr2[] = $r['craft_resource_id'];
                        $count++;
                        if ($count >=2) break;
                    }
                }
   
                $result = $mainDb->query("SELECT id FROM resource WHERE resource_type = 5 AND block_by_level <=".$level." ORDER BY RAND() LIMIT 6");
                $plants = $result->fetchAll();
                foreach ($plants as $value => $p) {
                    $arr2[] = $p['id'];
                }
                for ($i = 0; $i < 6; $i++) {
                    $arr2[]= -1;
                }

                $resultNeighbor = $mainDb->update('user_neighbor',
                ['last_update' => date('j'), 'resource_id1' => $arr2[0], 'resource_id2' => $arr2[1], 'resource_id3' => $arr2[2], 'resource_id4' => $arr2[3], 'resource_id5' => $arr2[4], 'resource_id6' => $arr2[5]],
                ['user_id' => $_POST['userId']],
                ['int', 'int', 'int', 'int', 'int', 'int', 'int'],
                ['int']);
                
                $result = $mainDb->query("SELECT * FROM user_neighbor WHERE user_id =".$_POST['userId']);
                if ($result) {
                    $arr = $result->fetch();
                } else {
                    $json_data['id'] = 3;
                    throw new Exception("Bad request to DB!");
                }
            }
        } else {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = $arr;
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's096';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's097';
    $json_data['message'] = 'bad POST[userSocialId]';
    echo json_encode($json_data);
}
