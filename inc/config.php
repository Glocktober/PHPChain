<?php

$env_list = getenv();
function env_get($name, $default){
    global $env_list;
    
    if (array_key_exists($name,$env_list)) return $env_list[$name];
    else return $default;
}
#
# Database selection
#  valid coices are 'SQLITE3' or 'MYSQL'
$db_choice = env_get('db_choice','SQLITE3');  
$db_log_queries = env_get('db_log_queries', false);        

# TZone
$time_zone = env_get('TZONE','America/New_York');

# Session name
$session_name = env_get('passcookie','passchain');
$session_lifetime = env_get('session_lifetime',28800);
# 
# mysql options
$mysql_host = env_get('mysql_host','localhost');
$mysql_username = env_get('mysql_username','chain');
$mysql_password = env_get('mysql_password','ch@1n');
$mysql_db_name = env_get('mysql_db_name','chain');

#
# sqlite options
$sqlite_db_path = env_get('sqlite_db_path','../test/test.db');

#
# login lockout
$login_lockout_failures = 5;
$login_lockout_window = 10;
$allow_new_accounts = true;

# Required min password length
$min_password_length = 8;

# force logout on CSRF error
$csrf_force_logout = true;

# log system status messages
$stat_log = env_get('log_status_messages',false);

# site name
$site_name = $_SERVER['SERVER_NAME'];

# Automatic uplift to TLS
if ($_SERVER["HTTPS"]!="on") {
	header('Location: ' . "https://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"] );
	die();
}

$is_get = $_SERVER['REQUEST_METHOD'] == 'GET';

# set time zone
date_default_timezone_set($time_zone);

# setup session
session_name($session_name);
session_set_cookie_params($session_lifetime, '/;SameSite=strict', null , True, True);
session_start();
include ("inc/sessions.php");

# select correct database driver
if ($db_choice == 'SQLITE3'){
    include ('inc/sqlite_db.php');
} elseif ($db_choice == 'MYSQL'){
    include ('inc/mysqli_db.php');
} else {
    echo "Fatal error: DATABASE NOT CONFIGURED";
    die();
}

?>