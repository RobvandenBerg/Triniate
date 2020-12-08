<?php
include("include_this.php");

light_login();


mysql_pconnect($dbhost,$dbuser,$dbpass) or die('error');
mysql_select_db($db) or die('error');

$select_money_request = mysql_query("SELECT money from position where id='$player_id'") or die(mysql_error());
$select_money_row = mysql_fetch_row($select_money_request);
$money = $select_money_row[0];

echo 'You have '.$money.' coins <img src="images/items/3.png"><br><div class="inventorybox" id="inventorybox">';

$item_class = 'selected';
$idplus = 1;
$pass_to_javascript = 'in_inventory = new Array(); total_items = 0;';

$select_inventory_request = mysql_query("SELECT inv.id, it.name, it.id, inv.durability, it.durability from inventories as inv, items as it where it.id=inv.item_id and inv.belongs_to='$player_id' order by inv.item_id") or die(mysql_error());
$total_items = mysql_num_rows($select_inventory_request);
for($m = 0; $m < $total_items; $m++)
{
	$item_array = mysql_fetch_array($select_inventory_request);
	$inventory_id = $item_array[0];
	$item_name = $item_array[1];
	$item_id = $item_array[2];
	$inv_durability = $item_array[3];
	$it_durability = $item_array[4];
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
	$item_name = str_replace('\'','&apos;',$item_name);
	//echo "<span oNclick=\"use_item($inventory_id,$item_id)\">$inventory_id: <img src='images/items/$item_id.png'> <b>$item_name</b></span><br>\n";
	// echo "<span oNclick=\"use_item($inventory_id,$item_id)\"><img src='images/items/$item_id.png'> <b>$item_name</b></span> (<span oNclick=\"set_hotkey($inventory_id,$item_id);\">Make hotkey</span>)<br>\n";

	echo '<div onClick=\'select_inventory_item('.$idplus.');\' id=\'item_bar_'.$idplus.'\'class=\'item_bar_'.$item_class.'\'><div style="display: inline-block;" class="item_image"><img src=\'images/items/'.$item_id.'.png\'>'.$display_bar.'</div> '.$item_name.'</div>';
	$item_class = 'normal';
	$idplus++;
	$pass_to_javascript .= 'in_inventory['.($m+1).'] = new Array('.$inventory_id.','.$item_id.',\''.$item_name.'\'); total_items++; ';
}
if($total_items == 0)
{
	echo 'Your inventory is empty.';
}
mysql_close();
?>
</div><div class='sbar'>
<div id='inventory_arrow_up' class='arrow_up' style='background-color: #F0F0F0; background-image: url(images/icons/arrow_up.png);' onClick='inventory_up(1);'></div>

<div id='inventory_arrow_down' class='arrow_down' style='background-color: #F0F0F0; background-image: url(images/icons/arrow_down.png);' onClick='inventory_down(1);'></div>
</div><br>
<div style='background-color: #CDCDCD;' class='inv_button' id='use_item_button' onClick='make_blink(this.id,"#CDCDCD","aqua"); select_inventory_item(selected_item);'>Use</div>
<div style='background-color: #CDCDCD;' class='inv_button' id='set_as_hotkey_button' onClick='make_blink(this.id,"#CDCDCD","aqua"); set_hotkey(in_inventory[selected_item][0],in_inventory[selected_item][1])'>Make hotkey</div>
<div style='background-color: #CDCDCD;' class='inv_button' id='throw_away_button' onClick='make_blink(this.id,"#CDCDCD","aqua"); alert("Not in use yet");'>Throw away</div>
;split;<?php
echo $pass_to_javascript;
?>