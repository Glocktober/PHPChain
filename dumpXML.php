<?php
include("inc/config.php");
include("inc/crypt.php");
define ('C_VERSION','1.5');

if (!isset($xml_dump_ok) OR ($xml_dump_ok != TRUE)){
    header("HTTP/1.1 401 Unauthorized");
    echo "Unsupported";
    die();
}

if (!$is_authed()) {
    header("HTTP/1.1 401 Unauthorized");
    echo "Not authenticated";
    die();
}

function xml_headers(){

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Type: application/xml');
}

function xml_start(){
    $output = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $output .= "<!-- PHPchain password extract -->\n";

    return $output;
}

function getSectionTitles($db, $userid)
{
    $result=sql_query($db, "select id, title from cat where userid = \"$userid\"");
    while ($row=sql_fetch_row($result)) {
        $titles [ $row[0]] = htmlspecialchars($row[1]);
    }
    return $titles;
}

sql_conn();

$userid=$_SESSION["id"];
$key=$_SESSION["key"];
$login=$_SESSION["login"];

$titles = getSectionTitles($db, $userid);

$output = xml_start();

$result=sql_query($db, "select id, catid, iv, login, password, site, url from logins where userid = \"$userid\"");

if (sql_num_rows($result)==0) {
    $output.="<error>No data found</error>\n";
} else {
    $output .= "<pwlist>\n";
    while ($row=sql_fetch_assoc($result)) {

        $login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
        $password=htmlspecialchars(trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"]))));
        $site=htmlspecialchars(trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"]))));
        $url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
	    $cat = trim($row["catid"]);

        $output .= "   <pwentry>\n";
        $output .= "      <group>" .  $titles[$cat] . "</group>\n";
        $output .= "      <notes>Group " .  $row["id"] . " from " . $titles[$cat].  "</notes>\n";
        $output .= "      <title>" .  $site .  "</title>\n";
        $output .= "      <username>" . $login . "</username>\n";
        $output .= "      <password>"  . $password .  "</password>\n";
        $output .= "      <url>" .  $url . "</url>\n";
        $output .= "   </pwentry>\n";

    }
    $output .= "</pwlist>\n";
}

xml_headers();
echo $output;
?>