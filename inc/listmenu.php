<?php
function getmenu($userid,$catid=NULL,&$count)
{
	$menu='';
	$menu="<input class='seafilter w3-round w3-border fullw' oninput=\"w3.filterHTML('#catlist', 'li', this.value)\" placeholder='Filter Navigation List...'>";
	$menu.='<ul id="catlist" class="w3-smal w3-ul">';
	
	$db = sql_conn();
	$result=sql_query($db,"select id, title from cat where userid = '$userid'");
	
	$count = sql_num_rows($result);

	while ($row=sql_fetch_assoc($result)) {
		$id = $row['id'];
		$title = $row['title'];
		if ($row["id"]==$catid) {
			$menu.="<li class='w3-pale-blue w3-padding-small w3-border catitem' title='Selected folder'>";
			$menu.="<a class='cat' href='catview.php?catid=$id' >$title&nbsp;&nbsp;<i class='iconchev material-icons' style='font-size:15px;'>format_list_bulleted</i></a></li>";
		} else {
			$menu.="<li class='w3-hover-pale-blue w3-padding-small catitem' title='Open folder $title'>";
			$menu.="<a class='cat' href='catview.php?catid=$id'><span>$title</span></a></li>";
		}
	}
	global $localview;
	global $localviewname;
	if (isset($localview) and $localview){
		if (file_exists($localview) and is_readable($localview)){
			if (!isset($localviewname) or empty($localviewname))
				$localviewname = 'xyzzy';
			$menu.="<li class='w3-hover-pale-blue w3-padding-small catitem' title='Open $localviewname'>";
			$menu.="<a class='cat' href='$localview'><span>$localviewname</span></a></li>";
		}
	}
	
	return $menu;
}

?>
