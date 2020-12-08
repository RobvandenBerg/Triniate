<?php
include('../include_this.php');

light_login('../please_login.php');

$sell_item = $_POST['sell_item'];


mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());
$select_shop_request = mysql_query('SELECT id, sells, retailer from pages where in_room=\''.$room.'\' and type=\'shop\'') or die(mysql_error());
$select_shop_array = mysql_fetch_array($select_shop_request);
$retailer = $select_shop_array['retailer'];

$item_being_sold = true;
$select_sells_request = mysql_query('SELECT it.price,it.quest_item from items as it, inventories as inv where inv.id='.$sell_item.' and it.id=inv.item_id LIMIT 0,1') or die(mysql_error());
$total_sell_items = mysql_num_rows($select_sells_request);
if($total_sell_items == 1)
{
	$item_array = mysql_fetch_array($select_sells_request);
	$item_price = ceil($item_array['price'] / 2);
	$quest_item = $item_array['quest_item'];
}

if(mysql_num_rows($select_shop_request) != 1)
{
	// The shop you are visiting does not exist
	exit('error,'.$money.','.$total_items);
}

if(!is_numeric($sell_item))
{
	exit('error');
}

if($total_sell_items != 1)
{
	exit('error;split;'.$total_sell_items);
}

if($quest_item != 'no')
{
	exit('error');
}

// Everything's okay, the item can be sold

$sell_items_request = mysql_query('DELETE from inventories where id='.$sell_item) or die(mysql_error());
$get_paid_request = mysql_query('UPDATE position set money=('.$item_price.' + money) where id='.$player_id) or die(mysql_error());


echo 'success';



echo ';split;';

$sellable = [];
$select_sells_request = mysql_query('SELECT inv.id, it.id, it.name, it.price, it.quest_item, it.durability, inv.durability from items as it, inventories as inv where inv.belongs_to='.$player_id.' and it.id=inv.item_id order by it.id') or die(mysql_error());
$total_sell_items = mysql_num_rows($select_sells_request);
for($a = 0; $a < $total_sell_items; $a++)
{
	array_push($sellable, mysql_fetch_array($select_sells_request));
	// echo 'Sells '.$selling[count($selling) - 1][0].': '.$selling[count($selling) - 1][1].'<br>';
}

$item_class = 'selected';
$idplus = 1;
foreach($sellable as $sellarray)
{
	$id = $sellarray[0];
	$item_id = $sellarray[1];
	$item_name = $sellarray[2];
	$item_price = ceil($sellarray[3] / 2);
	$quest_item = $sellarray[4];
	$it_durability = $sellarray[5];
	$inv_durability = $sellarray[6];
	
	$display_bar = '';
	$used_tool = 'false';
	if($it_durability != 0 && $inv_durability != 0)
	{
		$percentage = 100 - floor($inv_durability/$it_durability * 100);
		$bar_color = 'lime';
		if($percentage <= 35)
		{
			$bar_color = 'orange';
		}
		if($percentage <= 15)
		{
			$bar_color = 'red';
		}
		$display_bar = '<div class="durability_bar"><div style="width: '.$percentage.'%; background-color: '.$bar_color.';" class="in_durability_bar"></div></div>';
		$used_tool = 'true';
	}
	
	
	if($quest_item == 'no')
	{
		$quest_item = 'false';
	}
	else
	{
		$quest_item = 'true';
	}
	$item_name = str_replace('\'','&apos;',$item_name);
	echo '<div onClick=\'select_sellable_item('.$idplus.');\' id=\'sell_item_'.$idplus.'\'class=\'sell_item_'.$item_class.'\'><div class=\'sell_item_image\'><img src=\'../images/items/'.$item_id.'.png\'>'.$display_bar.'</div> '.$item_name.'<div class=\'sellable_price\'><img src="../images/items/3.png"> '.$item_price.'</div></div>';
	$item_class = 'normal';
	$pass_to_javascript .= 'sellable['.$idplus.'] = new Array('.$id.','.$item_id.',\''.$item_name.'\','.$item_price.','.$quest_item.','.$used_tool.'); total_items++; ';
	$idplus++;
}
echo ';split;'.$pass_to_javascript;



mysql_close();
?>