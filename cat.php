<?php
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

$page="cat";

sql_conn();

$auth = is_authed();

if (!$auth) {
	header("Location: index.php");
	die();
}

$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];

$action=gorp("action");

if (empty($action)) $action="view";
$output="";

if ($action != "view") check_csrf();
switch($action) {
	case "delete":
		$catid=sanigorp("catid");
		$itemid=sanigorp("itemid");

		sql_query($db,"delete from logins where id = \"$itemid\" and userid = \"$userid\"");

		set_status("Successfully deleted password entry");
		header("Location: cat.php?action=view&catid=".$catid);
		die();
	break;
	case "view":
		$catid=sanigorp("catid");

		$result=sql_query($db,"select id, iv, login, password, site, url from logins where userid = \"$userid\" and catid = \"$catid\"");

		if (sql_num_rows($result)==0) {
			# category has no entries, at least for this user
			$result=sql_query($db,"select id, title from cat where userid = \"$userid\" order by title");
			
			# lets see if they have ANY catagories:
			if (sql_num_rows($result)==0){
				# User has no categories - new user
				set_error("No categories exist for \"<b>$authed_login</b>\" - Create a category first");
				header("Location: settings.php?action=edit&csrftok=".get_csrf());
				die();
			}

			# So - there are catagories but no entries with this id
			$error = "No password entries in the selected category. Create a password entry:";
			set_error($error);
			header("Location: cat.php?action=edit&catid=$catid&csrftok=".get_csrf());
			die();
		} else {
			set_status("Hover over password to reveal. Click on <b>Login</b> or <b>Password</b> entry to copy to clipboard. Click on Site to open URL");
			$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"1\" id=cattable class=\"w3-table w3-small w3-border\">\n";
			$output.="<TR class='w3-pale-blue'>\n";
			$output.="<TD CLASS=\"header\" WIDTH=\"25%\">Site</TD>\n";
			$output.="<TD CLASS=\"header\" WIDTH=\"15%\">Login</TD>\n";
			$output.="<TD CLASS=\"header\" WIDTH=\"25%\">Password</TD>\n";
			$output.="<TD CLASS=\"header\" WIDTH=\"fit-content\">Actions</TD>\n";
			$output.="</TR>\n";

			while ($row=sql_fetch_assoc($result)) {
				$login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
				$password=trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"])));
				$site=trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"])));
				$url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
				$resarray[]=array("id"=>$row["id"], "login"=>$login, "password"=>$password, "site"=>$site, "url"=>$url);
				$sortarray[]=$site;
			}

			array_multisort($sortarray, SORT_ASC, $resarray);

			foreach ($resarray as $val) {
				if (strlen($val["url"])>1) $outsite="<A HREF=\"".$val["url"]."\" TARGET=\"_blank\" title=\"Click to open URL\">".$val["site"]."</A>";
				else $outsite=$val["site"];
				$output.="<TR  class='w3-hover-light-grey'>";
					$output.="<TD CLASS=\"row\" title=\"Site URL ".$val["url"]."\" >".$outsite."</TD>\n";
					$output.="<TD CLASS=\"row login copyclick\" title=\"Click to copy login\">".$val["login"]."</TD>\n";
					$output.="<TD  CLASS=\"password copyclick\" title=\"Click to copy password\">".$val["password"]."</TD>\n";
					$output.="<TD CLASS=\"row\">";
					$output.=action_button('Edit',$_SERVER["PHP_SELF"]."?action=edit&itemid=".$val["id"]."&csrftok=".get_csrf(), "Edit this password entry", "w3-light-grey w3-hover-pale-red");
					$output.=action_button('Delete',$_SERVER["PHP_SELF"]."?action=delete&itemid=".$val["id"]."&catid=".$catid."&csrftok=".get_csrf(),"Delete this password entry","w3-light-grey w3-hover-pale-red");
				$output.="</TR>";
			}
			$output.="<tr><td COLSPAN=4 width=100% class=w3-center>";
			$output.=action_button('Create a New Password Entry',
			"cat.php?action=edit&catid=".$catid."&csrftok=".get_csrf(),
			"Add a new entry to this category", "w3-border w3-hover-pale-green");
			$output.="</tr></TABLE>";
		}
	break;
	case "edit":
		$itemid=sanigorp("itemid");
		
		if ($itemid!=0) {
			set_status("Editing password entry");
			//Get existing data and decrypt it first.
			$result=sql_query($db,"select id, iv, catid, login, password, site, url from logins where id = \"$itemid\" and userid=\"$userid\"");
			if (sql_num_rows($result)==1) {
				$row=sql_fetch_assoc($result);
				$catid=$row["catid"];
				$login=trim(decrypt($key,base64_decode($row["login"]),base64_decode($row["iv"])));
				$password=trim(decrypt($key,base64_decode($row["password"]),base64_decode($row["iv"])));
				$site=trim(decrypt($key,base64_decode($row["site"]),base64_decode($row["iv"])));
				$url=trim(decrypt($key,base64_decode($row["url"]),base64_decode($row["iv"])));
			} else {
				$output="<SPAN CLASS=\"error\">No permission to edit this entry</DIV><P>";
				$login="";
				$password="";
				$site="";
				$url="";
				set_error("Error: Not authorized");
			}
		} else {
			set_status("Creating new password entry");
			$catid=sanigorp("catid");
			$login="";
			$password="";
			$site="";
			$url="";
		}

		// Get categories.
		$result=sql_query($db,"select id, title from cat where userid = \"$userid\" order by title");
		if (sql_num_rows($result)==0) {
			set_error("No categories exist for \"<b>$authed_login</b>\" - Create a category first");
			header("Location: settings.php?action=edit&csrftok=".get_csrf());
			die();
		} else {
			$cats=restoarray($result);

			$output.=form_begin($_SERVER["PHP_SELF"],"POST");
			$output.=input_hidden("itemid",$itemid);
			$output.=input_hidden("action","save");
			$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">\n";
			$output.="<TR><TD CLASS=\"plain\">Category: </TD><TD CLASS=\"plain\">".input_select("catid",$catid,$cats,'plain','Select the category for this entry')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain\"><span class=error>*</span>Site: </TD><TD CLASS=\"plain focus\">".input_text("site",30,255,$site,'', 'required: the sight name to identify this entry')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain\">URL: </TD><TD CLASS=\"plain\">".input_text("url",30,255,$url,'','the URL for this site')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain\">Login: </TD><TD CLASS=\"plain\">".input_text("login",30,255,$login,'', 'The username for this entry')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain\">Password: </TD><TD CLASS=\"plain\">".input_text("password",30,255,$password,'password', 'The password for this entry')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain w3-center\"  COLSPAN=\"2\">".submit("Save entry",'','Save/update this entry',"w3-border w3-hover-pale-green")."</TD></TR>\n";
			$output.="</TABLE>\n";
			$output.=form_end();
		}
	break;

	case "save":
		$itemid=sanigorp("itemid");
		$catid=sanigorp("catid");
		$login=sanigorp("login");
		$password=sanigorp("password");
		$site=sanigorp("site");
		$origsite=$site;
		$url=sanigorp("url");

		if (strlen($site)==0){
			set_error('Error: Can not create password entry: Specifiy a sight');
			header("Location: ".$_SERVER["PHP_SELF"] . "?action=edit&catid=". $catid . "&csrftok=".get_csrf());
			die();
		}

		if (strpos($url,"http://")===FALSE && strpos($url,"https://")===FALSE) $url="https://".$url;

		// Encrypt login and pass using key.
		$iv=make_iv();
		$login=base64_encode(encrypt($key,$login,$iv));
		$password=base64_encode(encrypt($key,$password,$iv));
		$site=base64_encode(encrypt($key,$site,$iv));
		$url=base64_encode(encrypt($key,$url,$iv));
		$iv=base64_encode($iv);

		if ($itemid==0) {
			$query="insert into logins values (NULL, \"$iv\", \"$userid\", \"$catid\", \"$login\", \"$password\", \"$site\", \"$url\")";
		} else {
			$query="update logins set iv = \"$iv\", catid=\"$catid\", login=\"$login\", password=\"$password\", site = \"$site\", url = \"$url\" where id = \"$itemid\" and userid=\"$userid\"";
		}
		sql_query($db,$query);

		set_status("Entry \"<b>$origsite</b>\" has been updated");
		header("Location: cat.php?catid=".$catid);
		die();
	break;
}

include ("inc/header.php");

echo $output;

include ("inc/footer.php");

?>
