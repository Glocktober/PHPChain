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

function sql_close($db)
{
	return $db->close();
}

function sql_errno($db)
{
	return $db->errno();
}

function sql_error($db)
{
	return $db->error();
}

function sql_num_rows($result)
{
	return $result->num_rows();
}

function sql_query($db, $query)
{
	error_log('msqli query: ' . $query);
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
	return $db->insert_id();
}

// function sql_prepare($db, $query)
// {
// 	return $db->prepare($query);
// }

// function sql_stmt_bind_param($stmt, $param, &$var, $type)
// {
// 	return $stmt->bindParam($param, $var, $type);
// }

// function sql_stmt_multi_bind_param($stmt, $types, &...$vars)
// {
// 	$stmt->bind_param($types, ...$vars);
// }

// function sql_stmt_execute($stmt)
// {
// 	return $db->execute();
// }

// function sql_stmt_clear($stmt)
// {
// 	return $stmt->clear();
// }

// function sql_stmt_close($stmt)
// {
// 	return $stmt->close();
// }

?>
