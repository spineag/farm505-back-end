<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/Application.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/php/api-v1-0/library/defaultResponseJSON.php');

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $app = Application::getInstance();
    $mainDb = $app->getMainDb();
    $channelId = 1; // VK

    try {
        $time = time();
        // $result = $mainDb->update(
        //     'user_papper_buy',
        //     ['resource_id' => $_POST['resourceId'], 'resource_count' => $_POST['resourceCount'], 'xp' => $_POST['xp'], 'cost' => $_POST['cost'], 'time_to_new' => $time, 'visible' => $_POST['visible']],
        //     ['user_id' => $_POST['userId'], 'buyer_id' => $_POST['buyerId']],
        //     ['int', 'int', 'int', 'int', 'int', 'int'],
        //     ['int', 'int']);
        $result = $mainDb->query('UPDATE user_papper_buy SET resource_id='.$_POST['resourceId'].', resource_count='.$_POST['resourceCount'].', xp='.$_POST['xp'].', cost='.$_POST['cost'].', time_to_new='.$time.', visible='.$_POST['visible'].', type_resource ='.$_POST['typeResource'].' WHERE user_id='.$_POST['userId'].' AND buyer_id='.$_POST['buyerId']);
        if (!$result) {
            $json_data['id'] = 2;
            throw new Exception("Bad request to DB!");
        }

        $json_data['message'] = '';
        echo json_encode($json_data);
    }
    catch (Exception $e)
    {
        $json_data['status'] = 's186';
        $json_data['message'] = $e->getMessage();
        echo json_encode($json_data);
    }
}
else
{
    $json_data['id'] = 1;
    $json_data['status'] = 's187';
    $json_data['message'] = 'bad POST[userId]';
    echo json_encode($json_data);
}