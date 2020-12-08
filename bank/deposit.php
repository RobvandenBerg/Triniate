<?php

$deposit_item = round($_POST['deposit_item']);
$deposit_amount = round($_POST['amount']);

if(!is_numeric($deposit_item) or !is_numeric($deposit_amount))
{
	exit('error;split;1');
}

include_once('../include_this.php');
include_once('bankfunc.php');
light_login();



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

$select_money_request = mysql_query('SELECT position.money, position.money_bank, count(*) from position, inventories where position.id='.$player_id .' and inventories.belongs_to=position.id LIMIT 0,1') or die(mysql_error());
$select_money_array = mysql_fetch_array($select_money_request);
$money_inventory = $select_money_array['money'];
$money_bank = $select_money_array['money_bank'];
$total_items_in_inventory = $select_money_array[2];

$select_item_info_request = mysql_query('SELECT inv.belongs_to,inv.item_id, inv.durability from inventories as inv where inv.id='.$deposit_item.' LIMIT 0,1') or die(mysql_error());

if(mysql_num_rows($select_item_info_request) != 1)
{
	exit('error;split;2: ' . 'SELECT inv.belongs_to,inv.item_id, inv.durability from inventories as inv where inv.id='.$deposit_item.' LIMIT 0,1');
}

$select_item_info_array = mysql_fetch_array($select_item_info_request);
$belongs_to = $select_item_info_array['belongs_to'];
$item_id = $select_item_info_array['item_id'];
$item_durability = $select_item_info_array['durability'];

if($belongs_to != $player_id)
{
	exit('error;split;3');
}

// everything seems to be okay, so deposit the item

// First check if we can stack the item

$check_stackable_request = mysql_query('SELECT id from bank where belongs_to='.$player_id.' and item_id='.$item_id.' and durability='.$item_durability.' LIMIT 0,1') or die(mysql_error());

$check_stackable_num = mysql_num_rows($check_stackable_request);


// First delete the item from the player's inventory
$delete_from_inventory_request = mysql_query('DELETE from inventories where id='.$deposit_item) or die(mysql_error());

if($check_stackable_num == 0)
{
	// The item is not stackable in the bank
	$update_bank_request = mysql_query('INSERT into bank (belongs_to,item_id,durability,quantity) VALUES('.$player_id.','.$item_id.','.$item_durability.',1)') or die(mysql_error());
}
else
{
	// The item is stackable in the bank
	$check_stackable_array = mysql_fetch_array($check_stackable_request);
	$stackable_id = $check_stackable_array['id'];
	$update_bank_request = mysql_query('UPDATE bank set quantity=(1+quantity) where id='.$stackable_id) or die(mysql_error());
}

echo 'success;split;';




$total_items_in_inventory = $total_items_in_inventory - 1;

$returned_array = get_inventory($player_id);
$returned = $returned_array[0];
$js_returned = $returned_array[1];

mysql_close();

echo $returned . ';split;' . $js_returned . ' money_bank = '.$money_bank . '; total_items_in_inventory = ' . $total_items_in_inventory;
?>