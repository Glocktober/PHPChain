<?php

function gorp($fieldname)
{
	if (isset($_GET[$fieldname])) $return = $_GET[$fieldname];

	if (isset($return)) return $return;

	if (strtolower($_SERVER["REQUEST_METHOD"])=="post") {
		if (array_key_exists($fieldname,$_POST)) $return=$_POST[$fieldname];
	}
	if (isset($return)) return $return;
	return null;
}

function get_post($tag){
	$value = null;
	if (array_key_exists($tag,$_POST)){
		$value =$_POST[$tag];
		$value = trim($value);
		$value = htmlspecialchars($value);
	}
	return $value;
}

function sanigorp($tag){
	$tval = gorp($tag);
	$tval = trim($tval);
	$tval = htmlspecialchars($tval);
	return $tval;
}

?>
