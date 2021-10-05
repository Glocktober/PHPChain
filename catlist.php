<?php

$page="catlist";
$reqauth=true;

include ("inc/config.php");
include ("inc/form.php");

sql_conn();

$userid = $_SESSION['id'];
$authed_login = $_SESSION['login'];

if (!has_status()) set_status("List of Folders");

if (!$result=sql_query($db,"select id, title from cat where userid = '$userid'"))
    error_out("Error: ($page) retrieving folders: ".sql_error($db));

include ("inc/header.php");
?>

<div id="tabplane" class="tabplane w3-card" >
<div class="w3-center">
        <span class="summ"><?php echo "$catcount Folders"?></span>
    </div>
    <div class="w3-bar">
    <button type=submit form="addcat" class='w3-button w3-bar-item w3-hover-pale-blue  addbutton' 
    title='Add a new Folder'>
    <i class='material-icons addicon iconoffs'>create_new_folder</i> Add Folder</button>
        <input class="catsea focus seafilter w3-bar-item w3-border" oninput="w3.filterHTML('#categorytable', '.trow', this.value)" 
            spellcheck="false" placeholder='Search Folders...'>
    </div>
<table class="w3-table w3-bordered" id="categorytable" >
<tr class="w3-pale-blue" >
<td class=""  id="catcolumn" onclick="w3.sortHTML('#categorytable','.trow', 'td:nth-child(1)')" 
    title='Click to sort..'>&nbsp;&nbsp;Folder <i class='material-icons micon'>sort</i></td>
<td class="w3-center"  id="actioncolumn" title="Choose an action">Action <i class="material-icons micon">category</i></td>
</tr>

<?php

while($row=sql_fetch_assoc($result)){
    $title=$row["title"];
    $catid=$row['id'];
    $valjs=json_encode([
        'title'=>$title, 
        'catid'=>$catid,
        'csrftok'=>get_csrf(),
    ]);
    $href = "catview.php?catid=$catid";
?>
<tr class='trow w3-hover-light-grey' >
<td class="row sea" >
    <a href=<?php echo $href?> title='view password entries in this Folder'><?php echo $title ?></a></td>
<td class='row w3-center' data='<?php echo $valjs?>'>
    <span class="w3-hover-pale-green" onclick="onpush(this,'catedit.php')"
        title="Edit the Folder name">
        <i class="material-icons editicon">edit</i>
    </span>
    <span class="w3-hover-pale-red" onclick="onpush(this,'catdelete.php')"
        title="Delete this Folder">
        <i class="material-icons delicon">delete</i>
    </span>
    <span >
        <a class="w3-hover-pale-green ainl" href="entedit.php?catid=<?php echo $catid?>"
            title="Add a password entry to <?php echo $title?>"><i class="material-icons addicon">add</i></a>
    </span>
</td></tr>
<?php
}
?>
<tr><td colspan=1 class="w3-center">
    
    </td></tr>
</table>
</div>
<div class="w3-hide">
    <form id="addcat" action="catedit.php" method="post">
        <input type="hidden" name="csrf" value=<?php echo get_csrf()?>>
        <input type="hidden" name="catid" value="0">
    </form>
</div>
<!-- Hidden constructed form for posts   -->
<div class="w3-hide">
    <form action="#" id="datform" method="POST">
    <input type="text" name="title" >
    <input type="text" name="catid" >
    <input type="text" name="csrftok" >
</form>
</div>
<script>
onpush = function(el,act){
    const jdat = JSON.parse(el.parentElement.getAttribute('data'))
    const fm = document.getElementById('datform')
    const chil = fm.children

    fm.action = act 
    for (k in jdat){
        chil[k].value = jdat[k]
    }
    console.log(act)
    fm.submit()
}

setTimeout(() => {
    w3.sortHTML('#categorytable','.trow', 'td:nth-child(1)');
}, 0);
</script>
<?php
include ("inc/footer.php");
?>