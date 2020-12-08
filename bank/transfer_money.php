<?php

$transfer_to = $_POST['to'];
$transfer_amount = round($_POST['amount']);

if(($transfer_to != 'bank' && $transfer_to != 'inventory') or !is_numeric($transfer_amount))
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

$select_money_request = mysql_query('SELECT position.money, position.money_bank from position where position.id='.$player_id .' LIMIT 0,1') or die(mysql_error());
$select_money_array = mysql_fetch_array($select_money_request);
$money_inventory = $select_money_array['money'];
$money_bank = $select_money_array['money_bank'];

if($transfer_to == 'bank')
{
	// transfer from inventory to bank
	$new_money_inventory = $money_inventory - $transfer_amount;
	$new_money_bank = $money_bank + $transfer_amount;
	if($new_money_inventory < 0)
	{
		mysql_close();
		exit('error;split;2');
	}
}
else
{
	// transfer from bank to inventory
	$new_money_inventory = $money_inventory + $transfer_amount;
	$new_money_bank = $money_bank - $transfer_amount;
	if($new_money_bank < 0)
	{
		mysql_close();
		exit('error;split;3');
	}
}

$update_money_request = mysql_query('UPDATE position set money='.$new_money_inventory.', money_bank='.$new_money_bank.' where id='.$player_id) or die(mysql_error());

echo 'success;split;'.$new_money_inventory.';split;'.$new_money_bank;
?>