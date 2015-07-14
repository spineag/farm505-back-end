<?php
global $cfgs;


////// ORIGINAL


$cfgs = array($_SERVER["SERVER_NAME"] =>
    array("db" => array("host" => getenv('IP'),
        "database" => getenv('C9_USER'),
        "user" => "spineag",
        "pass" => ""),

        "address" => "farm505-spineag.c9.io",
//        "support_email" => "vksupport@joyrocks.com",

        "sn" => array("channel" => 1,
            "socialNetworkClass" => "VKSocialNetwork",
            "local_path" => "local",
            // Social network specific values
            "api_id" => 4677235,//4510768,
//            "api_editor_id" => 4493900,
//            "secret_key" => "UrT1ucQusLCzTUGwwMIb")),
            "secret_key" => ""),

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
