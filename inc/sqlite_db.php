<?php

$db = 0;

function sql_conn()
{
	global $db;
	global $sqlite_db_path;
	$db = new SQLite3($sqlite_db_path);
	return $db;
}

function sql_log($msg){
	global $db_log_queries;
	if ($db_log_queries) error_log($msg);
}

function restoarray($resdata)
{
	$n=0;
	while ($row=$resdata->fetchArray()){
		$data[$n]=$row;
		$n++;
	}
	return $data;
}

function sql_close($db)
{
	return $db->close();
}

function sql_errno($db)
{
	return $db->lastErrorCode();
}

function sql_error($db)
{
	return $db->lastErrorMsg();
}

function sql_num_rows($result)
{
	$nrows = 0;
	while ($result->fetchArray())
			$nrows++;
		$result->reset();
	return $nrows;
}

function sql_query($db, $query)
{
	sql_log('sqlite query: ' . $query);
	return $db->query($query);
}

function sql_fetch_row($result)
{
	return $result->fetchArray();
}

function sql_fetch_assoc($result)
{
	return $result->fetchArray(SQLITE3_ASSOC);
}

function sql_insert_id($db)
{
	return $db->lastInsertRowId();
}

?>
