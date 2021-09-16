<?php
include ("inc/sessions.php");
include ("inc/db.php");
include ("inc/form.php");
include ("inc/crypt.php");

$page="password";

sql_conn();

$auth = is_authed();

if (!$auth) {
	header("Location: index.php");
	die();
}

$newkey=gorp("newkey");
$newkey2=gorp("newkey2");
$complete=gorp("complete");

if (empty($complete)) $complete=FALSE;

$output="<P CLASS=\"plain\">";
$error="";

if (!empty($newkey)&&!empty($newkey2)) {

	check_csrf();
	
	if ($newkey!=$newkey2) $error.="<SPAN CLASS=\"error\">The passwords you have entered do not match.</SPAN><BR>\n";
	if (strlen($newkey)<6) $error.="<SPAN CLASS=\"error\">Password must be at least 6 characters long.</SPAN><BR>\n";

	if (empty($error)) {

		$key = $_SESSION['key'];
		$userid = $_SESSION['id'];
		$login = $_SESSION['login'];

		$newkey=md5($newkey);

		// Create new entry in user table.
		$iv=make_iv();
		$teststring=base64_encode(encrypt($newkey,maketeststring(),$iv));
		$iv=base64_encode($iv);

		$result=mysqli_query($db, "insert user values (NULL, \"$login\", \"$teststring\", \"$iv\")");
		$id=mysqli_insert_id($db);

		$result=mysqli_query($db, "select id, iv, catid, login, password, site, url from logins where userid = \"$userid\"");

		while ($row=mysqli_fetch_assoc($result)) {
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
			mysqli_query($db, "insert logins values (NULL, \"$iv\", \"$id\", \"$catid\", \"$login\", \"$password\", \"$site\", \"$url\")");
		}

		// DB cleanup.

		mysqli_query($db, "update cat set userid = \"$id\" where userid = \"$userid\"");
		mysqli_query($db, "delete from logins where userid = \"$userid\"");
		mysqli_query($db, "delete from user where id = \"$userid\"");

		$_SESSION['id'] = $id;
		$_SESSION['key'] = $newkey;

		header("Location: ".$_SERVER["PHP_SELF"]."?complete=TRUE");
		die();
	}
}

if (!$complete) {
	if (!empty($error)) {
		$output.="The following error(s) occured:<P>\n";
		$output.=$error."<P>";
	}
	$output.="<P CLASS=\"plain\">Changing your password requires all data in the database under your login to be decrypted and re-encrypted. This process can take some time if you have a lot of entries. Do not hit stop or close your browser after entering your new password, or data loss may occur.";
	$output.="<P CLASS=\"plain\">Password must be at least 6 characters long";
	$output.="<P>";
	$output.=form_begin($_SERVER["PHP_SELF"],"POST");
	$output.=input_hidden("action","save");
	$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">\n";
	$output.="<TR><TD CLASS=\"plain\">New Password: </TD><TD CLASS=\"plain\">".input_passwd("newkey",20,255)."</TD></TR>\n";
	$output.="<TR><TD CLASS=\"plain\">Verify new password: &nbsp;&nbsp;</TD><TD CLASS=\"plain\">".input_passwd("newkey2",20,255)."</TD></TR>\n";
	$output.="<TR><TD CLASS=\"plain\" COLSPAN=\"2\" ALIGN=\"RIGHT\">".submit("Change password")."</TD></TR>\n";
	$output.="</TABLE>\n";
	$output.=form_end();
} else {
	$output.="<SPAN CLASS=\"plain\">Password changed!</SPAN>";
}



include ("inc/header.php");

echo $output;

include ("inc/footer.php");
?>
