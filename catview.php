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
$newvaljs = json_encode([
    'itemid'=>0, 
    'catid'=>$catid, 
    'noteid'=> 0,
    'site'=>'', 
    'csrftok'=>get_csrf()
]);
?>

<div id="catview" class="w3-card w3-round">
<div class="w3-bar" data='<?php echo $newvaljs?>'>
<span class="w3-btn w3-bar-item w3-left iconoffs w3-hover-pale-green" onclick="onpush(this,'entedit.php')"><i class='material-icons addicon iconoffs'
    >add</i> New Entry</span>
    <input oninput="w3.filterHTML('#cattable', '.trow', this.value)" placeholder='Filter entries...' 
        class='w3-border w3-bar-item  seafilter focus' title='Filter content'>
</div>
<table  id=cattable width="100%" class="w3-table w3-small w3-bordered">
<tr class='w3-pale-blue'>
<td class="header" width="30%" onclick="w3.sortHTML('#cattable','.trow', 'td:nth-child(1)')" title='Click to sort..'>Site <i style='font-size:15px' class='material-icons'>sort</i></td>
<td class="header" width="25%" onclick="w3.sortHTML('#cattable','.trow', 'td:nth-child(2)')" title='Click to sort..'>Login <i style='font-size:15px' class='material-icons'>sort</i></td>
<td class="header" width="25%">Password <i style='font-size:15px' class="material-icons">key</i> </td>
<td class="header" width="20%">Actions</td>
</tr>

<?php

foreach ($resarray as $val) {
    $modified = $val['modified'];
    $mod_time = 'Last modified: '. ($modified? strftime($time_format, $modified): "(the epoch)");
    $noteid = $val['noteid'];
    $noteclass = $noteid ? 'isgreen': 'isgrey';
    $notetip = $noteid ? 'View existing note' : 'Create a note';
    $noteicon = $noteid ? 'edit_note': 'note_add' ;
    $site = $val['site'];
    $url = $val['url'];

    if (strlen($val["url"])>1) $outsite="<A HREF=\"".$val["url"]."\" TARGET=\"_blank\" title=\"Click to open URL\">".$val["site"]."</A>";
    else $outsite=$val["site"];

    $login = $val['login'];
    $password = $val['password'];
    $itemid = $val['id'];
    $site = htmlspecialchars($site);
    $valjs = json_encode([
        'itemid'=>$itemid, 
        'catid'=>$catid, 
        'noteid'=> $val['noteid'],
        'site'=>$site, 
        'csrftok'=>get_csrf()
    ]);

    $dispjs = json_encode([
        'site' => $site,
        'modtime' =>$mod_time,
        'password'=>$password,
        'login'=>$login,
        'url'=>$url,
    ])

?>
<TR class='w3-hover-light-grey trow'>
<td class="row" title="<?php echo $mod_time?>"><?php echo $outsite ?></td>
<td class="row  login" title="Click to copy login" onclick="copyclip(this)"><span class="w3-block"><?php echo $login ?></span></td>
<td class="row  password" title="Click to copy password" onclick="copyclip(this)"><span class="w3-block"><?php echo $password ?></span></td>
<td class="sea" data='<?php echo $valjs?>' disp='<?php echo $dispjs?>'>

<i class="material-icons editicon" onclick="editpush(this,'entedit.php')" title="Details">zoom_in</i>
<i class="material-icons editicon" onclick="onpush(this,'entedit.php')" title="Edit this entry">edit</i>
<i class="material-icons <?php echo $noteclass?>" onclick="onpush(this,'noteedit.php')" title="<?php echo $notetip?>"><?php echo $noteicon?></i>
<i class="material-icons delicon" onclick="delpush(this,'entdelete.php','<?php echo $site?>')" title="Delete this entry">delete</i>

</td><td>
</td></tr>
<?php 
}?>
</table>
<!-- Hidden constructed form for posts   -->
<div class="w3-hide">
    <form action="#" id="datform" method="POST">
    <input type="text" name="itemid" >
    <input type="text" name="catid" >
    <input type="text" name="noteid" >
    <input type="text" name="csrftok" >
    <input type="text" name="site" >
</form>
</div>
<!-- Modal delete verification  -->
<div class="w3-modal" id="delver">
    <div class="w3-modal-content" id="delvermodal">
        <div class="w3-bar w3-teal w3-center">
            
            <h3 class="w3-center">Confirm delete!</h3>
        </div>
        <div class="w3-margin">
            <h4 id='delvertit' class="w3-center">placeholder</h4>
            <div id='delbar' class="w3-center" data=''>
                <button class="w3-large w3-hover-pale-green w3-button" onclick="document.getElementById('delver').style.display='none'">
                <i class="material-icons iconoffs">cancel</i> Cancel</button>
                <button class="w3-large w3-hover-pale-red w3-button" onclick="onpush(this,'entdelete.php')">
                <i class="material-icons iconoffs">delete</i> Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal detailed display -->
