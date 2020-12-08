<?php
include_once('../include_this.php');
include_once('bankfunc.php');
light_login('../please_login.php');

mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());

$check_bank_existance_request = mysql_query('SELECT id from pages where in_room=\''.$room.'\' and type=\'bank\'') or die(mysql_error());
if(mysql_num_rows($check_bank_existance_request) != 1)
{
	// The bank you are visiting does not exist
	mysql_close();
	header('location: ../redirect.php');
	exit();
}

$returned_array = get_bank($player_id);
$returned = $returned_array[0];
$js_returned = $returned_array[1];

$select_money_request = mysql_query('SELECT position.money, position.money_bank, count(*) from position, inventories where position.id='.$player_id .' and inventories.belongs_to=position.id LIMIT 0,1') or die(mysql_error());
$select_money_array = mysql_fetch_array($select_money_request);
$money_inventory = $select_money_array['money'];
$money_bank = $select_money_array['money_bank'];
$total_items_in_inventory = $select_money_array[2];
mysql_close();

echo $returned . ';split;' . $js_returned . ' money_bank = '.$money_bank . ';';
?>