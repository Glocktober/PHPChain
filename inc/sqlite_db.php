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

function sql_prepare($db, $query)
{
	return $db->prepare($query);
}

function sql_stmt_bind_value($stmt, $param, $value, $type)
{
	return $stmt->bindValue($param, $value, $type);
}

function sql_stmt_bind_param($stmt, $param, &$var, $type=0)
{
	if ($type) return $stmt->bindParam($param, $var, $type);
	else return $stmt->bindParam($param,$var);
}

function sql_stmt_multi_bind_param($stmt, &...$vars)
{
	$param = 1;
	$res = True;
	foreach($vars as &$var){
		$res = $res AND $stmt->bindParam($param, $var);
		$param +=1;
	}
}

function sql_stmt_multi_bind_value($stmt, ...$vars)
{
	$param = 1;
	$res = True;
	foreach($vars as $var){
		$res = $res AND $stmt->bindValue($param, $var);
		$param +=1;
	}
}

function sql_mysql_multi_bind_param($stmt, $types, &...$vars)
{
	$type_lists = array(
		'i' => SQLITE3_INTEGER ,
		'd' => SQLITE3_FLOAT,
		's' => SQLITE3_TEXT,
		'b' => SQLITE3_BLOB,
	);

	$param = 0;
	$res = TRUE;
	foreach ($vars as &$var){
		$type = $types[$param];
		$param += 1;
		$res = $res AND $stmt->bindParam($param, $var, $type_lists[$type]);
		if (!$res) break;
	}
	return $res;
}

function sql_stmt_execute($stmt)
{
	return $stmt->execute();
}

function sql_stmt_reset($stmt){
	return $stmt->reset();
}
function sql_stmt_clear($stmt)
{
	return $stmt->clear();
}

function sql_stmt_close($stmt)
{
	return $stmt->close();
}

?>
