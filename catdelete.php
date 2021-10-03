<?php
$page="entdelete";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$userid = $_SESSION['id'];
$authed_login = $_SESSION['login'];
$now = time();

$catid=get_post('catid');

if (!$result=sql_query($db,"select count(catid) from logins where userid=\"$userid\" and catid=\"$catid\""))
    error_out("Error: ($page) checking for empty folder: ".sql_error($db), 'catlist.php');

$row=sql_fetch_row($result);

if ($row[0]>0) {
    error_out('Error: Unable to delete Folder. Folder is not empty',
            "catlist.php");
} else {
    if (!sql_query($db,"delete from cat where id = \"$catid\" and userid=\"$userid\""))
        set_error("Error: ($page) Deleting Folder entry: ".sql_error($db), 'catlist.php');
    else set_status('Folder Successfully Removed');
}
header("Location: catlist.php");