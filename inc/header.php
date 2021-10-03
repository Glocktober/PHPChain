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
<!-- outer structure - div-->
<div class="">
<!-- menu bar -->
<?php
if ($auth){
	?>  <!-- Authenticated  -->
<div id="titlebar" class="w3-bar w3-teal" >

<form action="logout.php" method="POST" class='w3-right butform' >
	<button class="w3-button w3-bar-item iconoffs w3-hover-pale-blue" title="log out of PHPchain"><i class='material-icons  menuicon iconoffs'>logout</i><span class="">&nbsp;Logout</span></button></form>

<!-- drop-down menu  -->
<div class="w3-left w3-dropdown-hover">
	<button class="w3-button w3-bar-item w3-teal" title="Menu"><i class='material-icons  menuicon iconoffs'>menu</i></button>
<div class="w3-dropdown-content w3-bar-block w3-teal">

	<button class="w3-button w3-bar-item w3-tal w3-hover-teal" title="Menu"><i class='material-icons  menuicon iconoffs'>menu</i></button>
	<button class="w3-button w3-bar-item w3-tal w3-hover-pale-blue" onclick="clearFilters()" title="Clear search filters"><i class='material-icons  menuicon iconoffs'>clear</i>&nbsp;Clear Filters</button>
	<form action="logout.php" method="POST" class='butform w3-block' >
		<button class="w3-button w3-bar-item w3-hover-pale-blue" title="log out of PHPchain"><i class='material-icons  menuicon iconoffs'>logout</i><span class="">&nbsp;Logout</span></button></form>
	<form action="catlist.php" method="POST" class='butform w3-block' >
		<button class="w3-button w3-bar-item w3-hover-pale-blue" title="add/edit/remove Folders"><i class='material-icons  menuicon iconoffs'>create_new_folder</i><span class="">&nbsp;Manage Folders</span></button></form>
	<form action="entedit.php" method="POST" class='butform w3-block' >
		<input type="hidden" name="csrftok" value=<?php echo get_csrf()?>>
		<input type="hidden" name="catid" value=<?php echo (isset($catid)? $catid: 0)?>>
		<button class="w3-button w3-bar-item w3-hover-pale-blue" title="add a password entry"><i class='material-icons  menuicon iconoffs'>add</i><span class="">&nbsp;Add Password Entry</span></button></form>
	<form action="password.php" method="POST" class='butform w3-block' >
		<button class="w3-button w3-bar-item w3-hover-pale-blue" title="Change your PHPchain login password"><i class='material-icons  menuicon iconoffs'>key</i><span class="">&nbsp;Change Password</span></button></form>
<?php if ($allow_new_accounts){?>
	<form action="newlogin.php" method="POST" class=' w3-block' >
		<button class="w3-btn w3-bar-item w3-medium w3-hover-pale-blue butbut" title="Create a PHPchain account"><i class='material-icons  menuicon iconoffs'>person_add</i>&nbsp;<span class="">Add a New Login</span></button></form>
<?php } ?>
</div></div>

<!-- other menu bar display  -->
<p class="w3-bar-item w3-right ">&nbsp;<?php echo $document_title ?> &nbsp;</p>
<p class="w3-hover-teal w3-bar-item w3-left" title="Current user account"><i class="material-icons w3-left menuicon" >person</i>&nbsp; <?php echo $auth_login ;?></p>
<i class='material-icons w3-bar-item menuicon iconoffs' onclick="javascript:document.location='index.php'" title="home">home</i>


<?php	} else { 
	if (!has_status()) set_error('You are not logged in...');
	?>
	<!-- Not authenticated -->
<div id="titlebar" class="w3-bar w3-teal" onclick="javascript:document.location='index.php'">
<?php if ($allow_new_accounts and !$auth_for_new_accounts){?>
	<form action="newlogin.php" method="POST" class='butform  w3-ripple' >
		<button class="w3-btn w3-bar-item w3-large w3-hover-green butbut" title="Create a PHPchain account"><i class='material-icons  menuicon iconoffs'>person_add</i>&nbsp;<span class="">New Login</span></button></form>
<?php } ?>
<form action="login.php" method="POST" class='butform w3-left w3-ripple' >
		<button class="w3-btn w3-bar-item w3-large w3-hover-green butbut" title="Login with your PHPchain account"><i class='material-icons  menuicon iconoffs'>login</i><span class="">&nbsp;Login</span></button></form>

<?php
}
?>
<!-- info menu  -->
<div class="w3-riht w3-dropdown-hover">
	<button class="w3-btn w3-bar-item w3-teal w3-large" title="Info"><i class='material-icons  menuicon iconoffs'>info</i>&nbsp;</button>
<div class="w3-dropdown-content w3-bar-block w3-teal">
	<button class="w3-btn w3-bar-item w3-teal w3-hover-teal w3-large" title="Info"><i class='material-icons  menuicon iconoffs'>info</i>&nbsp;</button>
	<button class="w3-btn w3-bar-item w3-right w3-hover-teal" title="PHPchain version">Version <?php echo C_VERSION; ?></button>
</div></div>
</div> <!-- end of menu bar -->
</div>

<!-- status bar  -->
<div class="messagebar w3-bar" id='messagebar'>
		<?php echo status_message() ?>
</div> <!-- end of status bar  -->
<!-- main table  -->
<div id="toppane" class="toppane w3-container w3-border-top">
<?php
if (!isset($nomenu)){
?>
<!-- Nav plane and detail plane -->
<div id="navpane">
<!-- navigation menu list  -->
<?php
if ($auth) {
	include ("inc/listmenu.php");
	$catid=gorp('catid');
	$catcount=0;
	echo "<span class='w3-padding w3-block w3-center w3-pale-blue'>Folders</span>";
	echo getmenu($_SESSION["id"],$catid, $catcount);

	if (!$catcount){
?>
<div class="w3-block">
	<span class="w3-small error">You have no Folders.</span>
	<span class="w3-small">Create one or more Folders to group password entries.</span>
</div>
<?php 
	} 
?>
	<div class="w3-margin-top w3-center">
	<span class="w3-small w3-center"><?php echo $catcount?> Folders</span>
<?php
	$catid=gorp('catid');
	if ($catid) {
?>

<form action="entedit.php" id="newbut">
<input type="hidden" form="newbut" name="csrftok" value="<?php echo get_csrf()?>" >
<input type="hidden" form="newbut" name="catid" value="<?php echo $catid?>" >
<input type="hidden" form="newbut" name="itemid" value="0" >
<a href="javascript: void(0)" onclick="document.getElementById('newbut').submit()" 
	title="Add a new password entry"
    class='w3-block w3-button w3-hover-pale-green'><i class='material-icons addicon iconoffs'>add</i> Add Password Entry</a>
</form>
<?php
	}
?>
	<a class="w3-button w3-block w3-hover-pale-blue" href="catedit.php?catid=0" title="Add a Folder">
		<i class="material-icons addicon iconoffs">create_new_folder</i>&nbsp;Add Folder</a>
<?php
}
?>
<hr>
</div>
</div> <!-- end navigation menu content -->
<div id="detailpane">
<?php 
} else {?>
<div id="singlepane" class="w3-container div50">

<?php }?>
<!-- Here is the detail content -->