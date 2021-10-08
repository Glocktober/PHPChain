<?php
$page="newloginexec";
$reqauth=false; # handle this later in policy checks

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

check_csrf();

# Policy check
if (!$allow_new_accounts) 
    error_out("Error: <B>New accounts are disabled</b>: contact your administrator.");
elseif ($auth_for_new_accounts AND !is_authed())  
    error_out("Error: <b>Authentication required</b> to create an account");

# parameter check
$login = get_post('login');
$key = get_post('key');
$key2 = get_post('key2');

if (!$login)
    error_out("Error: ($page) Login name is invalid");
elseif (strlen($key)<$min_password_length) 
    error_out("Error: ($page) Password must be at least $min_password_length characters",'newlogin.php');
elseif ($key!=$key2) 
    error_out("Error: ($page) The passwords you have entered do not match",'newlogin.php');

sql_conn();

$result=sql_query($db,"select id from user where name = \"$login\"");
if (sql_num_rows($result)!=0) 
    error_out("Error: Login \"$login\" already exists. Please choose another",'newlogin.php');

# all is in order, proceed
$iv=make_iv();
$key=md5($key);
$teststring=encrypt($key,maketeststring(),$iv);
$iv=base64_encode($iv);

# Add the user
if (!sql_query($db,"insert into user values (NULL, \"$login\", \"$teststring\", \"$iv\")"))
    error_out("Error: ($page) database error adding \"$login\": ".sql_error($db), 'newlogin.php');

$_SESSION['login'] = $login;
$_SESSION['isauth'] = FALSE;
strictcookie('chainlogin',$login,0);
set_status("The account \"$login\" has been created - please login");
header ("Location: login.php");