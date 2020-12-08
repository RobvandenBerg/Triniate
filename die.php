<?php
include("include_this.php");

light_login();

mysql_pconnect($dbhost,$dbuser,$dbpass) or die('error');
mysql_select_db($db) or die('error');


$select_time = time() - 60;
$room = get_room();



// ---------------------
$select_my_stats = mysql_query("SELECT level,attack,defense,health,max_hp,exp from position where  id='$player_id'") or die('error: ' . mysql_error());

$sms = mysql_fetch_row($select_my_stats);
$my_level = $sms[0];
$my_attack = $sms[1];
$my_defense = $sms[2];
$my_health = $sms[3];
$my_max_hp = $sms[4];
$my_exp = $sms[5];

if($my_health != 0)
{
mysql_close();
die("You're not dead... Your health is $my_health");
}

$update_request = mysql_query("UPDATE position set pos='80,300',room='3',sprite='sprite_right',health='$my_max_hp',lastmove='".time()."',money='0' where id='$player_id'") or die(mysql_error());

$delete_inventory_request = mysql_query("DELETE inventories from inventories, items where inventories.belongs_to='$player_id' and items.id=inventories.item_id and items.quest_item='no'") or die(mysql_error());

include("redirect.php");

mysql_close();

?>