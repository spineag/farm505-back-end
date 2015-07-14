<?php

////// ORIGINAL

/**
 *  config
 */
define("SERVER", getenv('IP'));
define("DB", 'c9');
define("USER",getenv('C9_USER'));
define("PASSWORD","");
define("DBPORT", "3306");

define("MEMCACHED_HOST","localhost");
define("MEMCACHED_PORT",11211);
define("MEMCACHED_DICT_TIME", 60);

/**
 * game constants
 */
define("HEARTS_MAX_COUNT",5);
define("HEARTS_GENERATION_INTERVAL",300); // seconds
define("START_COINS",1000);               // start number of user's coins
define("START_BB",10);                    // start number of user's birdbucks
define("START_HEARTS",5);                 // start number of user's hearts

/**
 * secret keys
 */
define("GAME_SECRET", "54z7gfdtz97nhzNAkavN");
define("EDITOR_SECRET", "WUipkUud62esAucXIorZ");