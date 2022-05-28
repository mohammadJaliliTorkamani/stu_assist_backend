<?php

ini_set('display_errors', 'On');

error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}


//database connection config
$dbHost = 'localhost';
$dbUser = 'stuassis_root';
$dbPass = '&D93f6qe1';
$dbName = 'stuassis_db';

// setting up the web root and server root
$thisFile = str_replace('\\', '/', __FILE__);
$docRoot = $_SERVER['DOCUMENT_ROOT'];

$webRoot = str_replace(array($docRoot, 'config.php'), '', $thisFile);
$srvRoot = str_replace('config.php', '', $thisFile);

define('WEB_ROOT', $webRoot);
define('SRV_ROOT', $srvRoot);

require_once 'database.php';

/*
* End of file config.php
*/
