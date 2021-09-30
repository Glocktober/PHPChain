<?php

$page="notedelete";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");

check_csrf();

sql_conn();

$userid = $_SESSION['id'];

$itemid = get_post('itemid');
$noteid = get_post('noteid');
$catid = get_post('catid');
$site = get_post('site');

$backurl = "catview.php?catid=$catid";

$query = "select noteid from logins where id='$itemid' and userid='$userid'";
if (!$result=sql_query($db, $query))
    error_out("Error: ($page) You don't have access to this note",$backurl);

$row = sql_fetch_assoc($result);

if ($row['noteid'] != $noteid)
    error_out("Error: ($page) Note mismatch", $backurl);

$query = "delete from notes where id='$noteid'";
if (!$result=sql_query($db, $query))
    error_out("Error: ($page) Failed to remove note",$backurl);

$query = "update logins set noteid='0' where id='$itemid'";
if (!$result=sql_query($db, $query))
    error_out("Error: ($page) Failed to update noteid",$backurl);

set_status("Notes have been removed for \"$site\"");
header("Location: $backurl");
?>