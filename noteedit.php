<?php

$page="noteedit";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

check_csrf();

sql_conn();

$max_note_size = 2048;
$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];
$now = time();

$catid=get_post("catid");
$itemid=get_post("itemid");
$noteid=get_post("noteid");
$site=get_post('site');
$catidx = $catid;

if (!$catid or !$itemid)
    error_out("Error: ($page) parameter error");

$query = "select userid, noteid from logins where id='$itemid' and catid='$catid'";

if(!$result=sql_query($db,$query)){
    error_out("Error: ($page) database error: ".sql_error($db));
}
$row = sql_fetch_assoc($result);

if (($row['userid']!=$userid) or ($row['noteid'])!=$noteid){
    error_out("Error: ($page) You do not have access to this item");
}
$noteid = $row['noteid'];

if ($noteid){
    $query = "select note from notes where id='$noteid'";

    if(!$result=sql_query($db, $query))
        error_out("Error: ($page) fetching note: ".sql_error($db));

    $row = sql_fetch_assoc($result);
    $notedata = $row['note'];

    $status_message = "View notes for '$site' (unlock to edit)";

} else {
    $notedata = '';
    $status_message = "Creating notes for '$site'";
}

if (!has_status()) set_status($status_message);

include ("inc/header.php");

$catid = $catidx;

$backurl = "catview.php?catid=$catid";

?>
<!-- note view/edit  -->
<div class="w3-container">
<div class="w3-card w3-round w3-padding-16" onclick="lockedclick(this)">
    <div id='statmessage' class="w3-center w3-margin txtgrey"><?php echo $status_message?></div>
<form id='savf' action="notesave.php" method=post class="w3-container">
    <input type="hidden" form='savf' name="itemid" value=<?php echo $itemid; ?> >
    <input type="hidden" form='savf' name="catid" value=<?php echo $catid; ?> >
    <input type="hidden" form='savf' name="noteid" value=<?php echo $noteid; ?> >
    <input type="hidden" form='savf' name="site" value=<?php echo $site; ?> >
    <input type="hidden" form='savf' name="csrftok" value=<?php echo get_csrf();?> >
    
    <textarea name="notes" id="area" form='savf' cols="30" autocomplete="on" maxlength="<?php echo $max_note_size?>"
        title="click edit to update text" class="w3-block locked focus" spellcheck="true" disabled
        placeholder="You can keep notes about this password entry here.  These are not encrypted."
        rows="10"><?php echo $notedata; ?></textarea>
    
<div class="w3-bar w3-center w3-margin-top">
<div style="">
    <a class='butbut w3-button w3-hover-pale-green w3-round' href="<?php echo $backurl;?>" title='Make No Changes'><i class='material-icons backicon iconoffs'>chevron_left</i>Back</a>
<?php if ($noteid) { ?> <!-- unlock button  -->
    <a class='butbut w3-button w3-hover-pale-green w3-round' onclick='enableedit();', type=button title='Enable editing'><i id='padlock' class='material-icons lockicon iconoffs'>lock</i></a>            
<?php } ?>
    <button type="submit" form='savf' title='Save changes' class="butbut w3-btn w3-hover-pale-red w3-round locked" ><i class='material-icons saveicon iconoffs'>check_circle</i>Save</button>
</form>
<?php if ($noteid) {?> <!-- delete button  -->
    <button type='button' form='delf' title='Delete Note' onclick="document.getElementById('delf').submit();"
        class='butbut w3-btn w3-hover-pale-red w3-round locked'><i class='material-icons delicon iconoffs'>delete</i>Delete</button>
<?php } ?>
<form id='delf' action="notedelete.php" method=post class="w3-container">
    <input type="hidden" form='delf' name="itemid" value=<?php echo $itemid; ?> >
    <input type="hidden" form='delf' name="catid" value=<?php echo $catid; ?> >
    <input type="hidden" form='delf' name="noteid" value=<?php echo $noteid; ?> >
    <input type="hidden" form='delf' name="site" value=<?php echo $site; ?> >
    <input type="hidden" form='delf' name="csrftok" value=<?php echo get_csrf();?> >
</form>
</div>
</div>
</div>
<script>
var islocked = false;
const noteid = <?php echo $noteid;?>;

doenable = function(flag){
    console.log(`doenable ${flag}`);
    const lck = document.getElementsByClassName('locked');
    const n = lck.length;
    for (let i=0;i<n;i++ ) { 
        lck.item(i).disabled = flag;
    }
}
enableedit = ()=>{ 
    islocked=false;
    doenable(false);
    pdlck = document.getElementById('padlock')
    pdlck.innerText='lock_open';
    pdlck.style.color='green';
    document.getElementById('statmessage').innerText = "Editing..."
    document.getElementById("area").focus();
}

if (noteid) { 
    doenable(true);
    islocked=true;
}
else doenable(false);

lockedclick = function(el){
    if (islocked)
        flashmes('Form is locked - unlock to edit');
}
</script>

<?php
include ("inc/footer.php");
?>