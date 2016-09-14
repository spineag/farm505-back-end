<?php
global $cfgs;


////// ORIGINAL


$cfgs = array(
    '1' =>  array(
//                 "db" => array("host" => '148.251.121.199', //getenv('IP'),
//                                                    "database" => 'farm',
//                                                    "user" => "root",
//                                                    "pass" => ""),

                 "address" => "505.ninja",
//        "support_email" => "vksupport@joyrocks.com",

                 "sn" => array("channel" => 1,
                               "socialNetworkClass" => "VKSocialNetwork",
                               "local_path" => "local",
            // Social network specific values
                               "api_id" => 5448769, 
//            "api_editor_id" => 4493900,
                               "secret_key" => "pbJkDGDmNCcheNo6dZDe")

//        "memcached" => array("servers" => array("host" => "localhost",
//            "port" => 11211 ),
//            "prefix" => "vk_local_test"),
//
//        "redis" => array("host" => "hz-web7",
//            "port" => 6379,
//            "prefix" => "vk_mf_test",
//            "db" => array('game_user_energy' => 0,
//                'game_user_xp' => 1, ),
        ),
);
