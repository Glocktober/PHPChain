<?php
include("inc/sessions.php");
include("inc/db.php");
include("inc/form.php");
include("inc/crypt.php");

sql_conn();

$auth = is_authed();

$login = $_SESSION['login'];

if ($auth) {
	$output="<P CLASS=\"intro\">\n";
	$output.="The contents of each Category can be viewed by clicking the name on the left. If you have no categories, you will need to create some from the &quot;settings&quot; link in the menu above.\n";

//	$language=$_HTTP["HTTP_ACCEPT_LANGUAGE"];

//	if ($language=="en-us") {
		$format="%m/%d/%Y %T"." (PST)";
//	} else {
//		$format="%d/%m/%Y %T"." (PST)";
//	}

	$result=mysqli_query($db,"select date_format(date,\"$format\") as date, ip, outcome from loginlog where name = \"$login\" order by loginlog.date desc limit 10");
	if (mysqli_num_rows($result)>0) {
		$class = array (0 => "error", 1=> "plain");
		$outcome = array (0 => "Failed", 1=> "Ok");
		$output.="<P>\n";
		$output.="<SPAN CLASS=\"plain\">Last 10 logins to your account:</SPAN>";
		$output.="<P>\n";
		$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING\"0\">\n";
		$output.="<TR><TD CLASS=\"plain\" WIDTH=\"180\">Date</TD>";
		$output.="<TD CLASS=\"plain\" WIDTH=\"280\">IP (host)</TD>";
		$output.="<TD CLASS=\"plain\">Outcome</TD></TR>\n";
		while ($row=mysqli_fetch_assoc($result)) {
			$output.="<TR>\n";
			$output.="<TD CLASS=\"".$class[$row["outcome"]]."\">".$row["date"]."</TD><TD CLASS=\"".$class[$row["outcome"]]."\">".$row["ip"]." (".gethostbyaddr($row["ip"]).")</TD>";
			$output.="<TD CLASS=\"".$class[$row["outcome"]]."\">".$outcome[$row["outcome"]]."</TD></TR>\n";
		}
		$output.="</TABLE>";
	}
} else {
	$output="<P CLASS=\"intro\">
Welcome to PHPChain.
<P CLASS=\"intro\">
PHPChain is a secure database for storing important passwords. Data is stored encrypted using the Blowfish algorithm for security. You may login, or create a login from the links in the menu above.
<P CLASS=\"intro\">
In order for this system to be secure, your password is not stored in the database. Not only that, but only your password may be used to decrypt the passwords you have stored. Consequently, if you forget your password, <B>all your data is unrecoverable</B>. So, while this system exists to help you recall passwords, try and remember the one to get into this site.";
}


include("inc/header.php");
echo $output;
include("inc/footer.php");

?>
