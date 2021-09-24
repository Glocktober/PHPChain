<?php
function getmenu($userid,$catid=NULL)
{
	$db = sql_conn();
	$menu='';
	$menu="<input oninput=\"w3.filterHTML('#catlist', 'li', this.value)\" placeholder='Search categories...'>";
	$menu.='<ul id="catlist" class="w3-ul w3-small w3-boder">';

	$result=sql_query($db,"select id, title from cat where userid = \"$userid\" order by title");

	while ($row=sql_fetch_assoc($result)) {
		if ($row["id"]==$catid) {
			$menu.='<li class="boldx w3-pale-blue w3-padding-small w3-hover-pale-blue catitem">';
			$menu.="<A CLASS=\"cat\" HREF=\"cat.php?action=view&catid=".$row["id"]."\">".$row["title"]."</A></li>";
		} else {
			$menu.='<li class="w3-hover-pale-blue w3-padding-small catitem">';
			$menu.="<A CLASS=\"cat\" HREF=\"cat.php?action=view&catid=".$row["id"]."\">".$row["title"]."</A></li>";
		}
	}
	
	return $menu;
}
?>
