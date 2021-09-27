<?php
$page="catedit";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");

sql_conn();

$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];

$catid=sanigorp("catid");

if ($catid==0) {
    $title="";
    if (!has_status()) set_status("Adding a new category");
} else {
    $result=sql_query($db,"select title from cat where id = \"$catid\" and userid = \"$userid\"");
    $row=sql_fetch_assoc($result);
    $title=$row["title"];
    if (!has_status()) set_status("Rename category '$title'");
}

include("inc/header.php");
?>
<div>
<table class='formtable w3-card w3-round w3-margin w3-padding-16' width=80% >
    <tr>
        <td class=w3-center>
    <span CLASS='plain w3-medium'>Edit category:</span>
        </td>
        <td>
        <form action="catsave.php" method=POST class="w3-center" >
            <input type="hidden" name="catid" value=<?php echo $catid ?> >
            <input type="text" name="title" required maxlength=255 size=30 id="" 
                value="<?php echo $title; ?>"
                placeholder="Enter category title"
                title="Category title" class="plain focus">
            </td>
    </tr><tr><td>&nbsp;</td><td></td></tr><tr>
            <td class="w3-center" colspan=2 >
                <div class="w3-center w3-bar">
                    <?php echo icon_get($glyph_back, 'Back', 'catlist.php', [], 'butbut w3-border w3-hover-pale-green','Return'); ?>
                    <button type="submit" class='butbut w3-btn w3-hover-pale-red w3-border' title="Save this category"><i class='material-icons posticon'><?php echo $glyph_save?></i>&nbsp;Save</button>
                </div>
            </td>   
        </tr>
    </form>
</table>
</div>

<?php 
include ("inc/footer.php");
?>