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

$itemid = sanigorp('itemid');
$noteid = sanigorp('noteid');
$catid = sanigorp('catid');

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

function get_note($db, $itemid){
    $result = sql_query($db, "select note from notes where id = \"$itemid\"");

    $noteexists = false;
    $notedata = '';
    if ($result){
        $notexists = true;
        $row = sql_fetch_assoc($result);
        $notedata = $row['note'];
    } 
    return $notedata;
}

if ($site = validate_id($db, $itemid, $userid)){
    $notedata = get_note($db, $itemid);
} else{
    set_error('Error: could not validate "<b>$login</b>" for access to this note');
    header("Location: cat.php?catid=$catid");
    die();
} 

if (!$notedata) {
    $notedata = 'No notes exist for this entry. Enter notes here.';
    $doupdate = false;
    set_status("No existing notes for \"$site\"");
} else{
    set_status("Existing notes for \"$site\"");
    $doupdate = true;
}

include ("inc/header.php");

$catid = sanigorp('catid');
?>
<form id='fff' action="savenotes.php" method=post>
    <input type="hidden" name="itemid" value=<?php echo $itemid; ?> >
    <input type="hidden" name="catid" value=<?php echo $catid; ?> >
    <input type="hidden" name="doupdate" value=<?php echo $doupdate; ?> >
    <input type="hidden" name="csrftok" value=<?php echo get_csrf();?> >
    
    <textarea name="notes" id="area" cols="30" disabled 
    title="click edit to update text" class="w3-block"
    onclick="enableit()"rows="10">
        <?php 
        echo $notedata; 
        ?>
    </textarea>
    <button type="button" class="w3-btn focus" title="OK"
    onclick="<?php echo "goback($catid)";?>" >Back</button>
    <button type="button" class="w3-btn" title="Enable editing"
    onclick="enableit();">Edit</button>
    <button id='save' class="w3-btn butbut" title="Save changes"
    type="submit" disabled >Save</button>
</form>
<script>
enableit = function(){
    const ta = document.getElementById('area');
    ta.disabled = false;
    ta.title = 'Update text and press save to keep changes';
    ta.focus();
    event.stopImmediatePropagation();
    const fm = document.getElementById('fff');
    const sv = document.getElementById('save');
    save.disabled = false;

    console.log('enabled it');
}
goback = function(custid){
    console.log(custid);
    window.location.assign('cat.php?catid='+custid);
}
</script>

<?php
include ("inc/footer.php");
?>