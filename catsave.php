<?php

$page="catsave";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");

sql_conn();

$userid = $_SESSION['id'];

$catid=get_post('catid');
$title=get_post('title');

if(empty($title))
    error_out("Error: ($page) Required title is missing: not saved", "catedit.php?catid=$catid");

if($catid)
    $query= "update cat set title='$title' where id='$catid' and userid='$userid'";
else
    $query= "insert into cat values(NULL, '$userid', '$title')";

if (!$result=sql_query($db, $query)){
    error_log($query);
    error_out("Error: ($page) database error: ".sql_error($db), "catedit.php?catid=$catid");
}

if($catid)
    set_status("Updated category title '$title'");
else{
    $catid = sql_insert_id($db);
    set_status("New category '$title'");
}

header("Location: catlist.php?catid=$catid");
?>