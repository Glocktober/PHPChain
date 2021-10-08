<?php

$page="entsave";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

check_csrf();

sql_conn();

$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];
$now = time();

$itemid = get_post('itemid');
$notedata = get_post('notes');
$catid = get_post('catid');
$noteid = get_post('noteid');

$login=get_post("login");
$password=get_post("password");
$site=get_post("site");
$url=get_post("url");

# legacy app placed empty http:// fields - just make them empty.
if ($url == 'http://' or ($url == 'https://')) $url = "";

$iv=make_iv();
$blogin=encrypt($key,$login,$iv);
$bpassword=encrypt($key,$password,$iv);
$bsite=encrypt($key,$site,$iv);
$burl=encrypt($key,$url,$iv);
$biv=base64_encode($iv);

if ($itemid==0) $noteid=0;

$notedata = substr($notedata, 0, 4096);

if (!empty($notedata)){

    if ($noteid==0){
        $query="insert into notes values(NULL, '$notedata')";
    } else {
        $query="update notes set note='$notedata' where id=$noteid";
    }
    
    if (!$result=sql_query($db, $query)){
        set_error("Error: ($page) saving password notes for '$site': ". sql_error($db));
    } else {
        if (!$noteid) $noteid=sql_insert_id($db);
    }
}

if ($itemid==0)
    $query="insert into logins values (NULL, '$biv', '$userid', '$catid', '$blogin', '$bpassword', '$bsite', '$burl', '$noteid', $now, $now)";
else 
    $query="update logins set iv = '$biv', catid='$catid', login='$blogin', password='$bpassword', site = '$bsite', url = '$burl', noteid='$noteid', modified = $now where id = '$itemid' and userid='$userid'";

if (!$result=sql_query($db, $query)){
    error_out("Error: ($page) saving password entry for '$site': ". sql_error($db),
        "entedit.php?catid=$catid&itemid=$itemid");
} else {
    if (!has_status()) set_status("Password Entry for '$site' has been saved");
}
header("Location: catview.php?catid=$catid");

?>