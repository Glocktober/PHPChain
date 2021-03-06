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
$cats = [];
$catid=get_post("catid");
$itemid=get_post("itemid");
$notedata = "";

if ($itemid) check_csrf();
else{
    # This was a GET - only allowed for new items
    if (sanigorp('catid'))
        $catid=sanigorp('catid');
    $itemid=0;
}
if ($itemid!=0) {
    
    //Get existing data and decrypt it first.
    $result=sql_query($db,"select id, iv, catid, login, password, site, url, noteid, modified from logins where id = '$itemid' and userid='$userid'");
    if (sql_num_rows($result)==1) {
        $row=sql_fetch_assoc($result);
        $catid=$row["catid"];
        $iv = base64_decode($row["iv"]);
        $login=decrypt($key,$row["login"],$iv);
        $password=decrypt($key,$row["password"],$iv);
        $site=decrypt($key,$row["site"],$iv);
        $url=decrypt($key,$row["url"],$iv);
        $noteid=$row['noteid'];
        $modified=$row['modified'];
        if (!has_status()) set_status("View/Edit password entry for <span class='w3-tag w3-green w3-round'>$site</span>");
    } else {
        error_out("Error: Not authorized for this entry",'catview.php' );
    }
    if ($noteid){
    //     if (!$result = sql_query($db, "select note from notes where id = \"$noteid\"")){
    //         error_out("Error: Fetching note data for entry '$site'", 'catview.php');
    //     }
    //     $row = sql_fetch_assoc($result);
    //     $notedata = $row['note'];
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

# Get list of categories.
$result=sql_query($db,"select id, title from cat where userid = '$userid' order by title");
if (sql_num_rows($result)==0) {
    error_out("No Folders exist for \"<b>$authed_login</b>\" - Create a Folder",
        "catedit.php");
}

while($row=sql_fetch_assoc($result)){
    $cats[$row['id']] = $row['title']; 
}

$noteclass = $noteid ? '': 'locked';
$notetip = $noteid ? 'View existing note' : 'Create a note';
$noteicon = $noteid ? 'edit_note': 'note_add' ;

$mod_time = $modified ? strftime($time_format, $modified) : "(the epoch)";
$backurl = (isset($catid) and !is_null($catid)) ? "catview.php?catid=$catid" : "catindex.php";

include ("inc/header.php");

# header.php hammers $catid:
$catid=sanigorp("catid");
?>
<div class="w3-card" id="cardpanel">
<div class=" w3-padding-16 ">

<div class='w3-center fullw' >
    <?php if ($itemid) { ?>
        <span class="txtgrey "><i class="material-icons iconoffs">key</i>&nbsp;<i>last updated: <?php echo $mod_time; ?></span>
    <?php }  else { ?> 
        <span class="txtgrey " ><i class="material-icons iconoffs">key</i>&nbsp;<i>Creating a New Password entry <?php } ?>
</div><br>

<div class='' ><!-- Hidden fields  -->
<form action="entsave.php" method="POST">
        <input type="hidden" name="itemid" value=<?php echo $itemid ?> >
        <input type="hidden" name="noteid" value=<?php echo $noteid ?> >
        <input type="hidden" name="csrftok" value=<?php echo get_csrf() ?> >
</div>
<div id="lockwarn"> <!-- Warning when lock is set -->
<div class='w3-center w3-margin-left fullw' >
    <label CLASS="plain labform" ><span class=error>*</span> Folder:</label>
<!-- Build select options widget  -->
<select name="catid" class="plain locked" title="Select the Folder for this entry">
<?php
foreach($cats as $k => $v){
    if ($k==$catid) {
        echo "<option selected value='$k'>$v</option>";
    } else {
        echo "<option value='$k'>$v</option>";
    }
}
?>
</select>
</div><br>

<div class='w3-center w3-margin fullw' >
    <label CLASS="plain labform"><span class=error>*</span> Site:</label>
    <input type="text" required name="site" size="30" maxlen="255"
        value="<?php echo $site;?>" autocomplete="off" spellcheck="false"
        placeholder="Enter a site name..."
        class='locked focus' title='Identify this entry (required)' >
</div><br>
<div class='w3-center w3-margin fullw' >
    <label CLASS="plain labform">URL:</label>
    <input type="text" name="url" size="30" maxlen="255"
        value="<?php echo $url;?>" autocomplete="off" spellcheck="false"
        placeholder="Url for this site..."
        class='locked' title='URL for this site' >
</div><br>
<div class='w3-center w3-margin' >
    <label CLASS="plain labform" for="login">Login:</label>
    <input id="login" type="text" name="login" size="30" maxlen="255"
        value="<?php echo $login;?>" autocomplete="off" spellcheck="false"
        placeholder="Enter the login..."
        class='locked' title='Account login' >
</div><br>
<div class='w3-center w3-margin' >
    <label CLASS="plain labform" for="pass">Password:</label>
    <input id="pass" type="text" name="password" size="30" maxlen="255"
        value="<?php echo $password;?>" autocomplete="off" spellcheck="false"
        placeholder="Enter the password..."
        class='locked password' title='Enter the password' >
</div><br>
</div> <!-- lockwarn end-->
<div class='w3-margin w3-bar' >   
    <p class="plain labform">&nbsp;</p>
    <div style="">
    <a class='w3-button w3-bar-item w3-hover-pale-green w3-round' href="<?php echo $backurl;?>" title='Make No Changes'><i class='material-icons backicon iconoffs'>chevron_left</i>Back</a>
<?php if ($itemid) { ?>
    <a class='butbut w3-button w3-bar-item w3-hover-pale-green w3-round' onclick='enableedit(event);', type=button title='Enable editing'><i id='padlock' class='material-icons lockicon iconoffs'>lock</i></a>            
<?php } ?>
    <button type="submit" title='Save changes' class="butbut w3-button w3-bar-item w3-hover-pale-red w3-round locked" ><i class='material-icons saveicon iconoffs'>check_circle</i>Save</button>
</form>

<?php 
if ($itemid){
?>
<form id="noteedit" action="noteedit.php" method="POST">
    <input type="hidden" form="noteedit" name="noteid" value=<?php echo $noteid?>>
    <input type="hidden" form="noteedit" name="itemid" value=<?php echo $itemid?>>
    <input type="hidden" form="noteedit" name="catid" value=<?php echo $catid?>>
    <input type="hidden" form="noteedit" name="site" value=<?php echo $site?>>
    <input type="hidden" form="noteedit" name="csrftok" value=<?php echo get_csrf()?>>
    <button type="submit" form="noteedit" title="<?php echo $notetip?>" class="w3-button w3-bar-item w3-hover-pale-green w3-round <?php echo $noteclass?>">
    <i id="" class="material-icons isgreen editicon iconoffs"><?php echo $noteicon?></i>
    </button>
</form>
<?php
}
?>
</div>
</div>
</div>
</div>
<script>
const noteid = <?php echo $noteid;?>;
var islocked = false;

doenable = function(flag){
    const lck = document.getElementsByClassName('locked');
    const n = lck.length;
    for (let i=0;i<n;i++ ) { 
        lck.item(i).disabled = flag;
    }
    document.getElementsByClassName("focus")[0].focus();
}
enableedit = (e)=>{ 
    if (islocked){
        document.getElementById('lockwarn').removeEventListener("click",lockedclick);
        flashmes('<i class="material-icons">lock_open</i> Form unlocked', 1000);
        doenable(false);
        pdlck = document.getElementById('padlock')
        pdlck.innerText='lock_open';
        pdlck.style.color='green';
    }
    islocked = false;
}
<?php if ($itemid) { ?> 
    doenable(true); 
    islocked=true;
    <?php } ?>

lockedclick = function(){ 
    if (islocked)
        flashmes('<i class="material-icons padlock">lock</i> Form is locked - unlock to edit ', 1000);
}
document.getElementById('lockwarn').addEventListener("click",lockedclick);

</script>
<?php
include ("inc/footer.php");
?> 