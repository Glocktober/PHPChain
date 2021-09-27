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
        <input class='catsea' oninput="w3.filterHTML('#categorytable', '.trow', this.value)" placeholder='Search categories...'>
    </div>
<TABLE class='w3-table w3-bordered w3-card w3-small' BORDER="0" CELLPADDING="2" CELLSPACING="1" id=categorytable >
<TR class=w3-pale-blue >
<TD CLASS="header"  id=catcolumn >Category</TD>
<TD CLASS="header" dth=10em id=actioncolumn>Action</TD>
</TR>

<?php

while($row=sql_fetch_assoc($result)){
    $title=$row["title"];
    $catid=$row['id'];
    $valmap=['title'=>$title, 'catid'=>$catid];
    echo "<TR class='trow w3-hover-light-grey' ><TD CLASS=\"row sea\"><a href='catview.php?catid=$catid' title='view password entries in this category'>$title</a></TD>";
    echo "<TD CLASS='row'>";
    echo icon_post($glyph_edit,'',"ed$catid", 'catedit.php', $valmap, 'editicon', "Edit title");
    echo icon_post($glyph_delete,'',"del$catid", "catdelete.php", $valmap, 'delicon',"Delete category");
    echo "</TD></TR>";
}

echo "<tr><td colspan=2 class='w3-center'>";
echo form_begin('catedit.php',"POST");
echo input_hidden("action","edit").input_hidden("catid","0");
echo "<button type=submit class='w3-button w3-border w3-hover-pale-green addbutton' title='Add a new category'><i class='material-icons addicon iconoffs'>$glyph_add</i> New category</button>";
echo form_end();
echo '</td></tr>';
?>
</TABLE>
</div>
<?php
include ("inc/footer.php");
?>