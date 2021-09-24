<?php
$page="cat";
$reqauth=true;
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");


sql_conn();

$auth = is_authed();

if (!$auth) {
	header("Location: index.php");
	die();
}

$userid = $_SESSION['id'];
$key = $_SESSION['key'];
$authed_login = $_SESSION['login'];
$now = time();

$action=gorp("action");

if (empty($action)) $action="";
$output="";

if ($action and $action != "view") check_csrf();
switch($action) {
	case "delete":
		$catid=sanigorp("catid");
		$itemid=sanigorp("itemid");

		$query = "delete from logins where id = \"$itemid\" and userid = \"$userid\"";
		if (!sql_query($db,$query)) 
			set_error("Error: deleting password entry: ".sql_error($db));
		else set_status("Successfully deleted password entry");
		
		header("Location: cat.php?action=view&catid=".$catid);
		die();
	break;
	case "view":
		$catid=sanigorp("catid");

		$result=sql_query($db,"select id, iv, login, password, site, url, noteid, modified from logins where userid = \"$userid\" and catid = \"$catid\"");

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
			
			$output.="<input oninput=\"w3.filterHTML('#cattable', '.trow', this.value)\" placeholder='Search this category...' class='w3-block' title='Filter content'>";
			$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"1\" id=cattable class=\"w3-table w3-small w3-border\">\n";
			$output.="<TR class='w3-pale-blue'>\n";
			$output.="<TD CLASS=\"header\" WIDTH=\"25%\" onclick=\"w3.sortHTML('#cattable','.trow', 'td:nth-child(1)')\" title='Click to sort..'>Site <i style='font-size:15px' class='material-icons'>&#xe164;</i></TD>\n";
			$output.="<TD CLASS=\"header\" WIDTH=\"15%\" onclick=\"w3.sortHTML('#cattable','.trow', 'td:nth-child(2)')\" title='Click to sort..'>Login <i style='font-size:15px' class='material-icons'>&#xe164;</i></TD>\n";
			$output.="<TD CLASS=\"header\" WIDTH=\"25%\">Password</TD>\n";
			$output.="<TD CLASS=\"header\" WIDTH=\"fit-content\">Actions</TD>\n";
			$output.="</TR>\n";

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

			foreach ($resarray as $val) {

				$mod_time = 'Modified: '.strftime($time_format, $val['modified']);

				if (strlen($val["url"])>1) $outsite="<A HREF=\"".$val["url"]."\" TARGET=\"_blank\" title=\"Click to open URL\">".$val["site"]."</A>";
				else $outsite=$val["site"];
				
				$output.="<TR  class='w3-hover-light-grey trow'>";
					$output.="<TD CLASS=\"row \" title=\"$mod_time\" >".$outsite."</TD>\n";
					$output.="<TD CLASS=\"row  login copyclick\" title=\"Click to copy login\">".$val["login"]."</TD>\n";
					$output.="<TD  CLASS=\"row  password copyclick\" title=\"Click to copy password\">".$val["password"]."</TD>\n";
					$output.="<TD CLASS=\"sea\">";
					$output.=icon_button('<i style="font-size:20px" class="material-icons editicon">&#xe254;</i>',$_SERVER["PHP_SELF"]."?action=edit&itemid=".$val["id"]."&csrftok=".get_csrf(), "Edit this password entry");
					$output.=icon_button('<i style="font-size:20px" class="material-icons editicon">&#xe872;</i>',$_SERVER["PHP_SELF"]."?action=delete&itemid=".$val["id"]."&catid=".$catid."&csrftok=".get_csrf(),"Delete this password entry");
					$output.="<i class='material-icons iconoffs infoicon' title=\"$mod_time\">&#xe88e;</i>";
					$output.=icon_button('<i style="font-size:20px" class="material-icons editicon">&#xe745;</i>',"notes.php?action=view&itemid=".$val["id"]."&catid=".$catid."&noteid=".$val['noteid']."&csrftok=".get_csrf(),"Manage notes for this entry");
				$output.="</TR>";
			}
			$output.="<tr><td COLSPAN=4 width=100% class=w3-center>";
			$output.=action_button('Create a New Password Entry',"cat.php?action=edit&catid=".$catid."&csrftok=".get_csrf(),"Add a new entry to this category", "w3-border w3-hover-pale-green");
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
			$backurl = (isset($catid) and !is_null($catid)) ? "cat.php?action=view&catid=$catid" : "index.php";
			
			$output.=form_begin($_SERVER["PHP_SELF"],"POST");
			$output.=input_hidden("itemid",$itemid);
			$output.=input_hidden("action","save");
			$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">";
			$output.="<TR><TD CLASS=\"plain\">Category: </TD><TD CLASS=\"plain\">".input_select("catid",$catid,$cats,'plain locked','Select the category for this entry')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain\"><span class=error>*</span>Site: </TD><TD CLASS=\"plain\">".input_text("site",30,255,$site,'locked', 'required: the sight name to identify this entry')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain\">URL: </TD><TD CLASS=\"plain\">".input_text("url",30,255,$url,'locked','the URL for this site')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain\">Login: </TD><TD CLASS=\"plain\">".input_text("login",30,255,$login,'locked', 'The username for this entry')."</TD></TR>\n";
			$output.="<TR><TD CLASS=\"plain\">Password: </TD><TD CLASS=\"plain\">".input_text("password",30,255,$password,'locked password', 'The password for this entry')."</TD></TR>\n";
			$output.="<TR class=w3-center><TD CLASS=\"plain w3-center\"  COLSPAN=\"2\">&nbsp;&nbsp;<a class='butbut w3-button w3-border w3-hover-pale-green' href=\"$backurl\" title='Make No Changes'>Back</a>&nbsp;";
			$output.="<a class='butbut w3-button w3-border w3-hover-pale-green' onclick='doable(false);', type=button title='Enable editing'>Edit</a>&nbsp;";
			$output.=submit("Save",'','Save Changes',"w3-border w3-hover-pale-red locked")."</TD></TR>\n";
			$output.="</TABLE>";
			$output.=form_end();
			$output.="<script>";
			$output.="
			doable = function(flag){
				console.log('flag',flag);
				const lck = document.getElementsByClassName('locked');
				const n = lck.length;
				for (let i=0;i<n;i++ ) { 
					lck.item(i).disabled = flag;
				}
			}
			/*tid = setTimeout(doable,0,true);
			*/
			doable(true);
			";
			$output.="</script>";
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
			$query="insert into logins values (NULL, \"$iv\", \"$userid\", \"$catid\", \"$login\", \"$password\", \"$site\", \"$url\", \"0\", $now, $now)";
		} else {
			$query="update logins set iv = \"$iv\", catid=\"$catid\", login=\"$login\", password=\"$password\", site = \"$site\", url = \"$url\", modified = $now where id = \"$itemid\" and userid=\"$userid\"";
		}
		if (!sql_query($db,$query)) set_error("Error: saving entry \"<b>$origsite</b>\": ".sql_error($db));
		else set_status("Entry \"<b>$origsite</b>\" has been updated");

		header("Location: cat.php?action=view&catid=".$catid);
		die();
	break;

	default:
		$output.="<table width='fit-content' ><tr><td>";
		$output.="<p>Select a category from the left column, or create a new category.</p></td></tr>";
		$output.="<tr><td class=w3-center><a class='w3-btn w3-border w3-hover-pale-green butbut' href='settings.php' title='Manage categories'>Manage Categories</a></td></tr></table>";
}

include ("inc/header.php");

echo $output;

include ("inc/footer.php");

?>
