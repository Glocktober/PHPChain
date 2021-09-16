<?php
function checkcookies()
{
	if (array_key_exists('login',$_COOKIE)){
		$login=$_COOKIE["login"];
	}
	$key = 0;
	if (array_key_exists('key',$_COOKIE)){
		$key=$_COOKIE['key'];
	}
	if (array_key_exists('id',$_COOKIE)){
		$id=$_COOKIE['id'];
	}
	global $db;

	// Basic sanity checks first.
	if (empty($login)||empty($key)||empty($id)||($key=='deleted')||($id=='deleted')) {
		// Un-set cookies
		// Hack to deal with braindead browsers that don't unset properly.
		// Make sure cookie is full of garbage instead of the key.
		setcookie('key',md5('garbage').'x',time()-3600);
		setcookie('key','',time()-3600);
		setcookie('id','',time()-3600);
		return FALSE;
	} else {
		// Do some real checking.
		$result=mysqli_query($db,"select id, teststring, iv from user where name = \"$login\" and id = \"$id\"");
		if (mysqli_num_rows($result)==1) {
			$row=mysqli_fetch_row($result);
			if (testteststring(trim(decrypt($key,base64_decode($row[1]),base64_decode($row[2]))))) {
				// Mmmm! Good cookies!
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	// Just in case.
	return FALSE;
}

?>
