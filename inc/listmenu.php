<?php
function getmenu($userid,$catid=NULL)
{
	$menu='';
	$menu="<input class='catsea w3-small' oninput=\"w3.filterHTML('#catlist', 'li', this.value)\" placeholder='Filter...'>";
	$menu.='<ul id="catlist" class="w3-small w3-ul">';
	
	$db = sql_conn();
	$result=sql_query($db,"select id, title from cat where userid = '$userid' order by title");

	while ($row=sql_fetch_assoc($result)) {
		$id = $row['id'];
		$title = $row['title'];
		if ($row["id"]==$catid) {
			$menu.="<li class='boldx w3-pale-blue w3-padding-small w3-hover-pale-blue catitem' title='selected'>";
			$menu.="<A CLASS='cat' HREF='catview.php?catid=$id' >$title</A></li>";
		} else {
			$menu.="<li class='w3-hover-pale-blue w3-padding-small catitem' title='view entries for $title'>";
			$menu.="<A CLASS='cat' HREF='catview.php?catid=$id'><span>$title</span></A></li>";
		}
	}
	
	return $menu;
}

?>
