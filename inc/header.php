<?php
define ('C_VERSION','21.09.22');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");  

if (!isset($auth)){
	$auth = is_authed();
}
$auth_login = "";
if ($auth){
	$auth_login=$_SESSION['login'];
}
$document_title = $site_name;
?>
<!DOCTYPE html>
<html lang="en">
<HEAD>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<script src="https://www.w3schools.com/lib/w3.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<TITLE><?php echo $document_title; ?> phpchain</TITLE>
</HEAD>
<BODY CLASS="main">
<!-- outer structure - card within container -->
<div class="w3-container">
	<div class="w3-card">
<!-- header bar -->
<div class="w3-teal titlebar" onClick="javascript:document.location='index.php'">
<span > <?php echo $document_title ?></span> <SPAN style="float:right;"><i>phpchain</i> password vault version <?php echo C_VERSION; ?></A> </SPAN>
</div>
<!-- menu-bar -->
<div class='menubar w3-bar w3-small'>
<?php
if ($auth){
?>
<form action="catlist.php" method="POST" class='butform w3-left w3-ripple' ><button class="w3-btn w3-medium w3-hover-pale-blue butbut" title="add/edit/remove categories"><span class="butspan">Categories</span></button></form>
<form action="password.php" method="POST" class='butform w3-left w3-ripple' ><button class="w3-btn w3-medium w3-hover-pale-blue butbut" title="Change your PHPchain login password"><span class="butspan">Change Password</span></button></form>
<form action="logout.php" method="POST" class='butform w3-right w3-ripple' ><button class="w3-btn w3-medium w3-hover-pale-red butbut" title="log out of PHPchain"><span class="butspan">Logout</span></button></form>
<form action="javascript:void(0);" method="POST" class='butform w3-right' disabled><button class="w3-btn w3-medium butbut" title="Current PHPchain account"><span class="butspan">User: <?php echo $auth_login ;?></span></button></form>
<?php	
} else {
?>
<form action="newlogin.php" method="POST" class='butform w3-left w3-ripple' ><button class="w3-btn w3-medium w3-hover-blue butbut" title="Create a PHPchain account"><span class="butspan">New Login</span></button></form>
<form action="login.php" method="POST" class='butform w3-right w3-ripple' ><button class="w3-btn w3-medium w3-hover-green butbut" title="Login with your PHPchain account"><span class="butspan">Login</span></button></form>
<?php
}
?>
<!-- status bar  -->
<div class="messagebar" id='messagebar'>
		<?php echo status_message() ?>
</div>
<!-- main table  -->
<div id="toppane" class="toppane w3-border-top">
<div id="navpane">
<!-- navigation menu list  -->
<?php 
if ($auth) {
	include ("inc/listmenu.php");
	$catid=NULL;
	if (isset($page) and ($page=="catview")) {
		$catid=gorp("catid");
	}	
	$catid=gorp('catid');
	echo getmenu($_SESSION["id"],$catid);

	echo action_button('New Entry',"entedit.php?itemid=0&catid=$catid","Add new password entry", "w3-hover-pale-green w3-pale-grey  w3-border w3-block");
}
?>
</div> <!-- end navigation menu content -->
<div id="detailpane">
<!-- Here is the detail content -->