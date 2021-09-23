<?php
include ("inc/config.php");
include ("inc/form.php");

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

			if (strlen($title)==0){
				set_error("Error: Could not create or update: a category name is required.");
				header("Location: ".$_SERVER["PHP_SELF"]);
				die();				
			}
			if ($catid==0) {
				$query="insert into cat values (NULL, \"$userid\", \"$title\")";
			} else {
				$query="update cat set title=\"$title\" where userid=\"$userid\" and id=\"$catid\"";
			}
			if (!sql_query($db,$query))
				set_error("Error: saving category: ".sql_error($db));
			else set_status("Changes to \"<b>$title</b>\" saved");
			
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
				if (!sql_query($db,"delete from cat where id = \"$catid\" and userid=\"$userid\""))
					set_error("Error: Deleting catalog entry: ".sql_error($db));
				else set_status('Category Successfully Removed');
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
			$output.=input_text("title",30,255,$title,'plain focus',"The title for this category");
			$output.=submit("Save", '',"Save this category",'w3-hover-pale-green w3-border');
			$output.=form_end();

			if ($title) $msg = "Editing catagory \"$title\"";
			else $msg = "Creating new category";
			set_status($msg);
		break;
	}
} else {
	$result=sql_query($db,"select id, title from cat where userid = \"$userid\"");
	$output.="<input oninput=\"w3.filterHTML('#categorytable', '.sea', this.value)\" placeholder='Search...' style='width:35%'>";
	$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"1\" id=categorytable width='35%'>\n";
	$output.="<TR class=w3-pale-blue >\n";
	$output.="<TD CLASS=\"header\" WIDTH=\"200\" id=catcolumn onclick=\"w3.sortHTML('#categorytable','.trow', 'td:nth-child(1)')\" title='Click to sort..'>Category <i style='font-size:15px' class='material-icons'>&#xe164;</i></TD>\n";
	$output.="<TD CLASS=\"header\" WIDTH=\"50\" id=actioncolumn>Action</TD>\n";
	$output.="</TR>\n";

	while ($row=sql_fetch_assoc($result)) {
		$output.="<TR class=trow ><TD CLASS=\"row sea\"><a href=cat.php?catid=".$row["id"]." title=\"view password entries in this category\">".$row["title"]."</a></TD>\n";
		$output.="<TD CLASS=\"row sea\" style='display:inline-block'>";
		$output.= icon_button('<i style="font-size:20px" class="material-icons">&#xe254;</i>',$_SERVER["PHP_SELF"].'?action=edit&catid='.$row['id'].'&csrftok='.get_csrf(), "Edit category name", '');
		$output.= icon_button('<i style="font-size:20px" class="material-icons">&#xe872;</i>',$_SERVER["PHP_SELF"].'?action=delete&catid='.$row['id'].'&csrftok='.get_csrf(), "Delete (empty) category",'');
		$output.="</TD></TR>\n";
	}

	$output.="<tr><td colspan=2 class='w3-center'>";
	$output.=form_begin($_SERVER["PHP_SELF"],"POST");
	$output.=input_hidden("action","edit");
	$output.=input_hidden("catid","0");
	$output.=submit("Add another category", '', "Create a new category","w3-border w3-hover-pale-green");
	$output.=form_end();
	$output.='</td></tr>';
	$output.="</TABLE>\n<P>\n";
	$output.="<SCRIPT>w3.sortHTML('#categorytable','.trow', 'td:nth-child(1)');</SCRIPT>";
}

include ("inc/header.php");

echo $output;

include ("inc/footer.php");
?>
