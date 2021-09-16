<?php
include ("inc/sessions.php");
include ("inc/db.php");
include ("inc/crypt.php");
include ("inc/form.php");

$login = 0;
if (array_key_exists('login',$_POST)){
$login=$_POST["login"];
}
$key=0;
if (array_key_exists('key',$_POST)){
$key=$_POST["key"];
}
$key2=0;
if (array_key_exists('key2',$_POST)){
$key2=$_POST["key2"];
}

if (empty($login)) unset ($login);

$output="";
$error="";

if (isset($login)) {

	check_csrf();
	
	// check values
	if ($key!=$key2) $error.="<SPAN CLASS=\"error\">The passwords you have entered do not match.</SPAN><BR>\n";
	if (strlen($key)<6) $error.="<SPAN CLASS=\"error\">Password must be at least 6 characters long.</SPAN><BR>\n";

	// Login already exist?

	sql_conn();

	$result=mysqli_query($db,"select id from user where name = \"$login\"");
	if (mysqli_num_rows($result)!=0) {
		$error.="<SPAN CLASS=\"error\">Login name already exists. Please choose another.</SPAN><BR>\n";
	}

	if (empty($error)) {
		$iv=make_iv();
		$key=md5($key);
		$teststring=base64_encode(encrypt($key,maketeststring(),$iv));
		$iv=base64_encode($iv);

		$result=mysqli_query($db,"insert user values (NULL, \"$login\", \"$teststring\", \"$iv\")");
		$id=mysqli_insert_id($db);

		$_SESSION['login'] = $login;
		$_SESSION['isauth'] = FALSE;
		
		header ("Location: index.php");
		die();
	}
}

if (!empty($error)) {
	$output.="The following error(s) occured:<P>\n";
	$output.=$error."<P>";
}
$output.="<P CLASS=\"plain\">Password must be at least 6 characters long";
$output.="<P>";
$output.=form_begin($_SERVER["PHP_SELF"],"POST");
$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">\n";
$output.="<TR><TD CLASS=\"plain\">New Login: </TD><TD CLASS=\"plain\">".input_text("login",20,255,$login)."</TD></TR>\n";
$output.="<TR><TD CLASS=\"plain\">Password: </TD><TD CLASS=\"plain\">".input_passwd("key",20,255)."</TD></TR>\n";
$output.="<TR><TD CLASS=\"plain\">Verify password: &nbsp;&nbsp;</TD><TD CLASS=\"plain\">".input_passwd("key2",20,255)."</TD></TR>\n";
$output.="<TR><TD CLASS=\"plain\" COLSPAN=\"2\" ALIGN=\"RIGHT\">".submit("Create login")."</TD></TR>\n";
$output.="</TABLE>\n";
$output.=form_end();

include("inc/header.php");
echo $output;
include("inc/footer.php");

?>
