<?php
include ("inc/config.php");
include ("inc/crypt.php");
include ("inc/form.php");
 
$login = get_post('login');
$key = get_post('key');
$key2 = get_post('key2');

$output="";
$error="";

if ($is_get) set_status('Creating a New Login');

if ($allow_new_accounts and isset($login)) {

	check_csrf();

	sql_conn();

	// check values
	if (strlen($key)<$min_password_length) $error ="Error: Password must be at least $min_password_length characters long";
	elseif ($key!=$key2) $error="Error: The passwords you have entered do not match";
	
	if (empty($error)){
		$result=sql_query($db,"select id from user where name = \"$login\"");
		if (sql_num_rows($result)!=0) {
			$error="Error: Login \"$login\" already exists. Please choose another";
		}
	}

	if (empty($error)) {
		$iv=make_iv();
		$key=md5($key);
		$teststring=base64_encode(encrypt($key,maketeststring(),$iv));
		$iv=base64_encode($iv);

		$result=sql_query($db,"insert into user values (NULL, \"$login\", \"$teststring\", \"$iv\")");
		$id=sql_insert_id($db);

		$_SESSION['login'] = $login;
		$_SESSION['isauth'] = FALSE;
		
		set_status("The account \"$login\" has been created - please login");
		header ("Location: login.php");
		die();
	}
}

if (!empty($error))  set_error($error);
if ($allow_new_accounts){

	$output.="<P CLASS=\"plain\">Password must be at least $min_password_length characters long";
	$output.="<P>";
	$output.=form_begin($_SERVER["PHP_SELF"],"POST");
	$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">\n";
	$output.="<TR><TD CLASS=\"plain\">New Login: </TD><TD CLASS=\"plain\">".input_text("login",20,255,$login, 'plain focus')."</TD></TR>\n";
	$output.="<TR><TD CLASS=\"plain\">Password: </TD><TD CLASS=\"plain\">".input_passwd("key",20,255)."</TD></TR>\n";
	$output.="<TR><TD CLASS=\"plain\">Verify password: &nbsp;&nbsp;</TD><TD CLASS=\"plain\">".input_passwd("key2",20,255)."</TD></TR>\n";
	$output.="<TR><TD CLASS=\"w3-center\" COLSPAN=\"2\" ALIGN=\"RIGHT\">".submit("Create login",'',"Create the account","w3-border w3-hover-pale-green")."</TD></TR>\n";
	$output.="</TABLE>\n";
	$output.=form_end();
} else{
	$output.="<h2 class=info >New account creation has been disabled.</h2>";
}

include("inc/header.php");

echo $output;

include("inc/footer.php");

?>
