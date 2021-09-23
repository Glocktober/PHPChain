<?php
include ("inc/config.php");
include ("inc/form.php");

sql_conn();
$auth = is_authed();

$time_format = "%H:%M:%S %d-%b-%Y";

$login = "";
if (array_key_exists('login',$_SESSION)) $login = $_SESSION['login'];

if ($auth) {
	$output="<P CLASS=\"intro\">\n";
	$output.="The contents of each Category can be viewed by clicking the name on the left. If you have no categories, you will need to create some from the &quot;settings&quot; link in the menu above.\n";

	$result=sql_query($db,"select  date, ip, outcome from loginlog where name = \"$login\" order by loginlog.date desc limit 11");
	if (sql_num_rows($result)>0) {
		$class = array (0 => "error", 1=> "plain");
		$outcome = array (0 => "Failed", 1=> "Succeeded");
		$output.="<P>\n";
		$output.="<SPAN CLASS=\"plain\">Last 10 logins to your account:</SPAN>";
		$output.="<P>\n";
		$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING\"0\" class='w3-small w3-striped w3-border' >\n";
		$output.="<TR class='w3-pale-green'><TD CLASS=\"plain\" xWIDTH=\"180\">Date</TD>";
		$output.="<TD CLASS=\"plain\" >IP (host)</TD>";
		$output.="<TD CLASS=\"plain\" >Outcome</TD></TR>\n";
		# Skip this login entry
		$row = sql_fetch_assoc($result);
		while ($row=sql_fetch_assoc($result)) {
			$date_string = strftime($time_format,$row["date"]);
			$output.="<TR>\n";
			$output.="<TD CLASS=\"".$class[$row["outcome"]]."\">$date_string</TD><TD CLASS=\"".$class[$row["outcome"]]."\">".$row["ip"]." (".gethostbyaddr($row["ip"]).")</TD>";
			$output.="<TD CLASS=\"".$class[$row["outcome"]]."\">".$outcome[$row["outcome"]]."</TD></TR>\n";
		}
		$output.="</TABLE>";
	}
} else {
	$output="<P CLASS=\"intro\">
Welcome to a substantially updated PHPChain.
<P CLASS=\"intro\">
PHPChain is a secure database for storing important passwords. Data is stored encrypted using <b>AES-256-CBC</b> cipher. You may login, or create a login from the links in the menu above.
<P CLASS=\"intro\">
In order for this system to be secure, your password is not stored in the database. Not only that, but only your password may be used to decrypt the passwords you have stored. Consequently, if you forget your password, <B>all your data is unrecoverable</B>. So, while this system exists to help you recall passwords, try and remember the one to get into this site.";
}


include("inc/header.php");

echo $output;

include("inc/footer.php");

?>
