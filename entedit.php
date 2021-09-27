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
        "settings.php?action=edit");
} 
$cats=restoarray($result);

include ("inc/header.php");

$catid=sanigorp("catid");
$mod_time = strftime($time_format, $modified);
$backurl = (isset($catid) and !is_null($catid)) ? "catview.php?catid=$catid" : "catindex.php";
?>
<table border=0 width=100% id="rightcontent" class="">
<!-- right side components -->
<tr>
<td width=40% >
    <TABLE BORDER="0" class="w3-card w3-margin" CELLPADDING="2" CELLSPACING="0" id='catedittab' width=200 >
    <TR>
<form action="entsave.php" method="POST">
<input type="hidden" name="itemid" value=<?php echo $itemid ?> >
<input type="hidden" name="noteid" value=<?php echo $noteid ?> >
    <TD CLASS="plain right" ><span class=error>*</span>Category: </TD>
    <TD CLASS="plain"><?php echo input_select("catid",$catid,$cats,'plain locked','Select the category for this entry'); ?></TD>
</TR>
<TR>
    <TD CLASS="plain right"><span class=error>*</span>Site: </TD>
    <TD CLASS="plain">
        <input type="text" required name="site" size="30" maxlen="255"
            value="<?php echo $site;?>" 
            class='locked focus' title='Identify this entry (required)' >
    </TD>
</TR>
<TR>
    <TD CLASS="plain right">URL: </TD>
    <TD CLASS="plain">
        <input type="text" name="url" size="30" maxlen="255"
            value="<?php echo $url;?>" 
            class='locked' title='URL for this site' >
    </TD>
</TR>
<TR>
    <TD CLASS="plain right">Login: </TD>
    <TD CLASS="plain">
        <input type="text" name="login" size="30" maxlen="255"
            value="<?php echo $login;?>" 
            class='locked' title='Account login' >
    </TD>
</TR>
<TR>
    <TD CLASS="plain right">Password: </TD>
    <TD CLASS="plain">
        <input type="text" name="password" size="30" maxlen="255"
            value="<?php echo $password;?>" 
            class='locked password' title='Enter the password' >
    </TD>
</TR>
<tr >
    <TD CLASS="w3-bar w3-center" colspan=2 >
        <a class='butbut w3-button w3-border w3-hover-pale-green' href="<?php echo $backurl;?>" title='Make No Changes'>Back</a>
        <?php if ($itemid) { ?>
        <a class='butbut w3-button w3-border w3-hover-pale-green' onclick='enableedit();', type=button title='Enable editing'>Edit</a>
        <?php } ?>
        <button type="submit" title='Save changes' class="butbut w3-btn w3-border w3-hover-pale-red locked" >Save</button>&nbsp;&nbsp;
    </TD>

</tr> 
<tr>
    <td class="w3-center" colspan=2 >
    <?php if ($itemid) { ?>
    <span class="txtgrey w3-small"><i>last updated: <?php echo $mod_time; ?></span></td>
    <?php }  else { ?> <span class="txtgrey w3-small" ><i>Creating a New Password entry </td> 
<?php } ?>
    </td>
</tr>
</TABLE>


</td><td>
<!-- left side components -->
<div class="notebar hide w3-card" id="noteside">
<?php if (true or $noteid) { ?>
    <div class="w3-center w3-small txtgrey" ><i>Notes attached to this entry:</i></div>
<textarea name="notes" id="area" cols="30" maxlength=4096
title="click edit to update text" class="w3-block locked w3-monospace"
placeholder="You can keep notes here..."
rows="10">
    <?php 
    echo $notedata; 
    ?>
</textarea> </div><?php } ?>
</td></tr>
<!-- button row -->
<!-- <?php if ($itemid) { ?>
    <td class="plain w3-small w3-center info"><span ><i>last updated: <?php echo $mod_time; ?></span></td>
    <?php }  else { ?> <td class="w3-large info w3-center" ><i>Creating a New Password entry </td> 
<?php } ?> -->
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