<?php
include("include_this.php");

light_login();

mysql_pconnect($dbhost,$dbuser,$dbpass) or die('error');
mysql_select_db($db) or die('error');


$character_id = $_GET['character_id'];
if(!is_numeric($character_id))
{
mysql_close();
die('');
}

$select_time = time() - 60;
$room = get_room();



// ---------------------
$select_my_stats = mysql_query("SELECT attack,defense,health,max_hp,exp from position where id='$character_id'") or die('error: ' . mysql_error());

$sms = mysql_fetch_array($select_my_stats);
$my_attack = $sms['attack'];
$my_defense = $sms['defense'];
$my_health = $sms['health'];
$my_max_hp = $sms['max_hp'];
$real_exp = $sms['exp'];
/*$level_handle = get_level($real_exp);
$my_level = $level_handle[0];
$exp = $level_handle[1];
$nextexp = ($my_level + 1) * 100;
$nextexp = round(($my_level) * 50 * (1+$my_level/10));*/
$my_level = exp_to_level($real_exp);
$nextexp = level_to_exp($my_level + 1) - level_to_exp($my_level);
$exp = $real_exp - level_to_exp($my_level);

echo "$my_level,$my_attack,$my_defense,$my_health,$my_max_hp,".$exp."/$nextexp";

mysql_close();


?>