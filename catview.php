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

if (!isset($catid) or is_null($catid) or empty($catid) or !(is_numeric($catid)))
    error_out("Parameter error");

$catindex = [];
$catresult=sql_query($db,"select id, title from cat where userid = '$userid' order by title");
$number_cats = 0;

while($row=sql_fetch_assoc($catresult)){
    $catindex[$row['id']] = $row['title'];
    $number_cats++;
}

if ($number_cats==0) error_out(
    "No Folders exist for '<b>$authed_login</b>': Create a Folder",
        "catedit.php");

if (!array_key_exists($catid,$catindex)) error_out(
    "Access denied to this Folder");

$result=sql_query($db,"select id, iv, login, password, site, url, noteid, modified from logins where userid = \"$userid\" and catid = \"$catid\"");

$number_logins = sql_num_rows($result);
if ($number_logins==0) error_out("No password entries in the selected Folder. Create a password entry",
            "entedit.php?catid=$catid");

$catname = $catindex[$catid];


while ($row=sql_fetch_assoc($result)) {
    $iv = base64_decode($row["iv"]);
    $login=decrypt($key,$row["login"],$iv);
    $password=decrypt($key,$row["password"],$iv);
    $site=decrypt($key,$row["site"],$iv);
    $url=decrypt($key,$row["url"],$iv);
    $resarray[]=[
        "id"=>$row["id"], 
        "login"=>$login, 
        "password"=>$password, 
        "site"=>$site, 
        "url"=>$url,
        "noteid" => $row["noteid"], 
        "modified" => $row["modified"] 
    ];
} 

include ("inc/header.php");

$catid = $catidx;
$newvaljs = json_encode([
    'itemid'=>0, 
    'catid'=>$catid, 
    'noteid'=> 0,
    'site'=>'', 
    'csrftok'=>get_csrf()
], JSON_HEX_APOS|JSON_HEX_QUOT);
?>

<div id="catview" class="w3-card w3-round">
    <div class="w3-center topnote">
        <span class="summ"><?php echo "Folder </i><b>$catname</b><i> has </i><span class='w3-badge w3-border w3-pale-green'>$number_logins</span><i> password entries"?></span>
    </div>
<div class="w3-bar" data='<?php echo $newvaljs?>'>
<span class="w3-button w3-bar-item w3-left w3-hover-pale-green" title="Add a new password entry"
    onclick="onpush(this,'entedit.php')"><i class='material-icons addicon iconoffs'
    >add</i> Add Entry</span>
    <input oninput="w3.filterHTML('#cattable', '.trow', this.value)" placeholder='Filter Password Entries...' 
        class='w3-border w3-bar-item  w3-round seafilter focus' title='Filter content'>
</div> 
<table  id=cattable width="100%" class="w3-table w3-bordered w3-hoverable">
<tr class='w3-pale-green'>
<th id="sortfirst" class="header w3-center" width="30%" onclick="sortbycolumn(this)" title='Click to sort by site...'>Site <i class='material-icons micon'>sort</i></th>
<th class="header w3-center" width="25%" onclick="sortbycolumn(this)" title='Click to sort by login...'>Login <i class='material-icons micon'>sort</i></th>
<!-- <th class="header w3-center" width="25%" title="Hover over the box to reveal the password">Password <i class="material-icons micon">key</i></th> -->
<th class="header w3-center" width="20%" title="Select an action">Actions <i class="material-icons micon">category</i></th>
</tr>

