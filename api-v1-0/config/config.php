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
 * secret keys
 */
define("GAME_SECRET", "");
define("EDITOR_SECRET", "");