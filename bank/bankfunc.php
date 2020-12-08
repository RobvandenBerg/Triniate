<?php

function get_bank($player_id = 2)
{
	global $dbhost,$dbuser,$dbpass;
	$columns = 15;
	$select_bank_request = mysql_query('SELECT b.id, b.item_id, b.quantity, it.durability, b.durability, it.name from bank as b, items as it where b.belongs_to='.$player_id.' and it.id=b.item_id order by b.item_id limit 0,100') or die(mysql_error());
	$total_items_in_bank = mysql_num_rows($select_bank_request);
	$returner = '<table border=\'0\' style=\'padding: 0px; margin: 0px;\' cellpadding=\'0\' cellspacing=\'0\'>';
	$js_returner = 'selected_bank_item = 1; total_items_in_bank = '.$total_items_in_bank.'; bank_items = new Array(); ';
	for($a = 1; $a <= $total_items_in_bank; $a++)
	{
		$select_bank_array = mysql_fetch_array($select_bank_request);
		$id = $select_bank_array['id'];
		$item_id = $select_bank_array['item_id'];
		$quantity = $select_bank_array['quantity'];
		$it_durability = $select_bank_array[3];
		$inv_durability = $select_bank_array[4];
		$item_name = $select_bank_array['name'];
		$item_name = str_replace('\'','&apos;',$item_name);
		$percentage = 0;
		$display_bar = '';
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
		}
		
		if(($a - 1) % $columns == 0)
		{
			if($a != 0)
			{
				$returner .= '</tr>';
			}
			$returner .= '<tr>';
		}
		
		$imgclass = 'item_normal';
		if($a == 1)
		{
			$imgclass = 'item_selected';
		}
		
		$returner .= '<td class=\'td\' onClick="bank_navigate_to('.$a.');"><div style="position: relative; width: 20px; height: 20px;"><img class=\''.$imgclass.'\' id=\'bank_item_'.$a.'\' src="../images/items/'.$item_id.'.png">'.$display_bar.'<div class="bank_howmany">'.$quantity.'</div></div></td>';
		
		$js_returner .= 'bank_items['.$a.'] = new Array('.$id.','.$item_id.',\''.$item_name.'\','.$quantity.','.$percentage.');';
		
		if($a == $total_items_in_bank)
		{
			if($a % $columns != 0)
			{
				$b = $a;
				while($b % $columns != 0)
				{
					$returner .= '<td class=\'td\'> </td>';
					$b++;
				}
				$returner .= '</tr>';
			}
		}
	}
	
	$returner .= '<tr><td colspan="2"><div style="width: 21px; height: 40px; border: 1px solid purple; position: relative;"><div id="transfer_money_bank_up" class="arrow_up" style="border: 1px solid gray; position: absolute; top: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_up.png);" onClick="transfer_money_bank_up(1);"></div><div id="transfer_money_bank_down" class="arrow_down" style="border: 1px solid gray; border-top: none; position: absolute; bottom: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_down.png);" onClick="transfer_money_bank_down(1);"></div></div></td><td colspan="13"><span id="transfer_money_bank">0</span> <img src="../images/items/3.png"> <div class="transfer_money_button" onClick="transfer_money_bank();">Withdraw coins</div></td></tr><tr><td colspan="15"><div style="display: none; text-align: center;" id="transfer_loading_bank">Transferring... <img src="../images/loading.gif"></div></td></tr></table>';
	return(array($returner,$js_returner));
}








function get_inventory($player_id = 2)
{
	global $dbhost,$dbuser,$dbpass;
	$columns = 15;
	$select_inventory_request = mysql_query('SELECT inv.id, inv.item_id, it.durability, inv.durability, it.name from inventories as inv, items as it where inv.belongs_to='.$player_id.' and it.id=inv.item_id order by inv.item_id limit 0,30') or die(mysql_error());
	$total_items_in_inventory = mysql_num_rows($select_inventory_request);
	$returner = '<table border=\'0\' style=\'padding: 0px; margin: 0px;\' cellpadding=\'0\' cellspacing=\'0\'>';
	$js_returner = 'selected_inventory_item = 1; total_items_in_inventory = '.$total_items_in_inventory.'; inventory_items = new Array(); ';
	for($a = 1; $a <= $total_items_in_inventory; $a++)
	{
		$select_inventory_array = mysql_fetch_array($select_inventory_request);
		$id = $select_inventory_array['id'];
		$item_id = $select_inventory_array['item_id'];
		$it_durability = $select_inventory_array[2];
		$inv_durability = $select_inventory_array[3];
		$item_name = $select_inventory_array['name'];
		$item_name = str_replace('\'','&apos;',$item_name);
		
		$percentage = 0;
		
		$display_bar = '';
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
		}
		
		if(($a - 1) % $columns == 0)
		{
			if($a != 0)
			{
				$returner .= '</tr>';
			}
			$returner .= '<tr>';
		}
		
		$imgclass = 'item_normal';
		if($a == 1)
		{
			$imgclass = 'item_selected';
		}
		
		$returner .= '<td class=\'td\' onClick="inventory_navigate_to('.$a.');"><div style="position: relative; width: 20px; height: 20px;"><img class=\''.$imgclass.'\' id=\'inventory_item_'.$a.'\' src="../images/items/'.$item_id.'.png">'.$display_bar.'</div></td>';
		
		$js_returner .= 'inventory_items['.$a.'] = new Array('.$id.','.$item_id.',\''.$item_name.'\','.$percentage.');';
		
		if($a == $total_items_in_inventory)
		{
			if($a % $columns != 0)
			{
				$b = $a;
				while($b % $columns != 0)
				{
					$returner .= '<td class=\'td\'> </td>';
					$b++;
				}
				$returner .= '</tr>';
			}
		}
	}
	
	$returner .= '<tr><td colspan="2"><div style="width: 21px; height: 40px; border: 1px solid purple; position: relative;"><div id="transfer_money_inventory_up" class="arrow_up" style="border: 1px solid gray; position: absolute; top: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_up.png);" onClick="transfer_money_inventory_up(1);"></div><div id="transfer_money_inventory_down" class="arrow_down" style="border: 1px solid gray; border-top: none; position: absolute; bottom: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_down.png);" onClick="transfer_money_inventory_down(1);"></div></div></td><td colspan="13"><span id="transfer_money_inventory">0</span> <img src="../images/items/3.png"> <div class="transfer_money_button" onClick="transfer_money_inventory();">Deposit coins</div></td></tr><tr><td colspan="15"><div style="text-align: center; display: none;" id="transfer_loading_inventory">Transferring... <img src="../images/loading.gif"></div></td></tr></table>';

	return(array($returner,$js_returner));
}
?>