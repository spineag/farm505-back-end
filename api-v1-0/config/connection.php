<?php

/**
 * create connection to DB
 */

////// ORIGINAL


$link = mysqli_connect("p:".SERVER,USER,PASSWORD) or die ("Could not connect to MySQL");
mysqli_select_db(DB, $link);
mysqli_query("SET NAMES 'utf8'");