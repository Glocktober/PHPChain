<?php
$page='passwordsave';
$reqauth=true;
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$loginname = $_SESSION['login'];

$newkey = get_post('newkey');
$newkey2 = get_post('newkey2');

check_csrf();

$error='';
if (strlen($newkey)<$min_password_length) $error="Error: Password must be at least $min_password_length characters long";
elseif ($newkey!=$newkey2) $error="Error: The passwords you have entered do not match.";

error_log("$newkey == $newkey2");
if ($error) error_out($error, 'password.php');

$key = $_SESSION['key'];
$userid = $_SESSION['id'];
$loginname = $_SESSION['login'];

$newkey=md5($newkey);

// Create new entry in user table.
$iv=make_iv();
$teststring=base64_encode(encrypt($newkey,maketeststring(),$iv));
$iv=base64_encode($iv);

error_log("iv=$iv  str=$teststring ");
if (!sql_query($db, "insert into user values (NULL, '$loginname', '$teststring', '$iv')"))
    error_out("Error: ($page )changing password - password not changed: ".sql_error($db), 'password.php');

$id=sql_insert_id($db);

error_log("new id $userid => $id");
if (!$result=sql_query($db, "select id, iv, catid, login, password, site, url, noteid, created, modified from logins where userid = '$userid'"))
    error_out("Error: ($page) updating entreis (1):".sql_error($db), 'password.php');

error_log("XXXX Number of rows " . sql_num_rows($result));

while ($row=sql_fetch_assoc($result)) {
    $login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
    $password=trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"])));
    $site=trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"])));
    $url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
    $noteid = $row['noteid'];
    $created = $row ['created'];
    $modified = $row['modified'];
    $catid=$row["catid"];
    error_log("Updating $site / $login / $password");

    $iv=make_iv();
    $login=base64_encode(encrypt($newkey,$login,$iv));
    $password=base64_encode(encrypt($newkey,$password,$iv));
    $site=base64_encode(encrypt($newkey,$site,$iv));
    $url=base64_encode(encrypt($newkey,$url,$iv));
    $iv=base64_encode($iv);
    if (!$result2=sql_query($db, "insert into logins values (NULL, '$iv', '$id', '$catid', '$login', '$password', '$site', '$url','$noteid', '$created','$modified' )"))
        error_log("Password update failure for $loginname ".sql_error($db));

    # At this point, too late to backout on an error - should use a transaction here.
}

// DB cleanup.

sql_query($db, "update cat set userid = '$id' where userid = '$userid'");
sql_query($db, "delete from logins where userid = '$userid'");
sql_query($db, "delete from user where id = '$userid'");

$_SESSION['id'] = $id;
$_SESSION['key'] = $newkey;

set_status( "Password for '<b>$loginname</b>' has been updated.");
header("Location: index.php");
die();
?>