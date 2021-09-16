<?php
function getmenu($userid,$catid=NULL)
{
	$db = sql_conn();
	$menu="";

	$result=mysqli_query($db,"select id, title from cat where userid = \"$userid\" order by title");

	while ($row=mysqli_fetch_row($result)) {
		if ($row[0]==$catid) {
			$menu.="<TR><TD CLASS=\"ccat\"><SPAN CLASS=\"plain\">$row[1]</SPAN></TD></TR>\n";
		} else {
			$menu.="<TR><TD CLASS=\"cat\">";
			$menu.="<A CLASS=\"cat\" HREF=\"cat.php?catid=".$row[0]."\">".$row[1]."</A></TD></TR>\n";
		}
	}
	return $menu;
}
?>
