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
    $mast = 'Adding a new category';
} else {
    $result=sql_query($db,"select title from cat where id = \"$catid\" and userid = \"$userid\"");
    $row=sql_fetch_assoc($result);
    $title=$row["title"];
    $mast = "Category \"$title\"";
}
if (!has_status()) set_status($mast);

include("inc/header.php");
?>
<div class="w3-card cardpanel">
<div class=" w3-padding-16 ">
    
<div class='w3-center w3-padding-16 fullw' >
    <form action="catsave.php" method=POST class="w3-center" >
    <input type="hidden" name="catid" value=<?php echo $catid ?> >
    <span  class="w3-center txtgrey" ><?php echo $mast?></span>
</div>
<div class='w3-center fullw' >
<label class="plain labform" for="title">Category Title:</label>
<input type="text" name="title" required maxlength=255 size=30 id="title" 
    value="<?php echo $title; ?>"
    placeholder="Enter category title"
    title="Category title" class="plain focus">
</div>
<div class="w3-center w3-bar w3-margin-top">
    <?php echo icon_get('chevron_left', 'Back', 'catlist.php', [], 'butbut w3-hover-pale-green','Return','backicon iconoffs'); ?>
    <button type="submit" class='butbut w3-btn w3-hover-pale-red ' title="Save this category"><i class='material-icons saveicon iconoffs'>check_circle</i>&nbsp;Save</button>
</form>
</div>
</div>
<?php 
include ("inc/footer.php");
?>