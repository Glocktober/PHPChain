<?php
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

$login = get_post("login");
$key = get_post("key");

if (empty($login) AND array_key_exists('login',$_SESSION)) $login = $_SESSION['login'];

if (empty($key)) unset ($key);

if (!array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER)) {
	$ip=$_SERVER["REMOTE_ADDR"];
} else {
	$ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
}
// ip validation
if (filter_var($ip,FILTER_VALIDATE_IP)===false) $ip="0.0.0.0";

$output="";
$error="";

$now = time();

if (isset($login)&&isset($key)) {

	check_csrf();
	// Do DB login, redirect to welcome page.
	$db = sql_conn();

	// Check for $login_lockout_failures failed login attempts in the last $login_lockout_window minutes.
	$result=sql_query($db,
		"select count(name) from loginlog where date > " . ($now-($login_lockout_window*60)) ." and ip = \"$ip\" and outcome = 0");
	
	$row=sql_fetch_row($result);
	if ($row[0]< $login_lockout_failures) {
		$result=sql_query($db,"select id, teststring, iv from user where name = \"$login\"");
		if (sql_num_rows($result)==1) {
			$row=sql_fetch_assoc($result);
			$key=md5($key);
			if (testteststring(trim(decrypt($key,base64_decode($row["teststring"]),base64_decode($row["iv"]))))) {
				// Login log
				sql_query($db,"insert into loginlog values (\"$login\", \"$ip\", \"$now\",1)");
				$id=$row["id"];
				$_SESSION['login'] = $login;
				$_SESSION['id'] = $id;
				$_SESSION['key'] = $key;
				$_SESSION['isauth'] = TRUE;
				session_regenerate_id(TRUE);

				set_status("\"<b>$login</b>\" - has successfully logged on");
				header ("Location: index.php");
				die();
			} else {
				$error="Error: Incorrect credentials";
			}
		} else {
			$error="Error: Incorrect credentials";
		}
	} else {
		$error="Error: Too many failed login attempts. Disabled for $login_lockout_window minutes.";
	}
}

if (!empty($error)) {
	# Failed login attempt
	sql_query($db, "insert into loginlog values (\"$login\", \"$ip\", \"$now\",0)");
	set_error($error);
}

$output.=form_begin($_SERVER["PHP_SELF"],"POST");
$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">\n";
$output.="<TR><TD CLASS=\"plain\" COLSPAN=\"2\">".$error."</TD></TR>\n";
$output.="<TR><TD CLASS=\"plain\" COLSPAN=\"2\">&nbsp;&nbsp;</TD></TR>\n";
$output.="<TR><TD CLASS=\"plain\">Login: </TD><TD CLASS=\"plain\">".input_text("login",30,255,$login)."</TD></TR>\n";
$output.="<TR><TD CLASS=\"plain\">Password: </TD><TD CLASS=\"plain\">".input_passwd("key",30,255)."</TD></TR>\n";
$output.="<TR><TD CLASS=\"plain\" COLSPAN=\"2\" ALIGN=\"RIGHT\">".submit("Login")."</TD></TR>\n";
$output.="</TABLE>\n";
$output.=form_end();

include("inc/header.php");

echo $output;

include("inc/footer.php");

?>
