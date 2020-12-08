<?php
include("include_this.php");

light_login();

mysql_pconnect($dbhost,$dbuser,$dbpass) or die('error');
mysql_select_db($db) or die('error');



if((!isset($_GET['trade_id']) or !is_numeric($_GET['trade_id'])) && !isset($_GET['invite']))
{
	mysql_close();
	die("trade id not specified or invalid");
}

if(!isset($_POST['money']) or !is_numeric($_POST['money']))
{
	$offer_money = 0;
}
else
{
	$offer_money = $_POST['money'];
}

$select_time = time() - 60;



// ---------------------
$trade_id = $_GET['trade_id'];

$select_trade_info = mysql_query("SELECT trade_status,inviter,accepter,inviter_offered,accepter_offered,inviter_money,accepter_money from trades where id='$trade_id'") or die(mysql_error());
$select_trade_info_num = mysql_num_rows($select_trade_info);
if($select_trade_info_num != 1)
{
	if(isset($_GET['invite']) && isset($_GET['players_id']))
	{
		$players_id = htmlentities($_GET['players_id']);
		$i = mysql_query("INSERT into trades (trade_status,inviter,accepter,requestedtime) VALUES ('requesting','$player_id','$players_id','".time()."')") or die(mysql_error());
		$j = mysql_query("SELECT id from trades where trade_status='requesting' and inviter='$player_id' and accepter='$players_id' order by id DESC") or die(mysql_error());
		$j_row = mysql_fetch_row($j);
		$return_id = $j_row[0];
		mysql_close();
		exit($return_id);
	}
	else
	{
		mysql_close();
		exit("No trades with that id");
	}
}
$select_trade_info_row = mysql_fetch_row($select_trade_info);
$trade_status = $select_trade_info_row[0];
$inviter = $select_trade_info_row[1];
$accepter = $select_trade_info_row[2];
$inviter_offered = $select_trade_info_row[3];
$accepter_offered = $select_trade_info_row[4];
$inviter_money = $select_trade_info_row[5];
$accepter_money = $select_trade_info_row[6];

if($player_id == $inviter)
{
	$my_role = 'inviter';
	$other_offered = $accepter_offered;
	$other_money = $accepter_money;
	$confirm_money = $inviter_money;
	$others_id = $accepter;
}
else
{
	$my_role = 'accepter';
	$other_offered = $inviter_offered;
	$other_money = $inviter_money;
	$confirm_money = $accepter_money;
	$others_id = $inviter;
}


$select_others_name = mysql_query("SELECT id,name,room,lastmove,money from position where id='$others_id' or id='$player_id'") or die(mysql_error());

for($i = 0; $i < 2; $i++)
{
	$name_array = mysql_fetch_array($select_others_name);
	if($name_array['id'] == $others_id)
	{
		$others_name = $name_array['name'];
		$others_room = $name_array['room'];
		$others_lastmove = $name_array['lastmove'];
		$other_have_money = $name_array['money'];
	}
	else
	{
		$my_name = $name_array['name'];
		$my_room = $name_array['room'];
		$my_lastmove = $name_array['lastmove'];
		$i_have_money = $name_array['money'];
	}
}


if($my_role == 'accepter')
{
	$accepter_have_money = $i_have_money;
	$inviter_have_money = $other_have_money;
}
elseif($my_role == 'inviter')
{
	$accepter_have_money = $other_have_money;
	$inviter_have_money = $i_have_money;
}

$abort = false;
if($my_room != $others_room or ($my_lastmove < (time() - 60)) or ($others_lastmove < (time() - 60)))
{
	$abort = true;
}


