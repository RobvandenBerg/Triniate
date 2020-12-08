<?php
if(!isset($_GET['id']) or !is_numeric($_GET['id']))
{
	die("Invalid id. <a href='index.php'>Back</a>");
}
$id = $_GET['id'];
if(!file_exists("../rooms/$id/background.png") or !file_exists("../rooms/$id.txt"))
{
	die("Invalid id. <a href='index.php'>Back</a>");
}

$output = '';



/*for($m = 2000; $m > 0; $m--)
{
	if(!file_exists("../rooms/$m.txt"))
	{
		$mapid = $m;
		$newfile = "../rooms/$m.txt";
	}
}

if (!copy("maps/$id.txt", $newfile)) {
    echo "failed to copy maps/$id.txt to $newfile...\n";
}
*/











include('../functions/db_info.php');


include('../include_this.php');


	mysql_pconnect($dbhost,$dbuser,$dbpass);
	mysql_select_db($db);
	$player_id = 6;
	$mg = obtain_mg($player_id);
	setcookie("playerid", "$player_id", time()+$cookie_time, "/");
	setcookie("mg", "$mg", time()+$cookie_time, "/");
	
	$mysql_update_req = mysql_query("UPDATE position set name='Tester',room='$id' where id='$player_id'");

	mysql_close();
	header("location:../redirect.php");
	exit();
?>
