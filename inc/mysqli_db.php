<?php

$db = 0;

function sql_conn(){
	
	global $mysql_host;
	global $mysql_username;
	global $mysql_password;
	global $mysql_db_name;

	global $db;

	$db = mysqli_connect($mysql_host, $mysql_username, $mysql_password) or die(mysqli_error($db));
	
	mysqli_select_db($db, $mysql_db_name);
	
	return $db;
}

function sql_log($msg){
	global $db_log_queries;
	if ($db_log_queries) error_log($msg);
}

function restoarray($resdata)
{
	$n=0;
	while ($row=$resdata->fetch_row()){
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
	return $db->errno;
}

function sql_error($db)
{
	return $db->error;
}

function sql_num_rows($result)
{
	return $result->num_rows;
}

function sql_query($db, $query)
{
	sql_log('msqli query: ' . $query);
	return $db->query($query);
}

function sql_fetch_row($result)
{
	return $result->fetch_row();
}

function sql_fetch_assoc($result)
{
	return $result->fetch_assoc();
}

function sql_insert_id($db)
{
	return $db->insert_id;
}

?>
