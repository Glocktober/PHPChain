<?php
include("inc/sessions.php");



if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 0;
} else {
  $_SESSION['count']++;
}

echo $_SESSION['count'] . "<br>";


echo "<br>\nSESSION\n<br>";
foreach ($_SESSION as $x => $v){
  echo "$x => $v <br>";
}

echo "<br>\nSESSION config\n<br>";
$session =  session_get_cookie_params();

foreach ($session as $x => $v){
	echo "$x => $v <br>";
}
echo "<br>\nSERVER\n<br>";
foreach ($_SERVER as $x => $v){
	echo "$x => $v <br>";
}
?>
