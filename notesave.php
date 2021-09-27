<?php

$page="catsave";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];
$now = time();

$itemid = get_post('itemid');
$notedata = get_post('notes');
$catid = get_post('catid');
$noteid = get_post('noteid');

if ($itemid==0) $noteid=0;

$newnoteid = $noteid;

if (!empty($notedata)){

    if ($noteid==0){
        $query="insert into notes values(NULL, '$notedata')";
    } else {
        $query="update notes set note='$notedata' where id=$noteid";
    }
    
    if (!$result=sql_query($db, $query)){
        set_error("Error: ($page) saving password notes for '$site': ". sql_error($db));
    } else {
        if ($noteid==0) $newnoteid=sql_insert_id($db);
    }

    if ($noteid != $newnoteid){
        $query="update logins set noteid='$newnoteid' where id='$itemid'";
    
        if (!$result=sql_query($db, $query)){
            set_error("Error: ($page) updating logins noteid: ". sql_error($db));
        }
        set_status("Notes have been updated");
    }
}
header("Location: catview.php?catid=$catid");
