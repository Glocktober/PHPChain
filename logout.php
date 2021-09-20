<?php
include ("inc/config.php");
include("inc/form.php");

$login = "";
if (array_key_exists('login',$_SESSION)) $login = $_SESSION['login'];

if (array_key_exists('error_message',$_SESSION)) $error = $_SESSION['error_message'];

session_unset();
session_regenerate_id(TRUE);
$_SESSION['login'] = $login;

if(isset($error)) set_error($error);
else set_status("Logout for \"<b>$login</b>\" complete");

header('Location: index.php');
die();

?>
