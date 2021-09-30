<?php

$page="catlist";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");

sql_conn();

$userid = $_SESSION['id'];
$authed_login = $_SESSION['login'];

if (!has_status()) set_status("List of categories");

if (!$result=sql_query($db,"select id, title from cat where userid = '$userid' order by title"))
    error_out("Error: ($page) retrieving categories: ".sql_error($db));

include ("inc/header.php");
?>

<div id="tabplane" class="tabplane w3-card" >
    <div class="div50">
        <input class="catsea seafilter" oninput="w3.filterHTML('#categorytable', '.trow', this.value)" 
            spellcheck="false" placeholder='Search categories...'>
    </div>
<TABLE class="w3-table w3-bordered w3-small"  id="categorytable" >
<TR class="w3-pale-blue" >
<TD CLASS=""  id="catcolumn" >Category</TD>
<TD CLASS=""  id="actioncolumn">Action</TD>
</TR>

<?php

while($row=sql_fetch_assoc($result)){
    $title=$row["title"];
    $catid=$row['id'];
    $valmap=['title'=>$title, 'catid'=>$catid];
    $href = "catview.php?catid=$catid"
?>
<TR class='trow w3-hover-light-grey' >
<TD CLASS="row sea">
    <a href=<?php echo $href?> title='view password entries in this category'><?php echo $title ?></a></TD><TD CLASS='row '>
    <?php echo icon_post('edit','',"ed$catid", 'catedit.php', $valmap, 'editicon', "Edit title"); ?>
    <?php echo icon_post('delete','',"del$catid", "catdelete.php", $valmap, 'delicon',"Delete category");?>
</TD></TR>
<?php
}
?>
<tr><td colspan=1 class="w3-center">
<form action="catedit.php" method="post">
<input type="hidden" name="csrf" value=<?php echo get_csrf()?>>
<input type="hidden" name="catid" value="0">
<button type=submit class='w3-button w3-hover-pale-green w3-small addbutton' title='Add a new category'><i class='material-icons addicon iconoffs'>add</i> New Category</button>
</form>
</td></tr>
</TABLE>
</div>
<?php
include ("inc/footer.php");
?>