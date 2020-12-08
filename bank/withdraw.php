<?php

$withdraw_item = round($_POST['withdraw_item']);
$withdraw_amount = round($_POST['amount']);

if(!is_numeric($withdraw_item) or !is_numeric($withdraw_amount))
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

$select_item_info_request = mysql_query('SELECT b.belongs_to,b.item_id, b.quantity, b.durability from bank as b where b.id='.$withdraw_item.' LIMIT 0,1') or die(mysql_error());

if(mysql_num_rows($select_item_info_request) != 1)
{
	exit('error;split;2: ' . 'SELECT b.belongs_to,b.item_id, b.quantity, b.durability from bank as b where b.id='.$withdraw_item.' LIMIT 0,1');
}

$select_item_info_array = mysql_fetch_array($select_item_info_request);
$belongs_to = $select_item_info_array['belongs_to'];
$item_id = $select_item_info_array['item_id'];
$item_quantity = $select_item_info_array['quantity'];
$item_durability = $select_item_info_array['durability'];

if($belongs_to != $player_id)
{
	exit('error;split;3');
}
if($withdraw_amount > $item_quantity or $withdraw_amount < 1)
{
	exit('error;split;4');
}

if($withdraw_amount + $total_item_in_inventory > 30)
{
	exit('error;split;5');
}

// everything seems to be okay, so withdraw the item

$withdraw_string = 'VALUES ('.$player_id.','.$item_id.','.$item_durability.')';
for($a = 1; $a < $withdraw_amount; $a++)
{
	$withdraw_string .= ', ('.$player_id.','.$item_id.','.$item_durability.')';
}

$new_bank_amount = floor($item_quantity - $withdraw_amount);
if($new_bank_amount < 1)
{
	$take_from_bank_request = mysql_query('DELETE from bank where id='.$withdraw_item) or die(mysql_error());
}
else
{
	$take_from_bank_request = mysql_query('UPDATE bank set quantity='.$new_bank_amount.' where id='.$withdraw_item) or die(mysql_error());
}

$withdraw_request = mysql_query('INSERT into inventories (belongs_to,item_id,durability) '.$withdraw_string) or die(mysql_error());

echo 'success;split;';




$total_items_in_inventory = round($total_items_in_inventory + $withdraw_amount);

$returned_array = get_bank($player_id);
$returned = $returned_array[0];
$js_returned = $returned_array[1];

mysql_close();

echo $returned . ';split;' . $js_returned . ' money_bank = '.$money_bank . '; total_items_in_inventory = ' . $total_items_in_inventory;
?>