<?php
// Configuration settings
$sql_vars = array(
	'dbname' => "'../test/test.db'",			// Database name
	'db' => 0, 				// Database handle
);
$db = 0;

function sql_conn()
{
	global $db;
	$db = new SQLite3($sql_vars['dbname']);
	return $db;
}
function restoarray($resdata)
{
	$n=0;
	while ($row=$resdata->fetchArray()){
		// mysqli_fetch_row($resdata)) {
		$data[$n]=$row;
		$n++;
	}
	return $data;
}

//function sqlite_num_rows($result)
function mysqli_num_rows($result)
{
	$nrows = 0;
	while ($result->fetchArray())
			$nrows++;
		$result->reset();
	return $nrows;
}

//function sqlite_query($db, $query)
function mysql_query($db, $query)
{
	return $db->query($query);
}

//function sqlite_fetch_row($result)
function mysql_fetch_row($result)
{
	return $result->fetchArray();
}

//function sqlite_fetch_assoc($result)
function mysql_fetch_assoc($result)
{
	return $result->fetchArray(SQLITE3_ASSOC);
}

//function sqlite_insert_id($db)
function mysql_insert_id($db)
{
	return $db->lastInsertRowId();
}

?>
