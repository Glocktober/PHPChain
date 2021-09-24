<?php
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

check_csrf();

$auth = is_authed();

if (!$auth){
    set_error('You must be authenticated to perform this fuction');
    header('Location: index.php');
    die();
}

sql_conn();

$login = $_SESSION['login'];
$userid = $_SESSION['id'];

$itemid = get_post('itemid');
$notes = get_post('notes');
$catid = get_post('catid');
$doupdate = get_post('doupdate');

function validate_id($db, $itemid, $userid){
    if (!$itemid) return false;

    if($result= sql_query($db, "select iv, site from logins where id = '$itemid' and userid= '$userid'")){
        $row = sql_fetch_assoc($result);
        $key = $_SESSION['key'];
        $site=trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"])));
        return $site;
    }
    else return false;
}

if ($site = validate_id($db,$itemid, $userid)){

    if ($doupdate) $query = "update notes set note='$notes' where id=$itemid";
    else $query = "insert into notes values($itemid, '$notes')";

    if(!$result = sql_query($db, $query))
        set_error('Error: saving notes: '. sql_error($db));
    else set_status('Note Updated for \"$site\"');

} else set_error('Error: could not validate access to this note');

header("Location: cat.php?catid=$catid");
die();

