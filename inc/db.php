<?php
// Configuration settings
$sql_vars = array(
	'host' => "localhost",			// MySQL host
	'username' => "chain",			// Username
	'password' => "ch@1n",			// Password
	'dbname' => "chain",			// Database name
	'db' => 0, 				// Database handle
);
$db = 0;
function sql_conn()
{
	global $sql_vars;
	global $db;

	$db = mysqli_connect($sql_vars['host'], $sql_vars['username'], $sql_vars['password']) or die(mysqli_error($db));
	$sql_vars['db'] = $db;
	mysqli_select_db($db, $sql_vars['dbname']);
	return $db;
}

function restoarray($resdata)
{
	$n=0;
	while ($row=mysqli_fetch_row($resdata)) {
		$data[$n]=$row;
		$n++;
	}
	return $data;
}

?>
