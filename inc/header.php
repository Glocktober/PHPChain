<?php
define ('C_VERSION','1.5');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");  

if (!isset($login)){
	$login="";
}
if (!isset($auth)){
	$auth = is_authed();
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
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
<TR>
<TD COLSPAN="2" CLASS="titlebar" onClick="javascript:document.location='index.php'">
	<?php echo $document_title ?> <SPAN CLASS="plain">   <i>phpchain</i> password vault version <?php echo C_VERSION; ?></A> </SPAN>
</TD>
</TR>
<TR>
<TD COLSPAN="2" CLASS="menubar" WIDTH="100%">
<?php
$left="";
$right="";

function build_button($lab,$loc, $tip=''){
	return "<button class=\"butbut\" title=$tip><a href=\"$loc\" class=\"buttext\"> $lab </a></button>";
}

if ($auth) {
	$left = build_button('Categories', 'settings.php','"add/remove/edit categories"')."  ". 
			build_button('Password','password.php','"Change the login password, (re-incrypting all passwords)"');
	$right.="Current User: <span class=info>".$_SESSION["login"] . "</span>&nbsp;&nbsp;&nbsp;&nbsp;". 
			build_button('Logout ', 'logout.php', "Log-off");

} else {
	$right = build_button('Login', 'login.php','"Logon an existing account"');
	if ($allow_new_accounts)
		$left = build_button('New Login', 'newlogin.php','"Create a new account"');
	else $left = "&nbsp;";
}

$menu="<SPAN CLASS=\"menuleft\">".$left."</SPAN><SPAN CLASS=\"menuright\">".$right."</SPAN>";
echo $menu;

?>
</TD>
</TR>
<tr><td  COLSPAN="2" width="100%" class=messagebar><?php echo status_message() ?></td></tr>
<TR>
<TD WIDTH="120" style="vertical-align: top">
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%"> 
	<!-- menu wrapper table -->
<TR><TD WIDTH="100%" CLASS="cats">&nbsp;</TD></TR>
<?php
if ($auth) {
	include ("inc/menu.php");
	$catid=NULL;
	if (isset($page) and ($page=="cat")) {
		$catid=gorp("catid");
	}
	
	echo getmenu($_SESSION["id"],$catid);
}
?>
<TR>
<TD WIDTH="100%" CLASS="catsbot">
<?php
if ($auth) {
	echo form_begin("cat.php",'POST');
	echo input_hidden("action","edit");
	echo input_hidden("itemid",0);
	echo input_hidden("catid",$catid);
	echo submit("New entry");
	echo form_end();
}
?>
<IMG SRC="img/tiny.gif" WIDTH="120" HEIGHT="1">
</TD></TR></TABLE> <!-- End menu wrapping table -->

</TD>
<TD WIDTH="100%" CLASS="main">
