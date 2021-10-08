<?php
$page="logonexec";
$reqauth=false;
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

check_csrf('login.php');

$login = get_post("login");
$key = get_post("key");

if (empty($login)) unset ($login);
if (empty($key)) unset ($key);

if (!array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER)) {
	$ip=$_SERVER["REMOTE_ADDR"];
} else {
	$ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
}
// ip validation
if (filter_var($ip,FILTER_VALIDATE_IP)===false) $ip="0.0.0.0";

$error="";
$now = time();
sql_conn();
error_log("Ffff $login");
if (isset($login)) strictcookie('chainlogin',$login,0);

if (isset($login) and isset($key)) {
	// Check for $login_lockout_failures failed login attempts in the last $login_lockout_window minutes.
	$result=sql_query($db,
		"select count(name) from loginlog where date > " . ($now-($login_lockout_window*60)) ." and ip = \"$ip\" and outcome = 0");
	
	$row=sql_fetch_row($result);
	if ($row[0]< $login_lockout_failures) {
		$result=sql_query($db,"select id, teststring, iv from user where name = \"$login\"");
		if (sql_num_rows($result)==1) {
			$row=sql_fetch_assoc($result);
			$key=md5($key);
			if (testteststring(decrypt($key,$row["teststring"],base64_decode($row["iv"])))) {
				// Login log
				sql_query($db,"insert into loginlog values ('$login', '$ip', '$now','1')");
				# going to skip reporting errors as the db may be in RO mode
				$id=$row["id"];
				$_SESSION['login'] = $login;
				$_SESSION['id'] = $id;
				$_SESSION['key'] = $key;
				$_SESSION['isauth'] = TRUE;
				session_regenerate_id(TRUE);
				
				strictcookie('chainlogin',$login);

				set_status("\"<b>$login</b>\" - has successfully logged on");
				header ("Location: index.php");
				die();

			} else {
				$error="Error: ($page) Incorrect credentials";
			}
		} else {
			$error="Error: ($page) Incorrect credentials";
		}
	} else {
		$error="Error: ($page) Too many failed login attempts. Disabled for $login_lockout_window minutes.";
	}
} else error_out("Error: Fill out the form", 'login.php');
				
sql_query($db, "insert into loginlog values ('$login', '$ip', '$now','0')");

error_out($error, 'login.php');
?>