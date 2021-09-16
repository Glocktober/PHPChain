<?php
include("inc/sessions.php");
include("inc/form.php");

$output = "<p style=\"color:black\">";
if (isset($_GET['error'])){
    $output .= "<span style=\"color:red\">". $_GET['error'] . "</span><br>";
}

$login = $_SESSION['login'];

session_unset();
session_regenerate_id(TRUE);
$_SESSION['login'] = $login;

$output .= "Logout completed.</p>";
include ("inc/header.php");

echo $output;

include ("inc/footer.php");
?>