<?php
function str7_starts_with($haystack, $needle){
    return strncmp($needle,$haystack,strlen($needle)) == 0;
}
function str7_contains($haystack, $needle){
    return strstr($haystack,$needle) == true;
}
foreach ($resarray as $val) {
    $modified = $val['modified'];
    $mod_time = 'Last modified: '. ($modified? strftime($time_format, $modified): "(the epoch)");
    $noteid = $val['noteid'];
    $noteclass = $noteid ? 'isgreen': 'isgrey';
    $notetip = $noteid ? 'Click to view attached note' : 'Click to attach a note';
    $noteicon = $noteid ? 'edit_note': 'note_add' ;
    $site = $val['site'];
    $url = $val['url'];

    if ( empty($url) or $url == 'http://' or $url == 'https://' ){
        # url is empty or just a scheme
        $outsite="<a href='#' title='Site name - no url'>$site</a>";
    } else {
        # has content, but what kind?
        $lurl = strtolower($url);
        if (str7_starts_with($lurl, 'http://') or str7_starts_with($lurl, 'https://'))
            # real url
            $outsite="<a href='$url' target='_blank' title='Click to open $url'>$site</a>";
        elseif (str7_contains($lurl, ' ') or !str7_contains($lurl,'.'))
            # not a valid URL - has a space or no dots
            $outsite="<a href='#' title='$url'>$site</p>";
        else 
            # close enough to a url - but has no scheme 
            $outsite = "<a href='https://$url' target='_blank' title='Click to open $url'>$site</a>";
    }

    $login = $val['login'];
    $password = $val['password'];
    $itemid = $val['id'];
    $site = htmlspecialchars($site);
    $datajs = json_encode([
        'itemid'=>$itemid, 
        'catid'=>$catid, 
        'noteid'=> $val['noteid'],
        'site'=>$site, 
        'csrftok'=>get_csrf()
    ], JSON_HEX_APOS|JSON_HEX_QUOT);

    $dispjs = json_encode([
        'site' => $site,
        'modtime' =>$mod_time,
        'password'=>$password,
        'login'=>$login,
        'url'=>$url,
    ], JSON_HEX_APOS|JSON_HEX_QUOT)

?>
<tr class='w3-hover-light-grey trow'>
<td class="row" title="<?php echo $mod_time?>"><?php echo $outsite ?></td>
<td class="row  login w3-center" title="Double click to copy login" ondblclick="copyclip(this)"><span class="w3-block"><?php echo $login ?></span></td>
<!-- <td class="row  password w3-center" title="Click to copy password" onclick="copyclip(this)"><span class="w3-block"><?php echo $password ?></span></td> -->
<td class="row w3-center" data='<?php echo $datajs?>' disp='<?php echo $dispjs?>'>
	
<i class="material-icons passicon" onclick="copyattr(this, 'password')" title="Click to copy password">key</i>&nbsp;&nbsp;&nbsp;
<i class="material-icons detailicon" onclick="editpush(this,'entedit.php')" title="Click for entry details">zoom_in</i>&nbsp;
<i class="material-icons editicon" onclick="onpush(this,'entedit.php')" title="Click to edit this entry">edit</i>&nbsp;
<i class="material-icons <?php echo $noteclass?>" onclick="onpush(this,'noteedit.php')" title="<?php echo $notetip?>"><?php echo $noteicon?></i>&nbsp;
<i class="material-icons delicon" onclick="delpush(this,'entdelete.php')" title="Click to delete this entry">delete</i>&nbsp;
<i class="material-icons selicon">chevron_left</i>
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

<!-- Modal dialog for delete verification  -->
<div class="w3-modal" id="delemodal">
    <div class="w3-modal-content" id="delvermodal">
        <div class="w3-bar w3-pale-green w3-center">
        <span onclick="document.getElementById('delemodal').style.display='none'"
      class="w3-button w3-display-topright w3-hover-teal">&times;</span>    
            <h3 class="w3-center">Confirm delete!</h3>
        </div>
        <div class="w3-margin">
            <h4 id='delvertit' class="w3-center">placeholder</h4>
            <div id='delbar' class="w3-center" data=''>
                <button class="w3-large w3-hover-pale-green w3-button" onclick="document.getElementById('delemodal').style.display='none'">
                <i class="material-icons iconoffs">cancel</i> Cancel</button>
                <button class="w3-large w3-hover-pale-red w3-button" onclick="onpush(this,'entdelete.php')">
                <i class="material-icons iconoffs">delete</i> Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal dialog for detailed display -->
<div class="w3-modal" id="dispmod">
    <div class="w3-modal-content" id="dispdialog">
        <div class="w3-bar w3-pale-green w3-center">    
        <span onclick="document.getElementById('dispmod').style.display='none'"
      class="w3-button w3-display-topright w3-hover-teal">&times;</span>
            <h3 class="w3-center">Password Entry Details</h3>
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
        <div class="w3-center">
            <p class="w3-text-grey"> <i class="w3-small material-icons">content_copy</i>
                <i>Click on a field to copy the contents to your clipboard</i></p>
        </div>
        <div class="w3-margin">
            <div id='dispbar' class="w3-center" data=''>
                <button type="button" class="w3-large w3-hover-pale-green w3-button" 
                    title="Cancel" onclick="document.getElementById('dispmod').style.display='none'">
                    <i class="material-icons iconoffs">close</i> Close</button>
                <button type="button" id="dispnoteedit" class="w3-large w3-hover-pale-green w3-button" 
                    title="View/Edit Notes" onclick="onpush(this,'noteedit.php')">
                    <i class="material-icons isgreen iconoffs">edit_note</i> Notes</button>
                <button type="button" class="w3-large w3-hover-pale-red w3-button" 
                    title="Edit this entry" onclick="onpush(this,'entedit.php')">
                    <i class="material-icons iconoffs">edit</i> Edit...</button>
            </div>
        </div>
        </form>
    </div>
</div>
<script>
copyattr = function(el, attr){
    const jdat = JSON.parse(el.parentElement.getAttribute('disp'))
    navigator.clipboard.writeText(jdat[attr]);
    flashmes(`Copied ${attr} for "<i>${jdat.site}</i>"`,1000);
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

delpush = function(el,act){
    const jdat = el.parentElement.getAttribute('data');
    const jd = JSON.parse(jdat);
    document.getElementById('delvertit').innerHTML = `Deleting password entry<br><b><i>"${jd.site}"</i></b>`;
    document.getElementById('delbar').setAttribute('data',jdat);
    document.getElementById('delemodal').style.display = 'block';
}

editpush = function(el,act){
    const jdat = el.parentElement.getAttribute('data');
    const jjdat = JSON.parse(jdat);
    const ddat = JSON.parse(el.parentElement.getAttribute('disp'));
    const df = document.getElementById('dispform');
    const chil = df.children;
    const modmes = ddat['modtime'];
    delete ddat['modtime'];
    document.getElementById('dispnoteedit').style.display = (jjdat.noteid == 0) ? "none" : "";;
    document.getElementById('dispmodif').innerHTML = modmes;
    document.getElementById('dispbar').setAttribute('data',jdat);

    for (k in ddat){
        try{
            document.getElementById('disp'+k).value = ddat[k];
        } catch(e){ }
    }
    document.getElementById('dispmod').style.display = 'block';
}

notepush = function(el,act){

}
//This off stackoverflow https://stackoverflow.com/questions/14267781/sorting-html-table-with-javascript
const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
    v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

// do the work...
sortbycolumn = function(th){
    const table = th.closest('table');
    Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
        .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
        .forEach(tr => table.appendChild(tr) );
};
sortbycolumn(document.getElementById('sortfirst'));
</script>
<?php

include ("inc/footer.php");
?>