<div class="w3-modal" id="dispmod">
    <div class="w3-modal-content" id="dispdialog">
        <div class="w3-bar w3-teal w3-center">    
        <span onclick="document.getElementById('dispmod').style.display='none'"
      class="w3-button w3-display-topright w3-hover-pale-blue">&times;</span>
            <h3 class="w3-center">Password Entry Detail</h3>
        </div>
        <h4 id='dispmodif' class="w3-center w3-text-grey italic">placeholder</h4>
        <form action="" id="dispform" class="">
        <div class='w3-center w3-margin' >
            <label CLASS="plain labform" for="dispsite">Site:</label>
            <input id="dispsite" type="text" name="login" size="30" maxlen="255"
                value="" autocomplete="off" spellcheck="false"
                placeholder="None..." readonly onmouseup="inpclip(this)";
                class='' title='Click to copy site name' >
        </div><br>
        <div class='w3-center w3-margin' >
            <label CLASS="plain labform" for="dispurl">Site URL:</label>
            <input id="dispurl" type="text" name="url" size="30" maxlen="255"
                value="" autocomplete="off" spellcheck="false"
                placeholder="None..." readonly onmouseup="inpclip(this)";
                class='' title='Click to copy URL' >
        </div><br>
        <div class='w3-center w3-margin' >
            <label CLASS="plain labform" for="displogin">Login:</label>
            <input id="displogin" type="text" name="login" size="30" maxlen="255"
                value="" autocomplete="off" spellcheck="false"
                placeholder="None..." readonly onmouseup="inpclip(this)";
                class='' title='Click to copy login' >
        </div><br>
        <div class='w3-center w3-margin' >
            <label CLASS="plain labform" for="disppassword">Password:</label>
            <input id="disppassword" type="text" name="password" size="30" maxlen="255"
            value="w3-left" autocomplete="off" spellcheck="false"
            placeholder="None..." readonly onmouseup="inpclip(this)";
            class='password' title='Click to copy password' > 
        </div><br>
        <div class="w3-center w3-small">
            <p class="w3-text-grey"> <i class="w3-small material-icons">content_copy</i>
                <i>Click on a field to copy the contents to your clipboard</i></p>
        </div>
        <div class="w3-margin">
            <div id='dispbar' class="w3-center" data=''>
                <button type="button" class="w3-large w3-hover-pale-green w3-button" onclick="document.getElementById('dispmod').style.display='none'">
                <i class="material-icons iconoffs">close</i> Close</button>
                <button type="button" class="w3-large w3-hover-pale-red w3-button" onclick="onpush(this,'entedit.php')">
                <i class="material-icons iconoffs">edit</i> Edit...</button>
            </div>
        </div>
        </form>
    </div>
</div>
<script>
copyclip = function(el){
	const bg = el;
	const clip = bg.children[0].innerText;
    flashel(bg);
	navigator.clipboard.writeText(clip);
}

inpclip = function(el){
    const clip = el.value;
    flashel(el);
    navigator.clipboard.writeText(clip);
}

onpush = function(el,act){
    const jdat = JSON.parse(el.parentElement.getAttribute('data'))
    const fm = document.getElementById('datform')
    const chil = fm.children

    fm.action = act 
    for (k in jdat){
        chil[k].value = jdat[k];
    }
    console.log(act);
    fm.submit();
}

delpush = function(el,act,site){
    document.getElementById('delvertit').innerHTML = `Deleting password entry<br><b><i>"${site}"</i></b>`;
    const jdat = el.parentElement.getAttribute('data');
    document.getElementById('delbar').setAttribute('data',jdat);
    document.getElementById('delver').style.display = 'block';
}

editpush = function(el,act){
    const jdat = el.parentElement.getAttribute('data');
    const ddat = JSON.parse(el.parentElement.getAttribute('disp'));
    const df = document.getElementById('dispform');
    const chil = df.children;
    const modmes = ddat['modtime'];
    delete ddat['modtime'];
    document.getElementById('dispmodif').innerHTML = modmes;
    document.getElementById('dispbar').setAttribute('data',jdat);

    for (k in ddat){
        try{
            document.getElementById('disp'+k).value = ddat[k];
        } catch(e){ }
    }
    document.getElementById('dispmod').style.display = 'block';
}
</script>
<?php

include ("inc/footer.php");
?>