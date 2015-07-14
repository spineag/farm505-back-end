<?php
global $cfgs;


////// ORIGINAL


$cfgs = array("hodev.joyrocks.com" =>
    array("db" => array("host" => "hz-web7.joyrocks.com",
        "database" => "hidden_objects_db",
        "user" => "hiddenobject",
        "pass" => "3=5;3!7Y5~FQ~^l"),

        "local_db" => array("host" => "hz-web7.joyrocks.com",
            "database" => "raccoon_ok_local",
            "user" => "hidden_objects_db",
            "pass" => "3=5;3!7Y5~FQ~^l"),

        "address" => "hodev.joyrocks.com",
        "support_email" => "vksupport@joyrocks.com",

        "sn" => array("channel" => 1,
            "socialNetworkClass" => "VKSocialNetwork",
            "local_path" => "local",
            // Social network specific values
            "api_id" => 4677235,//4510768,
            "api_editor_id" => 4493900,
            "secret_key" => "UrT1ucQusLCzTUGwwMIb"),//"54z7gfdtz97nhzNAkavN"),

        "memcached" => array("servers" => array("host" => "localhost",
            "port" => 11211 ),
            "prefix" => "vk_local_test"),

        "redis" => array("host" => "hz-web7",
            "port" => 6379,
            "prefix" => "vk_mf_test",
            "db" => array('game_user_energy' => 0,
                'game_user_xp' => 1, ),
        ),
    ),
);
