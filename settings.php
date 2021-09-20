<?php
include ("inc/config.php");
include ("inc/form.php");

$page="settings";

sql_conn();

$auth = is_authed();

if (!$auth) {
	header("Location: index.php");
	die();
}

$action=gorp("action");
$userid = $_SESSION['id'];

$output="";

if (isset($action)) {
		
	switch ($action) {
		case "save":
			check_csrf();

			$catid=get_post("catid");
			$title=get_post("title");
			if ($catid==0) {
				$query="insert into cat values (NULL, \"$userid\", \"$title\")";
			} else {
				$query="update cat set title=\"$title\" where userid=\"$userid\" and id=\"$catid\"";
			}
			sql_query($db,$query);
			set_status("Changes to \"<b>$title</b>\" saved");
			header("Location: ".$_SERVER["PHP_SELF"]);
			die();
		break;

		case "delete":
			check_csrf();

			# Check to see if this cat is used.
			$catid=sanigorp("catid");
			$result=sql_query($db,"select count(id) from logins where userid=\"$userid\" and catid=\"$catid\"");
			$row=sql_fetch_row($result);
			if ($row[0]>0) {
				set_error('Unable to delete. Remove login entries from category first');
			} else {
				sql_query($db,"delete from cat where id = \"$catid\" and userid=\"$userid\"");
				set_status('Category Successfully Removed');
			}
			header("Location: ".$_SERVER["PHP_SELF"]);
			die();
		break;

		case "edit":

			$catid=sanigorp("catid");
			if ($catid==0) {

				check_csrf();
				
				$title="";
			} else {
				$result=sql_query($db,"select title from cat where id = \"$catid\" and userid = \"$userid\"");
				$row=sql_fetch_assoc($result);
				$title=$row["title"];
			}

			$output.="<P CLASS=\"plain\">\n";
			$output.="Edit category:\n";
			$output.="<P CLASS=\"plain\">\n";
			$output.=form_begin($_SERVER["PHP_SELF"],"POST","settings");
			$output.=input_hidden("action","save");
			$output.=input_hidden("catid",$catid);
			$output.=input_text("title",30,255,$title);
			$output.=submit("Save");
			$output.=form_end();

			if ($title) $msg = "Editing catagory \"$title\"";
			else $msg = "Creating new category";
			set_status($msg);
		break;
	}
} else {
	$result=sql_query($db,"select id, title from cat where userid = \"$userid\"");

	$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"1\">\n";
	$output.="<TR>\n";
	$output.="<TD CLASS=\"header\" WIDTH=\"200\">Category</TD>\n";
	$output.="<TD CLASS=\"header\" WIDTH=\"150\">Action</TD>\n";
	$output.="</TR>\n";

	while ($row=sql_fetch_assoc($result)) {
		$output.="<TR><TD CLASS=\"row\"><a href=cat.php?catid=".$row["id"].">".$row["title"]."</a></TD>\n";
		$output.="<TD CLASS=\"row\">";
			$output.="<A HREF=\"".$_SERVER["PHP_SELF"]."?action=edit&catid=".$row["id"]."&csrftok=".get_csrf()."\">Edit</A> | ";
		   	$output.="<A HREF=\"".$_SERVER["PHP_SELF"]."?action=delete&catid=".$row["id"]."&csrftok=".get_csrf()."\">Delete</A>";
		$output.="</TD></TR>\n";
	}

	$output.="</TABLE>\n<P>\n";

	$output.=form_begin($_SERVER["PHP_SELF"],"POST");
	$output.=input_hidden("action","edit");
	$output.=input_hidden("catid","0");
	$output.=submit("New category");
	$output.=form_end();
}

include ("inc/header.php");

echo $output;

include ("inc/footer.php");
?>
