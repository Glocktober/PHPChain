<?php
define ('C_VERSION',$app_version);
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");  

if (!isset($auth)){
	$auth = is_authed();
}
$hdrcatid = isset($catid) ? $catid : 0;
$hdruserid = $auth ? $_SESSION['id'] : 0;

$auth_login = $auth ? $_SESSION['login'] :"";

$document_title = $site_name ? $site_name : "Php chain";
?>
<!DOCTYPE html>
<html lang="en">
<HEAD>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<LINK REL=StyleSheet HREF="assets/style.css" TYPE="text/css">
<link rel="stylesheet" href="assets/w3.css">
<script src="assets/w3.js"></script>
<link rel="stylesheet" href="assets/icon.css">

<TITLE><?php echo $document_title; ?> phpchain</TITLE>
</HEAD>
<BODY CLASS="main">
<!-- outer structure - div-->
<div class="">
<!-- menu bar -->
<div id="titlebar" class="w3-bar w3-teal w3-padding" style="opacity:0.8;" >
<?php
if ($auth){
?> 	<!--  authenticated menu bar items -->
	<span class="w3-tooltip w3-bar-item w3-right" onclick="javascript:document.location='logout.php'"><i class='material-icons menuicon iconoffs' title="Logoff">logout</i><span class="w3-text">&nbsp;Logout</span></span>
<?php if ($allow_new_accounts){?> <!-- add new login permitted -->
	<span class="w3-tooltip w3-bar-item w3-right" onclick="javascript:document.location='newlogin.php'"><i class="material-icons menuicon iconoffs">person_add</i>&nbsp;<span class="w3-text">Add chain user</span></span>
<?php } ?>
	<span class="w3-tooltip w3-bar-item w3-right" onclick="javascript:document.location='password.php'"><i class="material-icons menuicon iconoffs">password</i>&nbsp;<span class="w3-text">Change <i><?php echo $auth_login?></i> password</span></span>
	<span class="w3-tooltip w3-bar-item w3-right" onclick="javascript:document.location='index.php'"><i class='material-icons menuicon iconoffs' title="home">home</i>&nbsp;<span class="w3-text"><?php echo $document_title;?></span></span>
	<span class="w3-tooltip w3-bar-item w3-right"><i class='material-icons menuicon iconoffs'>person</i> <span class="w3-text"><?php echo $auth_login? $auth_login: 'Not logged in'; ?>&nbsp;</span></span>
	<span class="w3-tooltip w3-bar-item" onclick="javascript:document.location='catlist.php'"><i class="material-icons menuicon iconoffs">list</i>&nbsp;<span class="w3-text">Manage Folders</span></span>
	<span class="w3-tooltip w3-bar-item" onclick="javascript:document.location='catedit.php?catid=0'"><i class="material-icons menuicon iconoffs">create_new_folder</i>&nbsp;<span class="w3-text">Add Folder</span></span>
	<span class="w3-tooltip w3-bar-item" onclick="javascript:document.location='entedit.php?catid=<?php echo $hdrcatid ?>'"><i class="material-icons menuicon iconoffs">add</i>&nbsp;<span class="w3-text">New password entry</span></span>
	<span class="w3-tooltip w3-bar-item" onclick="clearFilters()"title="Clear search filters" ><i class='material-icons  menuicon iconoffs'>clear</i>&nbsp;<span class="w3-text">Clear Filters</span></span>
<?php	} else { 
	if (!has_status()) set_error('You are not logged in...');
	?>	<!-- Not authenticated menu bar items -->
	<span class="w3-tooltip w3-bar-item w3-right" onclick="javascript:document.location='login.php'"><i class="material-icons menuicon iconoffs" >login</i>&nbsp;<span class="w3-text">Login</span></span>
<?php if ($allow_new_accounts and !$auth_for_new_accounts){?> 
	<span class="w3-tooltip w3-bar-item w3-right" onclick="javascript:document.location='newlogin.php'"><i class="material-icons menuicon iconoffs" >person_add</i>&nbsp;<span class="w3-text">Create a New Account</span></span>
<?php } ?>
<?php
}
?>

<span class="w3-tooltip w3-bar-item w3-right"><i class='material-icons menuicon iconoffs'>info</i> <span class="w3-text">PHPchain v<?php echo C_VERSION; ?>&nbsp;</span></span>

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

if ($auth) { 
	$catcount=0;
	$db = sql_conn();

	if($hdrresult=sql_query($db,"select id, title from cat where userid = '$hdruserid'")){

		$catcount = sql_num_rows($hdrresult);

?>
	<!-- Nav plane and detail plane -->
	<div id="navpane">
	<span class='w3-padding w3-block w3-center w3-pale-blue'>
		<span class='w3-badge w3-small w3-blue'><?php echo $catcount?></span>&nbsp;&nbsp;Folders&nbsp;
		<a href="catlist.php" class="" title="Manage Folders"><i class="material-icons addicon micon">list</i></a>
	</span>
	<input class='seafilter w3-round w3-border fullw' oninput="w3.filterHTML('#catlist', 'li', this.value)" placeholder='Type to Filter Folders List...'>
	<!-- navigation menu list  -->
	<ul id="catlist" class="w3-smal w3-ul">
<?php
		while($row=sql_fetch_assoc($hdrresult)){
			$hdrid = $row['id'];
			$hdrtitle = $row['title'];
			$hdrclass = "w3-hover-pale-blue";
			$hdrdescr = $hdrtitle;
			if ($hdrid == $hdrcatid){
				$hdrdescr .= "&nbsp;&nbsp;<i class='iconchev material-icons' style='font-size:15px;'>format_list_bulleted</i></a></li>";
				$hdrclass = "w3-pale-blue";
			}
?>
	<li class='<?php echo $hdrclass?> w3-padding-small catitem' title='Open folder <?php echo $hdrtitle?>'>
	<a class='cat' href='catview.php?catid=<?php echo $hdrid?>'><span><?php echo $hdrdescr?></span></a></li>
<?php
		}
	}
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
	<?php
	if ($hdrcatid) {
?>

<form action="entedit.php" id="newbut">
<input type="hidden" form="newbut" name="csrftok" value="<?php echo get_csrf()?>" >
<input type="hidden" form="newbut" name="catid" value="<?php echo $hdrcatid?>" >
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
</ul><!-- end of list -->
<hr>
</div> <!-- end navigation menu content -->
<div id="detailpane" class="detailpane">
<?php 
} else {?>
<div id="singlepane" class="w3-container">

<?php }?>
<!-- Here is the detail content -->