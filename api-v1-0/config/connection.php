<?php

/**
 * create connection to DB
 */

////// ORIGINAL


$link = mysql_pconnect(SERVER,USER,PASSWORD) or die ("Could not connect to MySQL");
mysql_select_db(DB, $link);
mysql_query("SET NAMES 'utf8'");