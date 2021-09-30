<?php
$page='passwordsave';
$reqauth=true;
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$loginname = $_SESSION['login'];
$key = $_SESSION['key'];

$newkey = get_post('newkey');
$newkey2 = get_post('newkey2');
$oldpass = get_post('currentpassword');

check_csrf();

$pwform = 'password.php';
if (strlen($newkey)<$min_password_length) 
    error_out("Error: ($page) Password must be at least $min_password_length characters long",$pwform);
elseif ($newkey!=$newkey2) 
    error_out("Error: ($page) The passwords you have entered do not match.",$pwform);
elseif (!$oldpass) 
    error_out("Error: ($page) You must provide your current password",$pwform);
else{
    $testkey = md5($oldpass);

    if (!$result=sql_query($db,"select teststring, iv from user where name = \"$loginname\""))
        error_out("Error: ($page) validating current user: ".sql_error($db), $pwform);

    if (sql_num_rows($result)!=1)
        error_out("Error: ($page) Validating current user user: ".sql_error($db),$pwform);

    $row=sql_fetch_assoc($result);

    if (!testteststring(trim(decrypt($testkey,base64_decode($row["teststring"]),base64_decode($row["iv"])))))
        error_out("Error: ($page) Incorrect credentials", $pwform);
}

$key = $_SESSION['key'];
$userid = $_SESSION['id'];
$loginname = $_SESSION['login'];

$newkey=md5($newkey);

// Create new entry in user table.
$iv=make_iv();
$teststring=base64_encode(encrypt($newkey,maketeststring(),$iv));
$iv=base64_encode($iv);

if (!sql_query($db, "insert into user values (NULL, '$loginname', '$teststring', '$iv')"))
    error_out("Error: ($page )changing password - password not changed: ".sql_error($db), 'password.php');

# get the new userid
$id=sql_insert_id($db);

if (!$result=sql_query($db, "select id, iv, catid, login, password, site, url, noteid, created, modified from logins where userid = '$userid'"))
    error_out("Error: ($page) updating entreis (1):".sql_error($db), 'password.php');

while ($row=sql_fetch_assoc($result)) {
    # decrypt with the old key
    $login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
    $password=trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"])));
    $site=trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"])));
    $url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
    $noteid = $row['noteid'];
    $created = $row ['created'];
    $modified = $row['modified'];
    $catid=$row["catid"];

    # encrypt with the new key
    $iv=make_iv();
    $login=base64_encode(encrypt($newkey,$login,$iv));
    $password=base64_encode(encrypt($newkey,$password,$iv));
    $site=base64_encode(encrypt($newkey,$site,$iv));
    $url=base64_encode(encrypt($newkey,$url,$iv));
    $iv=base64_encode($iv);

    # insert reencrypted row as a new row
    if (!$result2=sql_query($db, "insert into logins values (NULL, '$iv', '$id', '$catid', '$login', '$password', '$site', '$url','$noteid', '$created','$modified' )"))
        error_log("Password update failure for $loginname ".sql_error($db));

    # At this point, too late to backout on an error - should use a transaction here.
}

# renumber any categories for this user
sql_query($db, "update cat set userid = '$id' where userid = '$userid'");
# delete the old logins
sql_query($db, "delete from logins where userid = '$userid'");
# delete the old user entry
sql_query($db, "delete from user where id = '$userid'");

# set the session to use the updated userid and key
$_SESSION['id'] = $id;
$_SESSION['key'] = $newkey;

set_status( "Password for '<b>$loginname</b>' has been updated.");
header("Location: index.php");
?>