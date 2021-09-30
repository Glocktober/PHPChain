<?php

$page="catview";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];
$now = time();
$noteid=0;

$output='';

$catid=sanigorp("catid");
$catidx = $catid;

if (!isset($catid) or is_null($catid) or empty($catid) or !(is_numeric($catid))){
    header("location: catlist.php");
    die();
}

$result=sql_query($db,"select id, iv, login, password, site, url, noteid, modified from logins where userid = \"$userid\" and catid = \"$catid\"");

if (sql_num_rows($result)==0) {
    # category has no entries, at least for this user
    $result=sql_query($db,"select id, title from cat where userid = \"$userid\" order by title");
    
    # lets see if they have ANY catagories:
    if (sql_num_rows($result)==0) error_out(
        "No categories exist for \"<b>$authed_login</b>\" - Create a category first",
            "catedit.php");

    # So - there are catagories but no entries with this id
    error_out("No password entries in the selected category. Create a password entry",
            "entedit.php?catid=$catid");

} 
if (!has_status()) set_status("Password entries for the selected category");

while ($row=sql_fetch_assoc($result)) {
    $login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
    $password=trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"])));
    $site=trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"])));
    $url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
    $resarray[]=array("id"=>$row["id"], "login"=>$login, "password"=>$password, "site"=>$site, 
    "url"=>$url,"noteid" => $row["noteid"], "modified" => $row["modified"] );
    $sortarray[]=$site;
}

array_multisort($sortarray, SORT_ASC, $resarray);

include ("inc/header.php");
$catid = $catidx;
?>

<div id="catview" class="w3-card w3-round">
<div class="div50" >
    <input oninput="w3.filterHTML('#cattable', '.trow', this.value)" placeholder='Filter entries...' class='w3-block  seafilter' title='Filter content'>
</div>
<TABLE  id=cattable class="w3-table w3-small w3-bordered">
<TR class='w3-pale-blue'>
<TD CLASS="header" WIDTH="25%" onclick="w3.sortHTML('#cattable','.trow', 'td:nth-child(1)')" title='Click to sort..'>Site <i style='font-size:15px' class='material-icons'>sort</i></TD>
<TD CLASS="header" WIDTH="15%" onclick="w3.sortHTML('#cattable','.trow', 'td:nth-child(2)')" title='Click to sort..'>Login <i style='font-size:15px' class='material-icons'>sort</i></TD>
<TD CLASS="header" WIDTH="25%">Password</TD>
<TD CLASS="header" WIDTH="fit-content">Actions</TD>
</TR>

<?php

foreach ($resarray as $val) {
    $modified = $val['modified'];
    $mod_time = 'Last modified: '. ($modified? strftime($time_format, $modified): "(the epoch)");
    $noteid = $val['noteid'];
    $noteclass = $noteid ? 'isgreen': 'isgrey';
    $notetip = $noteid ? 'View existing note' : 'Create a note';
    $noteicon = $noteid ? 'edit_note': 'note_add' ;
    $site = $val['site'];

    if (strlen($val["url"])>1) $outsite="<A HREF=\"".$val["url"]."\" TARGET=\"_blank\" title=\"Click to open URL\">".$val["site"]."</A>";
    else $outsite=$val["site"];

    $login = $val['login'];
    $password = $val['password'];
    $itemid = $val['id'];

    $valmap = ['itemid'=>$itemid, 'catid'=>$catid, 'noteid'=> $val['noteid'],
                'site'=>$site, 'csrftok'=>get_csrf()];

?>
<TR  class='w3-hover-light-grey trow'>
<TD CLASS="row" title="<?php echo $mod_time?>"><?php echo $outsite ?></TD>
<TD CLASS="row  login" title="Click to copy login"><span class=copyclick><?php echo $login ?></span></TD>
<TD  CLASS="row  password" title="Click to copy password"><span class=copyclick><?php echo $password ?></span></TD>
<TD CLASS="sea">
<?php 
echo icon_post('edit', '',"edi$itemid", 'entedit.php', $valmap, 'editicon', 'Edit this entry');
echo icon_post('delete', '', "del$itemid", 'entdelete.php', $valmap, 'delicon', 'Delete this entry');
echo icon_post($noteicon, '', "note$itemid", 'noteedit.php', $valmap, $noteclass, $notetip);
?>
</td><td>
</td></TR>
<?php 
}
?>
<tr><td colspan=3 width=100% class=w3-center>
<form action="entedit.php" id="newbut">
<input type="hidden" form="newbut" name="csrftok" value="<?php echo get_csrf()?>" >
<input type="hidden" form="newbut" name="catid" value="<?php echo $catid?>" >
<input type="hidden" form="newbut" name="itemid" value="0" >
<a href="javascript: void(0)" onclick="document.getElementById('newbut').submit()" 
    title="Add a new password entry" class='butbut w3-button w3-hover-pale-green'>
    <i class='material-icons addicon iconoffs'>add</i> New Entry</a>
</form>
</td></tr>
</TABLE>

<?php
include ("inc/footer.php");
?>