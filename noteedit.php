<?php

$page="noteedit";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$max_note_size = 2048;
$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];
$now = time();

$catid=sanigorp("catid");
$itemid=sanigorp("itemid");
$noteid=sanigorp("noteid");
$catidx = $catid;

$query = "select userid, noteid from logins where id=$itemid and catid=$catid";
if(!$result=sql_query($db,$query)){
    error_out("Error: ($page) database error: ".sql_error($db));
}
$row = sql_fetch_assoc($result);

if (($row['userid']!=$userid) or ($row['noteid'])!=$noteid){
    error_out("Error: ($page) You do not have access to this item");
}

if ($noteid){
    $query = "select note from notes where id='$noteid'";

    if(!$result=sql_query($db, $query))
        error_out("Error: ($page) fetching note: ".sql_error($db));

    $row = sql_fetch_assoc($result);
    $notedata = $row['note'];

    if (!has_status()) set_status('Editing entry notes');

} else {
    $notedata = '';

    if (!has_status()) set_status('Adding entry notes');
}

include ("inc/header.php");

$catid = $catidx;

$backurl = (isset($catid) and !is_null($catid)) ? "catview.php?catid=$catid" : "catindex.php";

?>
<div class="w3-card w3-round w3-padding-16">
<form id='' action="notesave.php" method=post class="w3-container">
    <input type="hidden" name="itemid" value=<?php echo $itemid; ?> >
    <input type="hidden" name="catid" value=<?php echo $catid; ?> >
    <input type="hidden" name="noteid" value=<?php echo $noteid; ?> >
    <input type="hidden" name="csrftok" value=<?php echo get_csrf();?> >
    
    <textarea name="notes" id="area" cols="30" autocomplete="on" maxlength="<?php echo $max_note_size?>"
        title="click edit to update text" class="w3-block locked" spellcheck="true"
        placeholder="You can keep notes about this password entry here.  These are not encrypted."
        rows="10"><?php echo $notedata; ?></textarea>
    
    <div class="w3-bar w3-center w3-margin-top">
    <a class='butbut w3-btn w3-border w3-hover-pale-green focus' href="<?php echo $backurl;?>" title='Make No Changes'>Back</a>&nbsp;
<?php if ($noteid) { ?>
    <a class='butbut w3-button w3-border w3-hover-pale-green' onclick='doenable(false);', type=button title='Enable editing'>Edit</a>&nbsp;
<?php } ?>
    <button type="submit" title='Save changes' class="butbut w3-btn w3-border w3-hover-pale-red locked" >Save</button>
</div>
</form>
</div>
<script>
doenable = function(flag){
    console.log('doable');
    const lck = document.getElementsByClassName('locked');
    const n = lck.length;
    for (let i=0;i<n;i++ ) { 
        lck.item(i).disabled = flag;
    }
    document.getElementById("area").focus();
}
<?php if ($noteid) { ?> doenable(true) <?php } ?>
</script>

<?php
include ("inc/footer.php");
?>