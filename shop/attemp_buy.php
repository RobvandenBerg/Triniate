<?php
include('../include_this.php');

light_login('../please_login.php');

$howmany = $_POST['howmany'];
$buy_item = $_POST['buy_item'];
$item_being_sold = false;


mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());
$select_shop_request = mysql_query('SELECT id, sells, retailer from pages where in_room=\''.$room.'\' and type=\'shop\'') or die(mysql_error());
$select_shop_array = mysql_fetch_array($select_shop_request);
$sellstring = $select_shop_array['sells'];
$retailer = $select_shop_array['retailer'];
$sellsplit = explode(',',$sellstring);
$sellcount = count($sellsplit);
for($a = 0; $a < $sellcount; $a++)
{
	if($sellsplit[$a] == $buy_item)
	{
		$item_being_sold = true;
		$select_sells_request = mysql_query('SELECT price from items where id='.$buy_item.' and quest_item=\'no\' order by id LIMIT 0,1') or die(mysql_error());
		$total_sell_items = mysql_num_rows($select_sells_request);
		if($total_sell_items == 1)
		{
			$item_array = mysql_fetch_array($select_sells_request);
			$item_price = $item_array['price'];
		}
	}
}

$select_money_request = mysql_query('SELECT position.money, count(*) from position, inventories where position.id='.$player_id .' and inventories.belongs_to=position.id LIMIT 0,1') or die(mysql_error());
$select_money_array = mysql_fetch_array($select_money_request);
$money = $select_money_array['money'];
$total_items = $select_money_array[1];

if(mysql_num_rows($select_shop_request) != 1)
{
	// The shop you are visiting does not exist
	exit('error,'.$money.','.$total_items);
}

if(!is_numeric($howmany) or !is_numeric($buy_item))
{
	exit('error,'.$money.','.$total_items);
}

if($item_being_sold == false)
{
	exit('error,'.$money.','.$total_items);
}

if($item_price * $howmany > $money)
{
	exit('error,'.$money.','.$total_items.',money');
}

if($total_items + $howmany > 30)
{
	exit('error,'.$money.','.$total_items.',space');
}

if($howmany < 1)
{
	exit('error,'.$money.','.$total_items);
}

// Everything's okay, the item can be bought

$insert_values = '';
for($a = 0; $a < $howmany; $a++)
{
	if($a == 0)
	{
		$insert_values .= 'VALUES';
	}
	else
	{
		$insert_values .= ',';
	}
	$insert_values .= '('.$player_id.','.$buy_item.')';
}

$money = $money - $item_price * $howmany;
$total_items = $total_items + $howmany;
$pay_request = mysql_query('UPDATE position set money='.$money.' where id='.$player_id) or die(mysql_error());
$buy_items_request = mysql_query('INSERT into inventories (belongs_to,item_id) '.$insert_values) or die(mysql_error());
mysql_close();
echo 'success,'.$money.','.$total_items;
?>