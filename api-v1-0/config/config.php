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
define("DEFAULT_AMBAR_MAX",50);
define("DEFAULT_SKLAD_MAX",50);
define("DEFAULT_AMBAR_LEVEL",1);
define("DEFAULT_SKLAD_LEVEL",1);
define("DEFAULT_HARD_COUNT",50);
define("DEFAULT_SOFT_COUNT",1000);
define("DEFAULT_RED_COUNT",1);
define("DEFAULT_YELLOW_COUNT",1);
define("DEFAULT_GREEN_COUNT",1);
define("DEFAULT_BLUE_COUNT",1);

/**
 * secret keys
 */
define("GAME_SECRET", "");
define("EDITOR_SECRET", "");