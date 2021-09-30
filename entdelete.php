<?php
$page="entdelete";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];

$itemid = get_post('itemid');
$catid = get_post('catid');
$noteid = get_post('noteid');

if ($noteid){
    $query = "delete from notes where id='$noteid'";
    if (!$result=sql_query($db,$query))
        error_out("Error: ($page) part1 ".sql_error($db));
}
if ($itemid){
    $query = "delete from logins where id='$itemid' and userid='$userid'";
    if (!$result=sql_query($db,$query))
        error_out("Error: ($page) part2 ".sql_error($db));

}
header("Location: catview.php?catid=$catid");