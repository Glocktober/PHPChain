<?php
define ('C_VERSION','1.0');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");  

if ($_SERVER["HTTPS"]!="on") {
	$warning="SSL not enabled! Connection is not secure! Click <A HREF=\"https://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."\">here</A> for secure version.";
}

if (!isset($login)){
	$login="";
}
if (!isset($auth)){
	$auth = is_authed();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
    "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
<LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
<TITLE>PHPChain</TITLE>
</HEAD>
<BODY CLASS="main">
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
<TR>
<TD COLSPAN="2" CLASS="titlebar" onClick="javascript:document.location='index.php'">
PHPChain <SPAN CLASS="plain"> - Powered by <A HREF="http://www.globalmegacorp.org/PHPChain">PHPChain <?php echo C_VERSION; ?></A> (somewhat updated for sunyocc)</SPAN>
</TD>
</TR>
<TR>
<TD COLSPAN="2" CLASS="menubar" WIDTH="100%">
<?php
$left="";
$right="";

if ($auth) {
	$left.="<A HREF=\"settings.php\" CLASS=\"menubar\">Settings</A>";
	$left.="<A HREF=\"password.php\" CLASS=\"menubar\">Password</A>";
	$left.="<A HREF=\"logout.php\" CLASS=\"menubar\">Logout</A>";
	$right.="Logged in as: ".$_SESSION["login"];

} else {
	$left.="<A HREF=\"newlogin.php\" CLASS=\"menubar\">Create login</A>";
	$right.=form_begin("login.php","POST");
	$right.="Login: ".input_text("login",8,255,$login,"login");
	$right.="&nbsp;Password: ".input_passwd("key",8,255,NULL,"login")."&nbsp;";
	$right.=submit("Login",NULL,"submit");
	$right.=form_end();
}

$menu="<SPAN CLASS=\"menuleft\">".$left."</SPAN><SPAN CLASS=\"menuright\">".$right."</SPAN>";
echo $menu;

?>
</TD>
</TR>
<?php
if (isset($warning)) {
	echo "<TR><TD COLSPAN=\"2\" CLASS=\"nossl\" WIDTH=\"100%\">".$warning."</TD></TR>";
}
?>
<TR>
<TD WIDTH="120" style="vertical-align: top">
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%"> <!-- menu wrapper table -->
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
