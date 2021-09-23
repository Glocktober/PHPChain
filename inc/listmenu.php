<?php
function getmenu($userid,$catid=NULL)
{
	$db = sql_conn();
	$menu='<ul class="w3-ul w3-small w3-boder"><li class="w3-small w3-light-grey" >Categories</li>';

	$result=sql_query($db,"select id, title from cat where userid = \"$userid\" order by title");

	while ($row=sql_fetch_assoc($result)) {
		if ($row["id"]==$catid) {
			$menu.='<li class="boldx w3-pale-blue w3-padding-small w3-hover-pale-blue catselitem">'.$row["title"]."</li>";
		} else {
			$menu.='<li class="w3-hover-pale-blue w3-padding-small catitem">';
			$menu.="<A CLASS=\"cat\" HREF=\"cat.php?catid=".$row["id"]."\">".$row["title"]."</A></li>\n";
		}
	}
	
	return $menu;
}
?>
