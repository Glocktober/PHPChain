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
$login = "";
if ($auth){
	$login=$_SESSION['login'];
}
$document_title = $site_name;
?>
<!DOCTYPE html>
<html lang="en">
<HEAD>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
<TITLE><?php echo $document_title; ?> phpchain</TITLE>
</HEAD>
<BODY CLASS="main">
<div class=w3-container>
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%" id="toptable" class="w3-table w3-bordered w3-borer">
<TR class="w3-container w3-teal">
<TD COLSPAN="2" CLASS="titlebar" onClick="javascript:document.location='index.php'" style="padding-left:5%;">
<span style=";"> <?php echo $document_title ?></span> <SPAN style="margin-right:5%;float:right;"><i>phpchain</i> password vault version <?php echo C_VERSION; ?></A> </SPAN>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</TD>
</TR>
<TR class="w3-white">
<TD COLSPAN="2" CLASS="menubar" WIDTH="100%" >
		<div class="w3-bar">
<?php
if ($auth){
?>
<form action="settings.php" method="POST" class='butform w3-left w3-ripple' ><button class="w3-btn w3-medium w3-hover-pale-blue butbut" title="add/edit/remove categories"><span class="butspan">Edit Categories</span></button></form>
<form action="password.php" method="POST" class='butform w3-left w3-ripple' ><button class="w3-btn w3-medium w3-hover-pale-blue butbut" title="Change your PHPchain login password"><span class="butspan">Change Password</span></button></form>
<form action="logout.php" method="POST" class='butform w3-right w3-ripple' ><button class="w3-btn w3-medium w3-hover-pale-red butbut" title="log out of PHPchain"><span class="butspan">Logout</span></button></form>
<form action="javascript:void(0);" method="POST" class='butform w3-right' disabled><button class="w3-btn w3-medium butbut" title="Current PHPchain account"><span class="butspan">User: <?php echo $login ;?></span></button></form>
<?php	
} else {
?>
<form action="newlogin.php" method="POST" class='butform w3-left w3-ripple' ><button class="w3-btn w3-medium w3-hover-blue butbut" title="Create a PHPchain account"><span class="butspan">New Login</span></button></form>
<form action="login.php" method="POST" class='butform w3-right w3-ripple' ><button class="w3-btn w3-medium w3-hover-green butbut" title="Login with your PHPchain account"><span class="butspan">Login</span></button></form>
<?php
}
?>
</div>
</TD>
</TR>
<tr class="w3-hover-pale-yellow"><td  COLSPAN="2" width="100%" class=messagebar><div class="w3-medium w3-center"><?php echo status_message() ?></div></td></tr>
<TR>
<TD WIDTH="120" style="vertical-align: top">
<TABLE  CELLPADDING="0" CELLSPACING="0" WIDTH="100%" id=menutable > 
	<!-- menu wrapper table -->
<TR><TD WIDTH="100%" CLASS="cats">&nbsp;</TD></TR>
<?php
if ($auth) {
	include ("inc/listmenu.php");
	$catid=NULL;
	if (isset($page) and ($page=="cat")) {
		$catid=gorp("catid");
	}	
	echo getmenu($_SESSION["id"],$catid);

	echo action_button('New Entry',"cat.php?action=edit&itemid=0&catid=$catid","Add new password entry", "w3-hover-pale-green w3-pale-grey w3-border w3-block");
}
?>
</TD></TR></TABLE> <!-- End menu wrapping table -->

</TD>
<TD WIDTH="100%" CLASS="main">
<!-- Here is the center content -->