if(isset($_GET['abort']) or $abort == true)
{
	mysql_query("update trades set trade_status='aborted' where id='$trade_id'") or die(mysql_error());
	mysql_close();
	exit("aborted");
}
elseif($trade_status == 'aborted')
{
	mysql_close();
	exit("aborted");
}
elseif($trade_status == 'success')
{
	mysql_close();
	exit("success");
}
elseif(isset($_GET['accept']))
{
	mysql_query("update trades set trade_status='in_trade' where id='$trade_id'") or die(mysql_error());
	echo "Done! Trade status was $trade_status, now it's in_trade.";
}
elseif(isset($_GET['accept_trade']))
{
	if($my_role == 'accepter')
	{
		if($trade_status == 'in_trade')
		{
			$new_status = 'accepter_accepted';
		}
		elseif($trade_status == 'inviter_accepted')
		{
			$new_status = 'confirm';
		}
	}
	elseif($my_role == 'inviter')
	{
		if($trade_status == 'in_trade')
		{
			$new_status = 'inviter_accepted';
		}
		elseif($trade_status == 'accepter_accepted')
		{
			$new_status = 'confirm';
		}
	}
	if(isset($new_status))
	{
		$set_new_status = mysql_query("UPDATE trades set trade_status='$new_status' where id='$trade_id'") or die(mysql_error());
		mysql_close();
		exit($new_status);
	}
}
elseif(isset($_GET['confirm']))
{
	if($my_role == 'accepter')
	{
		if($trade_status == 'confirm')
		{
			$new_status = 'accepter_confirmed';
		}
		elseif($trade_status == 'inviter_confirmed')
		{
			$new_status = 'success';
		}
	}
	elseif($my_role == 'inviter')
	{
		if($trade_status == 'confirm')
		{
			$new_status = 'inviter_confirmed';
		}
		elseif($trade_status == 'accepter_confirmed')
		{
			$new_status = 'success';
		}
	}
	if(isset($new_status))
	{
		$set_new_status = mysql_query("UPDATE trades set trade_status='$new_status' where id='$trade_id'") or die(mysql_error());

		if($new_status == 'success')
		{
			$inviter_new_money = $inviter_have_money + $accepter_money - $inviter_money;
			$accepter_new_money = $accepter_have_money + $inviter_money - $accepter_money;


			$acc_query = '';
			$acc_off_split = explode(",",$accepter_offered);
			if(count($acc_off_split > 0) && $accepter_offered != '')
			{
				for($a = 0; $a < count($acc_off_split); $a++)
				{
					$citem = $acc_off_split[$a];
					if($acc_query == '')
					{
						$acc_query .= "id='$citem'";
					}
					elseif($acc_query != '')
					{
						$acc_query .= " or id='$citem'";	
					}
				}
			}
		
			$inv_query = '';
			$inv_off_split = explode(",",$inviter_offered);
			if(count($inv_off_split > 0) && $inviter_offered != '')
			{
				for($a = 0; $a < count($inv_off_split); $a++)
				{
					$citem = $inv_off_split[$a];
					if($inv_query == '')
					{
						$inv_query .= "id='$citem'";
					}
					elseif($inv_query != '')
					{
						$inv_query .= " or id='$citem'";	
					}
				}
			}

			$give_accepter_money = mysql_query("UPDATE position set money='$accepter_new_money' where id='$accepter'") or die(mysql_error());
			$give_inviter_money = mysql_query("UPDATE position set money='$inviter_new_money' where id='$inviter'") or die(mysql_error());
			if($acc_query != '')
			{
				$belongsto_accepter = mysql_query("UPDATE inventories set belongs_to='$inviter' where $acc_query") or die(mysql_error());
$acc_uitgevoerd = 'yes';
			}

			if($inv_query != '')
			{
				$belongsto_inviter = mysql_query("UPDATE inventories set belongs_to='$accepter' where $inv_query") or die(mysql_error());
$inv_uitgevoerd = 'yes';
			}

/*$myFile = "do.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, "Trade:\ninviter_have_money: $inviter_have_money.\ninviter_money: $inviter_money.\naccepter_have_money: $accepter_have_money.\n accepter money: $accepter_money.\n\naccepter_offered: $accepter_offered.\nacc_query: $acc_query\ntotal query: UPDATE inventories set belongs_to='$accepter' where $acc_query (uitgevoerd: $acc_uitgevoerd.)\n\ninviter_offered: $inviter_offered.\ninv_query: $inv_query\nTotal query: UPDATE inventories set belongs_to='$inviter' where $inv_query (uitgevoerd: $inv_uitgevoerd).\n\n\n");
fclose($fh);*/

		}
		mysql_close();
		exit($new_status);
	}
}
elseif(($trade_status == 'in_trade' or $trade_status == 'accepter_accepted' or $trade_status == 'inviter_accepted') && isset($_POST['i_offer']))
{

	echo "$trade_status;split;";

	$i_offer_array = array();
	$offered_array = array();

	$i_offer = htmlentities($_POST['i_offer']);
	$splitoffer = explode(",",$i_offer);

	if($i_offer != "")
	{
		for($b = 0; $b<count($splitoffer); $b++)
		{
			$curritem = $splitoffer[$b];
			if($offered_array[$curritem] != 'a')
			{
				$i_offer_array[count($i_offer_array)] = $curritem;
				$offered_array[$curritem] = 'a';
			}
		}
	}

	$where_string = '';

	foreach($i_offer_array as $oitem)
	{
		$where_string .= " and inv.id!='$oitem'";
		if($where_string2 == '')
		{
			$where_string2 .= "and (inv.id='$oitem'";
		}
		elseif($where_string2 != '')
		{
			$where_string2 .= " or inv.id='$oitem'";
		}
	}

	if($where_string2 != '')
	{
		$where_string2 .= ")";
	}

	$offer_request = mysql_query("UPDATE trades set ".$my_role."_offered='$i_offer',".$my_role."_money='$offer_money' where id='$trade_id'") or die(mysql_error());


	$select_money_request = mysql_query("SELECT money from position where id='$player_id'") or die(mysql_error());
	$select_money_row = mysql_fetch_row($select_money_request);
	$my_money = $select_money_row[0];

	echo "$my_money;split;";

// echo "Money: <span id='money_offered'>0</span><br><div id='i_offer_money' style='overflow-y: auto; overflow-x: hidden; height: 32px; width: 16px; background-color: yellow;' onscroll='document.getElementById(\"money_offered\").innerHTML=(this.scrollTop/40);'><div id='in_offer_money' style='height: $m40; overflow: hidden; background-color: aqua; width: 30px; border: 1px solid black;'>&nbsp;</div></div>";

	$select_inventory_request = mysql_query("SELECT inv.id, it.name, it.id, it.durability, inv.durability from inventories as inv, items as it where it.id=inv.item_id and inv.belongs_to='$player_id' and it.quest_item='no'$where_string") or die(mysql_error());
	$total_items = mysql_num_rows($select_inventory_request);
	for($m = 0; $m < $total_items; $m++)
	{
		$item_row = mysql_fetch_row($select_inventory_request);
		$inventory_id = $item_row[0];
		$item_name = $item_row[1];
		$item_id = $item_row[2];
		$it_durability = $item_row[3];
		$inv_durability = $item_row[4];
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
		//echo "<span oNclick=\"offer_item($inventory_id)\">$inventory_id: <img src='images/items/$item_id.png'> <b>$item_name</b></span><br>\n";
		echo "<span oNclick=\"offer_item($inventory_id,$item_id)\"><div style='position: relative; display: inline-block; width: 15px; height: 15px;'><img src='images/items/$item_id.png'>$display_bar</div> $item_name</span><br>\n";
	}
	if($total_items == 0)
	{
		echo "<span class='trade_inventory_empty'>Your inventory is empty.</span>";
	}



	echo ";split;";
	











	if($where_string2 != '')
	{

		$select_offered_request = mysql_query("SELECT inv.id, it.name, it.id, it.durability, inv.durability from inventories as inv, items as it where it.id=inv.item_id and inv.belongs_to='$player_id' $where_string2") or die(mysql_error());
		$total_offered_items = mysql_num_rows($select_offered_request);
		for($m = 0; $m < $total_offered_items; $m++)
		{
			$item_row = mysql_fetch_row($select_offered_request);
			$inventory_id = $item_row[0];
			$item_name = $item_row[1];
			$item_id = $item_row[2];
			$it_durability = $item_row[3];
			$inv_durability = $item_row[4];
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
			//echo "<span>$inventory_id: <img src='images/items/$item_id.png'> <b>$item_name</b></span><br>\n";
			// oNclick=\"use_item($inventory_id,$item_id)\"
			echo "<span class='trade_item'><div style='position: relative; display: inline-block; width: 15px; height: 15px;'><img src='images/items/$item_id.png'>$display_bar</div> $item_name (<span class='remove_item' oNclick=\"remove_item($inventory_id);\">-</span>)</span><br>\n";
		}
		if($total_offered_items == 0)
		{
			echo "You did not offer.";
		}

	}
	elseif($offer_money == 0)
	{
		echo "<span class='you_offered_nothing'>You offered nothing yet.</span><br>";
	}
	if($offer_money != 0)
	{
		echo "<span class='trade_item'><img src='images/items/3.png'> $offer_money Coins</span><br>\n";
	}

	if($my_role == 'inviter')
	{
		if($trade_status == 'inviter_accepted')
		{
			echo "<span class='trade_accepted'>Ready</span><br>\n";
		}
	}
	elseif($my_role == 'accepter')
	{
		if($trade_status == 'accepter_accepted')
		{
			echo "<span class='trade_accepted'>Ready</span><br>\n";
		}
	}











	echo ";split;";












	$other_where = '';

	if($other_offered != '')
	{
		$splitoffer2 = explode(",",$other_offered);
		for($c = 0; $c<count($splitoffer2); $c++)
		{
			$curritem = $splitoffer2[$c];
			if($c == 0)
			{
				$other_where .= "(inv.id='$curritem'";
			}
			else
			{
				$other_where .= " or inv.id='$curritem'";
			}
		}
	}

	if($other_where != '')
	{
		$other_where .= ')';
		$select_offered_request = mysql_query("SELECT inv.id, it.name, it.id, it.durability, inv.durability from inventories as inv, items as it where it.id=inv.item_id and $other_where") or die(mysql_error());
		$total_offered_items = mysql_num_rows($select_offered_request);
		for($m = 0; $m < $total_offered_items; $m++)
		{
			$item_row = mysql_fetch_row($select_offered_request);
			$inventory_id = $item_row[0];
			$item_name = $item_row[1];
			$item_id = $item_row[2];
			$it_durability = $item_row[3];
			$inv_durability = $item_row[4];
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
			//echo "<span>$inventory_id: <img src='images/items/$item_id.png'> <b>$item_name</b></span><br>\n";
			// oNclick=\"use_item($inventory_id,$item_id)\"
			echo "<span><div style='position: relative; display: inline-block; width: 15px; height: 15px;'><img src='images/items/$item_id.png'>$display_bar</div> $item_name</span><br>\n";
		}
		if($total_offered_items == 0)
		{
			echo "Other person did not offer. Other where is $other_where";
		}
		
	}
	elseif($other_money == 0)
	{
		echo "<span class='other_offered_nothing'>The other person offered nothing yet.</span><br>";
	}
	if($other_money != 0)
	{
		echo "<span class='trade_item'><img src='images/items/3.png'> $other_money Coins</span><br>\n";
	}

	if($my_role == 'inviter')
	{
		if($trade_status == 'accepter_accepted')
		{
			echo "<span class='trade_accepted'>Ready</span><br>\n";
		}
	}
	elseif($my_role == 'accepter')
	{
		if($trade_status == 'inviter_accepted')
		{
			echo "<span class='trade_accepted'>Ready</span><br>\n";
		}
	}







	// echo "The other offered: $other_offered (You offered: $accepter_offered)";
}
elseif(($trade_status == 'confirm' or $trade_status == 'accepter_confirmed' or $trade_status == 'inviter_confirmed'))
{

	echo "$trade_status;split;";

	$i_offer_array = array();
	$offered_array = array();

	if($my_role == 'accepter')
	{
		$i_offer = $accepter_offered;
	}
	elseif($my_role == 'inviter')
	{
		$i_offer = $inviter_offered;
	}
	$splitoffer = explode(",",$i_offer);

	if($i_offer != "")
	{
		for($b = 0; $b<count($splitoffer); $b++)
		{
			$curritem = $splitoffer[$b];
			if($offered_array[$curritem] != 'a')
			{
				$i_offer_array[count($i_offer_array)] = $curritem;
				$offered_array[$curritem] = 'a';
			}
		}
	}

	$where_string = '';

	foreach($i_offer_array as $oitem)
	{
		$where_string .= " and inv.id!='$oitem'";
		if($where_string2 == '')
		{
			$where_string2 .= "and (inv.id='$oitem'";
		}
		elseif($where_string2 != '')
		{
			$where_string2 .= " or inv.id='$oitem'";
		}
	}

	if($where_string2 != '')
	{
		$where_string2 .= ")";
	}


// echo "Money: <span id='money_offered'>0</span><br><div id='i_offer_money' style='overflow-y: auto; overflow-x: hidden; height: 32px; width: 16px; background-color: yellow;' onscroll='document.getElementById(\"money_offered\").innerHTML=(this.scrollTop/40);'><div id='in_offer_money' style='height: $m40; overflow: hidden; background-color: aqua; width: 30px; border: 1px solid black;'>&nbsp;</div></div>";
	











	if($where_string2 != '')
	{

		$select_offered_request = mysql_query("SELECT inv.id, it.name, it.id, it.durability, inv.durability from inventories as inv, items as it where it.id=inv.item_id and inv.belongs_to='$player_id' $where_string2") or die(mysql_error());
		$total_offered_items = mysql_num_rows($select_offered_request);
		for($m = 0; $m < $total_offered_items; $m++)
		{
			$item_row = mysql_fetch_row($select_offered_request);
			$inventory_id = $item_row[0];
			$item_name = $item_row[1];
			$item_id = $item_row[2];
			$it_durability = $item_row[3];
			$inv_durability = $item_row[4];
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
			//echo "<span>$inventory_id: <img src='images/items/$item_id.png'> <b>$item_name</b></span><br>\n";
			// oNclick=\"use_item($inventory_id,$item_id)\"
			echo "<span class='trade_item'><div style='position: relative; display: inline-block; width: 15px; height: 15px;'><img src='images/items/$item_id.png'>$display_bar</div> $item_name</span><br>\n";
		}
		if($total_offered_items == 0)
		{
			echo "You did not offer.";
		}

	}
	elseif($confirm_money == 0)
	{
		echo "<span class='you_offered_nothing'>You offered nothing.</span><br>";
	}
	if($confirm_money != 0)
	{
		echo "<span class='trade_item'><img src='images/items/3.png'> $confirm_money Coins</span><br>\n";
	}

	if($my_role == 'inviter')
	{
		if($trade_status == 'inviter_confirmed')
		{
			echo "<span class='trade_accepted'>Ready</span><br>\n";
		}
	}
	elseif($my_role == 'accepter')
	{
		if($trade_status == 'accepter_confirmed')
		{
			echo "<span class='trade_accepted'>Ready</span><br>\n";
		}
	}











	echo ";split;";












	$other_where = '';

	if($other_offered != '')
	{
		$splitoffer2 = explode(",",$other_offered);
		for($c = 0; $c<count($splitoffer2); $c++)
		{
			$curritem = $splitoffer2[$c];
			if($c == 0)
			{
				$other_where .= "(inv.id='$curritem'";
			}
			else
			{
				$other_where .= " or inv.id='$curritem'";
			}
		}
	}

	if($other_where != '')
	{
		$other_where .= ')';
		$select_offered_request = mysql_query("SELECT inv.id, it.name, it.id, it.durability, inv.durability from inventories as inv, items as it where it.id=inv.item_id and $other_where") or die(mysql_error());
		$total_offered_items = mysql_num_rows($select_offered_request);
		for($m = 0; $m < $total_offered_items; $m++)
		{
			$item_row = mysql_fetch_row($select_offered_request);
			$inventory_id = $item_row[0];
			$item_name = $item_row[1];
			$item_id = $item_row[2];
			$it_durability = $item_row[3];
			$inv_durability = $item_row[4];
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
			//echo "<span>$inventory_id: <img src='images/items/$item_id.png'> <b>$item_name</b></span><br>\n";
			// oNclick=\"use_item($inventory_id,$item_id)\"
			echo "<span><div style='position: relative; display: inline-block; width: 15px; height: 15px;'><img src='images/items/$item_id.png'>$display_bar</div> $item_name</span><br>\n";
		}
		if($total_offered_items == 0)
		{
			echo "Other person did not offer. Other where is $other_where";
		}
		
	}
	elseif($other_money == 0)
	{
		echo "<span class='other_offered_nothing'>The other person offered nothing.</span><br>";
	}
	if($other_money != 0)
	{
		echo "<span class='trade_item'><img src='images/items/3.png'> $other_money Coins</span><br>\n";
	}

	if($my_role == 'inviter')
	{
		if($trade_status == 'accepter_confirmed')
		{
			echo "<span class='trade_accepted'>Ready</span><br>\n";
		}
	}
	elseif($my_role == 'accepter')
	{
		if($trade_status == 'inviter_confirmed')
		{
			echo "<span class='trade_accepted'>Ready</span><br>\n";
		}
	}







	// echo "The other offered: $other_offered (You offered: $accepter_offered)";
}
elseif($trade_status == 'requesting' && isset($_GET['await']))
{
echo "inviting (id $trade_id)";
}
elseif($trade_status == 'in_trade' && isset($_GET['await']))
{
echo "accept";
}

mysql_close();

?>