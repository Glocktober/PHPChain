<?php

$page="entedit";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];
$now = time();

$catid=sanigorp("catid");
$itemid=sanigorp("itemid");
$notedata = "";
		
if ($itemid!=0) {
    if (!has_status()) set_status("Editing password entry");
    //Get existing data and decrypt it first.
    $result=sql_query($db,"select id, iv, catid, login, password, site, url, noteid, modified from logins where id = \"$itemid\" and userid=\"$userid\"");
    if (sql_num_rows($result)==1) {
        $row=sql_fetch_assoc($result);
        $catid=$row["catid"];
        $login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
        $password=trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"])));
        $site=trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"])));
        $url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
        $noteid=$row['noteid'];
        $modified=$row['modified'];
    } else {
        error_out("Error: Not authorized for this entry",'catview.php' );
    }
    if ($noteid){
        if (!$result = sql_query($db, "select note from notes where id = \"$noteid\"")){
            error_out("Error: Fetching note data for entry '$site'", 'catview.php');
        }
        $row = sql_fetch_assoc($result);
        $notedata = $row['note'];
    }
} else {
    if (!has_status()) set_status("Creating new password entry");
    $catid=sanigorp("catid");
    $login="";
    $password="";
    $site="";
    $url="";
    $noteid=0;
    $modified=0;
}

// Get categories.
$result=sql_query($db,"select id, title from cat where userid = \"$userid\" order by title");
if (sql_num_rows($result)==0) {
    error_out("No categories exist for \"<b>$authed_login</b>\" - Create a category first",
        "catedit.php");
} 
$cats=restoarray($result);

$mod_time = strftime($time_format, $modified);
$backurl = (isset($catid) and !is_null($catid)) ? "catview.php?catid=$catid" : "catindex.php";

include ("inc/header.php");

# header.php hammers $catid:
$catid=sanigorp("catid");
?>
<div class="w3-card">
<div class=" w3-padding-16 ">

<div class='w3-center fullw' >
    <?php if ($itemid) { ?>
    <span class="txtgrey "><i>last updated: <?php echo $mod_time; ?></span>
    <?php }  else { ?> <span class="txtgrey " ><i>Creating a New Password entry <?php } ?>
</div><br>

<div class='' >
    <form action="entsave.php" method="POST">
        <input type="hidden" name="itemid" value=<?php echo $itemid ?> >
        <input type="hidden" name="noteid" value=<?php echo $noteid ?> >
</div>
<div class='w3-center w3-margin-left fullw' >
    <label CLASS="plain labform" >Category:</label>
    <?php echo input_select("catid",$catid,$cats,'plain locked','Select the category for this entry'); ?>
</div><br>
<div class='w3-center w3-margin full2' >
    <label CLASS="plain labform"><span class=error>*</span>Site:</label>
        <input type="text" required name="site" size="30" maxlen="255"
        value="<?php echo $site;?>" 
        class='locked focus' title='Identify this entry (required)' >
</div><br>
<div class='w3-center w3-margin fullw' >
    <label CLASS="plain labform">URL:</label>
    <input type="text" name="url" size="30" maxlen="255"
            value="<?php echo $url;?>" 
            class='locked' title='URL for this site' >
</div><br>
<div class='w3-center w3-margin' >
    <label CLASS="plain labform" for="login">Login:</label>
    <input id="login" type="text" name="login" size="30" maxlen="255"
        value="<?php echo $login;?>" 
        class='locked' title='Account login' >
</div><br>
<div class='w3-center w3-margin' >
    <label CLASS="plain labform" for="pass">Password:</label>
    <input id="pass" type="text" name="password" size="30" maxlen="255"
            value="<?php echo $password;?>" 
            class='locked password' title='Enter the password' >
</div><br>
<div class='w3-center w3-margin w3-bar' >   
    <a class='butbut w3-button w3-hover-pale-green w3-round' href="<?php echo $backurl;?>" title='Make No Changes'><i class='material-icons backicon iconoffs'>chevron_left</i>Back</a>
<?php if ($itemid) { ?>
    <a class='butbut w3-button w3-hover-pale-green w3-round' onclick='enableedit();', type=button title='Enable editing'><i class='material-icons editicon iconoffs'>edit</i>Edit</a>            
<?php } ?>
    <button type="submit" title='Save changes' class="butbut w3-btn w3-hover-pale-red w3-round locked" ><i class='material-icons saveicon iconoffs'>check_circle</i>Save</button>
</div>
</div>
</div>
<script>
const noteid = <?php echo $noteid;?>;
console.log('noteid is', noteid);
doable = function(flag){
    console.log('doable');
    const lck = document.getElementsByClassName('locked');
    const n = lck.length;
    for (let i=0;i<n;i++ ) { 
        lck.item(i).disabled = flag;
    }
    document.getElementsByClassName("focus")[0].focus();
}
enableedit = ()=>{
    doable(false);
    document.getElementById('noteside').style.display = 'block';
}
<?php if ($itemid) { ?> doable(true); <?php } ?>
if (noteid) { 
    document.getElementById('noteside').style['display']='block';
}
</script>
<?php
include ("inc/footer.php");
?> 