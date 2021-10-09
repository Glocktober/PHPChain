<?php
$page="index";
$reqauth=false;

include ("inc/config.php");
include ("inc/form.php");

sql_conn();
$auth = is_authed();

$login = "";
if (array_key_exists('login',$_SESSION)) $login = $_SESSION['login'];

// if (!$auth and !empty($login)){
// 	header("Location: login.php");
// 	die();
// }

if (!$auth) $nomenu=true;
include("inc/header.php");
if ($auth) { 

?>
<div id='index' class="w3-container" style="width:70%;">
<P CLASS="intro">The contents of each Folder can be viewed by clicking the name on the left. If you have no Folders, you will need to create some from the &quot;settings&quot; link in the menu above.</n>
<?php
	$result=sql_query($db,"select  date, ip, outcome from loginlog where name = \"$login\" order by loginlog.date desc limit 11");
	if (sql_num_rows($result)>0) {
		$class = array (0 => "error", 1=> "plain");
		$outcome = array (0 => "Failed", 1=> "Succeeded");
?>
<P><SPAN CLASS="plain">Last 10 logins to your account:</SPAN></p>
<TABLE class='w3-table w3-striped w3-border' >
<TR class='w3-pale-green'>
	<TD CLASS="plain" >Date</TD>
	<TD CLASS="plain" >IP (host)</TD>
	<TD CLASS="plain" >Outcome</TD>
</TR>
<?php	# Skip this login entry
		$row = sql_fetch_assoc($result);
		while ($row=sql_fetch_assoc($result)) {
			$date_string = strftime($time_format,$row["date"]);
			$class = $row['outcome'] ? 'plain': 'w3-pale-red';
			$stat = $row['outcome'] ? 'Succeeded':'Failed';
			$ip = $row['ip'];
			$ipstr = $ip ." (". gethostbyaddr($ip).")";
?>
<TR class="<?php echo $class?>">
<TD><?php echo $date_string?></TD><TD><?php echo $ipstr?></TD>
<TD><?php echo $stat?></TD></TR>
<?php 
		} ?>
</TABLE>
<?php
	}
} else {
	?>
	<p class="w3-xlarge w3-center">You are not currently logged in...</p>
<div class="w3-bar w3-center">
	<form action="login.php" method="POST" class='butform w3-center w3-ripple' >
	<label class="w3-xlarge w3-bar-item"></label>
		<button class="w3-btn w3-bar-item w3-center w3-xlarge w3-pale-green w3-hover-green butbut" title="Login with your PHPchain account"><i class='material-icons  menuicon iconoffs'>login</i><span class="">&nbsp;Please Login...</span></button></form>
</div>
<br><br><hr>
<P CLASS="w3-large">Welcome to a substantially updated PHPChain.</p>
<P CLASS="intro">
	PHPChain is a secure database for storing important passwords. Data is stored encrypted using <b>AES-256-CBC</b> cipher.</p>
	<P CLASS="intro">In order for this system to be secure, your password is not stored in the database. Not only that, but only your password may be used to decrypt the passwords you have stored. <b>If you forget your password, all your data is unrecoverable</B>. So, while this system exists to help you recall passwords, try and remember the one to get into this site.</p>
	

<?php	if ($allow_new_accounts and !$auth_for_new_accounts){ ?>
	<div class="w3-bar w3-center">
	<form action="newlogin.php" method="POST" class='butform w3-center w3-ripple' >
		<button class="w3-btn w3-bar-item w3-pale-green w3-hover-green butbut" title="Create a PHPchain account"><i class='material-icons  menuicon iconoffs'>person_add</i>&nbsp;<span class="">You can create an account here</span></button></form>
</div><br>

<?php 
	} else {?>
	<hl>
<p class="w3-large w3-center">Adding new accounts is not enabled.</p>
<?php	}
} 
?> 

</div>

<?php
include("inc/footer.php");
?>
