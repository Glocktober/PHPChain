<?php
#
# Database selection
#  valid coices are 'SQLITE3' or 'MYSQL'
$db_choice = 'SQLITE3';          

# TZone
$time_zone = 'America/New_York';

# Session name
$session_name = 'passchain';
$session_lifetime = 28800;
# 
# mysql options
$mysql_host = 'localhost';
$mysql_username = 'chain';
$mysql_password = 'ch@1n';
$mysql_db_name = 'chain';

#
# sqlite options
$sqlite_db_path = '../test/test.db';

#
# login lockout
$login_lockout_failures = 5;
$login_lockout_window = 10;
$allow_new_accounts = true;

# Required min password length
$min_password_length = 8;

# force logout on CSRF error
$csrf_force_logout = true;

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