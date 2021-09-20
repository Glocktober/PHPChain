<?php
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

$page="password";

sql_conn();

$auth = is_authed();

if (!$auth) {
	header("Location: index.php");
	die();
}

$login = $_SESSION['login'];

if ($is_get) set_status("Updating Password for \"<b>$login</b>\": Passwords must be at least $min_password_length long");

$complete=gorp("complete");

$newkey = get_post('newkey');
$newkey2 = get_post('newkey2');

if (empty($complete)) $complete=FALSE;

$output="<P CLASS=\"plain\">";
$error="";

if (!empty($newkey)&&!empty($newkey2)) {

	check_csrf();
	
	if (strlen($newkey)<$min_password_length) $error.="Error: Password must be at least $min_password_length characters long";
	elseif ($newkey!=$newkey2) $error="Error: The passwords you have entered do not match.";

	if (empty($error)) {

		$key = $_SESSION['key'];
		$userid = $_SESSION['id'];
		$login = $_SESSION['login'];

		$newkey=md5($newkey);

		// Create new entry in user table.
		$iv=make_iv();
		$teststring=base64_encode(encrypt($newkey,maketeststring(),$iv));
		$iv=base64_encode($iv);

		$result=sql_query($db, "insert into user values (NULL, \"$login\", \"$teststring\", \"$iv\")");
		$id=sql_insert_id($db);

		$result=sql_query($db, "select id, iv, catid, login, password, site, url from logins where userid = \"$userid\"");

		while ($row=sql_fetch_assoc($result)) {
			$login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
			$password=trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"])));
			$site=trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"])));
			$url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
			$catid=$row["catid"];

			$iv=make_iv();
			$login=base64_encode(encrypt($newkey,$login,$iv));
			$password=base64_encode(encrypt($newkey,$password,$iv));
			$site=base64_encode(encrypt($newkey,$site,$iv));
			$url=base64_encode(encrypt($newkey,$url,$iv));
			$iv=base64_encode($iv);
			sql_query($db, "insert into logins values (NULL, \"$iv\", \"$id\", \"$catid\", \"$login\", \"$password\", \"$site\", \"$url\")");
		}

		// DB cleanup.

		sql_query($db, "update cat set userid = \"$id\" where userid = \"$userid\"");
		sql_query($db, "delete from logins where userid = \"$userid\"");
		sql_query($db, "delete from user where id = \"$userid\"");

		$_SESSION['id'] = $id;
		$_SESSION['key'] = $newkey;

		header("Location: ".$_SERVER["PHP_SELF"]."?complete=TRUE");
		die();
	}
}

if (!$complete) {
	if (!empty($error)) {
		set_error($error);
	} 
	$output.=form_begin($_SERVER["PHP_SELF"],"POST");
	$output.=input_hidden("action","save");
	$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">\n";
	$output.="<TR><TD CLASS=\"plain\">New Password: </TD><TD CLASS=\"plain\">".input_passwd("newkey",20,255)."</TD></TR>\n";
	$output.="<TR><TD CLASS=\"plain\">Verify new password: &nbsp;&nbsp;</TD><TD CLASS=\"plain\">".input_passwd("newkey2",20,255)."</TD></TR>\n";
	$output.="<TR><TD CLASS=\"plain\" COLSPAN=\"2\" ALIGN=\"RIGHT\">".submit("Change password")."</TD></TR>\n";
	$output.="</TABLE>\n";
	$output.=form_end();
} else {
	$output.="<h2 CLASS=\"success\">Password changed!</h2>";
	set_status( "Password for \"<b>login</b>\" has been updated.");
}

include ("inc/header.php");

echo $output;

include ("inc/footer.php");
?>
