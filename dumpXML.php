<?php
include("inc/sessions.php");
include("inc/db.php");
include("inc/crypt.php");
define ('C_VERSION','1.0');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

sql_conn();


$auth= is_authed();

if (!$auth) {
        header("Location: index.php");
        die();
}

$userid=$_SESSION["id"];
$key=$_SESSION["key"];
$login=$_SESSION["login"];


function getSectionTitles($db, $userid)
{
        $result=mysqli_query($db, "select id, title from cat where userid = \"$userid\"");
        while ($row=mysqli_fetch_row($result)) {
        $titles [ $row[0]] = htmlspecialchars($row[1]);
        }
        return $titles;
}


$acctName = $login;
$titles = getSectionTitles($db, $userid);

header('Content-Type: application/xml');
$output = '<?xml version="1.0" encoding="UTF-8"?>';
$output .= "<!-- PHPchain password extract -->";

$result=mysqli_query($db, "select id, catid, iv, login, password, site, url from logins where userid = \"$userid\"");

if (mysqli_num_rows($result)==0) {
    $output.="<error>No data found</error>";
} else {
    $output .= '<pwlist>';
    while ($row=mysqli_fetch_assoc($result)) {

        $login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
        $password=htmlspecialchars(trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"]))));
        $site=htmlspecialchars(trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"]))));
        $url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
	    $cat = trim($row["catid"]);

        $output .= "<pwentry>";
        $output .= "<group>" .  $titles[$cat] . "</group>";
        $output .= '<notes>Group ' .  $row["id"] . " from " . $titles[$cat].  '</notes>';
        $output .= '<title>' .  $site .  '</title>';
        $output .= '<username>' . $login . '</username>';
        $output .= '<password>'  . $password .  '</password>';
        $output .= '<url>' .  $url . '</url>';
        $output .="</pwentry>";

    }
    $output .= '</pwlist>';
}

echo $output;
?>
