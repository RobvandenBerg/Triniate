<?php
include("include_this.php");

light_login();

mysql_pconnect($dbhost,$dbuser,$dbpass) or die('error');
mysql_select_db($db) or die('error');


if(!isset($_GET['players_id']) or !is_numeric($_GET['players_id']))
{
	mysql_close();
	die("players id not specified or invalid");
}

$select_time = time() - 60;
$room = get_room();



// ---------------------
$players_id = $_GET['players_id'];
$select_my_stats = mysql_query("SELECT level,attack,defense,health,max_hp,exp,name from position where  id='$players_id'") or die('error: ' . mysql_error());

$sms = mysql_fetch_row($select_my_stats);
$my_level = $sms[0];
$my_attack = $sms[1];
$my_defense = $sms[2];
$my_health = $sms[3];
$my_max_hp = $sms[4];
$real_exp = $sms[5];
$my_name = $sms[6];
$level_handle = get_level($real_exp);
$my_level = $level_handle[0];
$exp = $level_handle[1];


mysql_close();

echo "$my_name,$my_level,$my_attack,$my_defense";

?>