<?php
define('SN', 'vk');
require_once '../library/Application.php';

$mainDb = Application::getInstance()->getMainDb(2);
//$snCfg = Application::getInstance()->getSnCfg();

header("Content-Type: application/json; encoding=utf-8");

$secret_key = 'pbJkDGDmNCcheNo6dZDe';

$ar = array();
$time = time();
$input = $_POST;
if ($input['item_id'] == 13) {
    $db_r = $mainDb->query('SELECT * FROM data_starter_pack');
    if ($db_r) {
        $arr = $db_r->fetchAll();
        foreach ($arr as $value => $dict) {
            $res = [];
            $res['item_id'] = 13;
            $res['soft_count'] = $dict['soft_count'];
            $res['hard_count'] = $dict['hard_count'];

            $resp[] = $res;
        }
    }

} else {
    $db_r = $mainDb->query('SELECT * FROM data_buy_money');
    while ($r = $db_r->fetch($db_r)) {
//    if ($r['start_action'] > $time || $r['finish_action'] < $time)
//    {
//        $r['action'] = 0;
//    }
//    if (empty($r['version']))
//    {
//        $r['version'] = 0;
//    }
        $r['item_name'] = 'item_' . $r['id'];
        $ar[] = $r;
    }

    $input = $_POST;
// Проверка подписи
    $sig = $input['sig'];
    unset($input['sig']);
    ksort($input);
    $str = '';
    foreach ($input as $k => $v) {
        $str .= $k . '=' . $v;
    }

    if ($sig != md5($str . $secret_key)) {
        $response['error'] = array(
            'error_code' => 10,
            'error_msg' => 'Несовпадение вычисленной и переданной подписи запроса.',
            'critical' => true
        );
    } else {
        // Подпись правильная
        switch ($input['notification_type']) {
            case 'get_item':
            case 'get_item_test':
                // Формируем текст "МОНЕТ", Рубинов
                if ($input['notification_type'] == 'get_item_test') {
                    $realStr = "РУБИНОВ (тестовый режим)";
                    $virtStr = "МОНЕТ (тестовый режим)";
                } else {
                    $realStr = "РУБИНОВ";
                    $virtStr = "МОНЕТ";
                }
                // Получение информации о товаре
                $item = $input['item']; // наименование товара
                $isFound = false;
                foreach ($ar as $v) {
                    if ($item == $v['item_name']) {
                        $isFound = true;
                        $response['response'] = array(
                            'item_id' => $v['id'],
                            'title' => 'уопача ' + $v['count_getted'],
                            'photo_url' => $v['url'],
                            'price' => $v['cost_for_real'],
                        );
                        break;
                    }
                }
                if ($notFound) {
                    $response['error'] = array(
                        'error_code' => 20,
                        'error_msg' => 'Товара не существует.',
                        'critical' => true
                    );
                }
                break;
            case 'order_status_change':
            case 'order_status_change_test':
                // Изменение статуса заказа
                if ($input['status'] == 'chargeable') {
                    $order_id = intval($input['order_id']);

                    // Код проверки товара, включая его стоимость
                    $app_order_id = 0; // Получающийся у вас идентификатор заказа.
                    $error = 0;

                    $object_id = $input['item_id'];

                    $itemArray = explode("_", $input['item']);
                    //  $itemArray[0] это item или offer

//                if ($itemArray[0] == "offer")
//                {
//                    $r['count']     = $input['item_price'];
//                    $r['price']     = $input['item_price'];
//                    $r['type']      = 'real';
//                    $r['object_id'] = $itemArray[1];
//
//                    $callbackHelper = new callbackHelper();
//                    $callbackHelper->offer = $r;
//                    $callbackHelper->social_id = $input['user_id'];
//                    $callbackHelper->input_price = $input['item_price'];
//
//                    $result = $callbackHelper->updateResource();
//                }
//                else
//                {
//                    $callbackHelper = new callbackHelper();
//                    $callbackHelper->object_id = $object_id;
//                    $callbackHelper->social_id = $input['user_id'];
//                    $callbackHelper->input_price = $input['item_price'];
//
//                    $result = $callbackHelper->updateResource();
//                }
                    $result = true;

                    if ($result === true) {
                        $response['response'] = array(
                            'order_id' => $order_id,
                            'app_order_id' => $app_order_id,
                        );
                    } else {
                        $data = '[E' . $result . '] - SID: ' . $input['user_id'] . ', ITEM: ' . $input['item'] . ', ITEM_ID: ' . $input['item_id'] . ', PRICE: ' . $input['item_price'] . ', ITEM_CURRENCY_AMOUNT: ' . $input['item_currency_amount'] . ";\r\n";

                        //callbackHelper::errorLog("../error/error_payment.log", $data);

                        $response['error'] = array(
                            'error_code' => $error,
                            'error_msg' => '',
                            'critical' => true
                        );
                    }
                } else {
                    $response['error'] = array(
                        'error_code' => 100,
                        'error_msg' => 'Передано непонятно что вместо chargeable.',
                        'critical' => true
                    );
                }
                break; // order_status_change && order_status_change_test
        }
    }
//if ($input['item_id'] == 12) {
//    $json_data['id'] = 12;
//    $json_data['status'] = 's221';
//    $json_data['message'] = 'Lolololololol';
//    echo json_encode($json_data);
//}
}
echo json_encode($response);
