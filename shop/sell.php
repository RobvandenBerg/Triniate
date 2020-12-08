<?php
include('../include_this.php');

light_login('../please_login.php');

mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());
$select_shop_request = mysql_query('SELECT id, sells, retailer from pages where in_room=\''.$room.'\' and type=\'shop\'') or die(mysql_error());
if(mysql_num_rows($select_shop_request) != 1)
{
	// The shop you are visiting does not exist
	header('location: ../redirect.php');
	exit();
}
$select_shop_array = mysql_fetch_array($select_shop_request);
$retailer = $select_shop_array['retailer'];



$select_money_request = mysql_query('SELECT position.money, count(*) from position, inventories where position.id='.$player_id .' and inventories.belongs_to=position.id LIMIT 0,1') or die(mysql_error());
$select_money_array = mysql_fetch_array($select_money_request);
$money = $select_money_array['money'];
$total_items = $select_money_array[1];



$select_sells_request = mysql_query('SELECT inv.id, it.id, it.name, it.price, it.quest_item, it.durability, inv.durability from items as it, inventories as inv where inv.belongs_to='.$player_id.' and it.id=inv.item_id order by it.id') or die(mysql_error());
$total_sell_items = mysql_num_rows($select_sells_request);
for($a = 0; $a < $total_sell_items; $a++)
{
	$sellable[] = mysql_fetch_array($select_sells_request);
	// echo 'Sells '.$selling[count($selling) - 1][0].': '.$selling[count($selling) - 1][1].'<br>';
}

mysql_close();
?>
<html>
<head>
<title>
Test shop
</title>
<style>
body
{
padding: 0px;
margin: 0px;
}

.screen
{
width: 302px;
height: 166px;
padding: 2px;
overflow-x: hidden;
overflow-y: auto;
background-color: #F0F0F0;
}

.sellbox
{
	height: 100px;
	width: 260px;
	border: 1px solid black;
	border-right: none;
	font-size: 15px;
	background-color: white;
	overflow: hidden;
	position: relative;
	display: inline-block;
}
.sellcontain
{
	width: 282px;
	height: 102px;
	position: relative;
	left: 9px;
	top: 0px;
}
.sell_item_normal
{
	height: 19px;
	width: 260px;
	border-bottom: 1px solid gray;
	background-color: #F0F0F0;
	text-align: center;
	position: relative;
	
}
.sell_item_selected
{
	height: 19px;
	width: 260px;
	border-bottom: 1px solid gray;
	background-color: aqua;
	text-align: center;
	position: relative;
}
.arrow_up
{
	width: 15px;
	height: 15px;
	padding: 2px;
	background-color: #F0F0F0;
	background-repeat: no-repeat;
	background-position: 2px 2px;
	border-bottom: 1px solid gray;
}
.arrow_down
{
	width: 15px;
	height: 15px;
	padding: 2px;
	background-color: #F0F0F0;
	background-repeat: no-repeat;
	background-position: 2px 2px;
	border-top: 1px solid gray;
}

.sbar
{
	height: 100px;
	width: 19px;
	background-color: #CDCDCD;
	position: absolute;
	border: 1px solid gray;
	display: inline-block;
}

.sell_item_image
{
	position: absolute;
	top: 2px;
	left: 2px;
}

.button
{
	border: 2px outset black;
	background-color: aqua;
	padding: 2px;
	font-size: 12px;
	display: inline-block;
}
#sellconfirm
{
	position: absolute;
	top: 15px;
	left: 30px;
	width: 236px;
	height: 125px;
	padding: 5px;
	background-color: white;
	border: 2px outset blue;
}

#displayerror
{
	color: red;
	font-size: 12px;
	max-width: 100px;
	display: inline-block;
}
#sell_button
{
	border: 2px solid gray;
	background-color: #DCDCDC;
	padding: 2px;
	padding-left: 5px;
	padding-right: 5px;
	font-size: 12px;
	position: absolute;
	right: 10px;
	display: inline-block;
}
#cancel_button
{
	border: 2px solid gray;
	background-color: #DCDCDC;
	padding: 2px;
	padding-left: 5px;
	padding-right: 5px;
	font-size: 12px;
	position: absolute;
	right: 50px;
	display: inline-block;
}

.loading_sell
{
	font-size: 12px;
}

.sellable_price
{
	display: inline-block;
	position: absolute;
	right: 1px;
	top: 0px;
	width: 45px;
	text-align: left;
}

.durability_bar
{
	height: 1px;
	width: 11px;
	border: 1px solid black;
	background-color: white;
	position: absolute;
	bottom: 1px;
	left: 1px;
}

.in_durability_bar
{
	height: 1px;
	background-color: lime;
}
</style>
<script>
var sellrequest;

if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
	sellrequest=new XMLHttpRequest();
}
else
{// code for IE6, IE5
	sellrequest=new ActiveXObject("Microsoft.XMLHTTP");
}
  
var money = <?php echo $money;?>;
var total_items_in_inventory = <?php echo $total_items;?>;
var selected_item = 1;
var sellable = new Array(0);
var total_items = 0;
var selector_open = false;

var items_in_box = 5;
var item_bar_height = 20;

var downclick = 0;
var upclick = 0;

var nbuttoncolor = '#F0F0F0';
var sbuttoncolor = 'aqua';

function sellable_down(makeblink)
{
	if(selected_item >= total_items)
	{
		return;
	}
	
	if(makeblink)
	{
		document.getElementById('sell_arrow_down').style.backgroundColor = sbuttoncolor;
		downclick++;
		setTimeout('if(downclick == '+downclick+'){document.getElementById("sell_arrow_down").style.backgroundColor = "'+nbuttoncolor+'";}',200);
	}
	
	
	selected_item++;
	var els = document.getElementsByClassName('sell_item_selected');
	for(var i in els)
	{
		els[i].className = 'sell_item_normal';
	}
	document.getElementById('sell_item_'+selected_item).className = 'sell_item_selected';
	var boxscrolltop = document.getElementById('sellbox').scrollTop;
	if(((selected_item + 1) * item_bar_height) > (boxscrolltop + items_in_box * item_bar_height))
	{
		document.getElementById('sellbox').scrollTop = (selected_item + 1) * item_bar_height - items_in_box * item_bar_height;
	}
}

function sellable_up(makeblink)
{
	if(selected_item <= 1)
	{
		return;
	}
	
	if(makeblink)
	{
		document.getElementById('sell_arrow_up').style.backgroundColor = sbuttoncolor;
		upclick++;
		setTimeout('if(upclick == '+upclick+'){document.getElementById("sell_arrow_up").style.backgroundColor = "'+nbuttoncolor+'";}',200);
	}
	
	selected_item--;
	var els = document.getElementsByClassName('sell_item_selected');
	for(var i in els)
	{
		els[i].className = 'sell_item_normal';
	}
	document.getElementById('sell_item_'+selected_item).className = 'sell_item_selected';
	var boxscrolltop = document.getElementById('sellbox').scrollTop;
	if((selected_item - 1) * item_bar_height <= (boxscrolltop + item_bar_height))
	{
		document.getElementById('sellbox').scrollTop = (selected_item - 2) * item_bar_height;
	}
}

function select_sellable_item(in_line)
{
	if(in_line == selected_item)
	{
		showsellconfirm();
	}
	else
	{
		var bdiff = in_line - selected_item;
		var dofunction = 'sellable_down';
		if(bdiff < 0)
		{
			bdiff = bdiff * -1;
			dofunction = 'sellable_up';
		}
		for(var a = 0; a < bdiff; a++)
		{
			eval(dofunction+'();');
		}
	}
}

var id;
var item_id;
var item_name;
var item_price;
var item_questitem;
var used_tool;
function showsellconfirm()
{
	selector_open = true;
	id = sellable[selected_item][0];
	item_id = sellable[selected_item][1];
	item_name = sellable[selected_item][2];
	item_price = sellable[selected_item][3];
	item_questitem = sellable[selected_item][4];
	used_tool = sellable[selected_item][5];
	var html_in_box = '<div style="text-align: center; font-weight: bold;">Sell item?</div>';
	
	html_in_box += "<table style='width: 220px;'><tr><td><img src='../images/items/"+item_id+".png'> "+item_name+" for <img src='../images/items/3.png'> <span id='price'>"+item_price+"</span></td></tr></table><div id='displayerror'></div><div id='cancel_button' onClick='hidesellconfirm();'>Cancel</div><div id='sell_button' onClick='sell_item();'>Sell</div>";
	
	document.getElementById('sellconfirm').innerHTML = html_in_box;
	if(item_questitem)
	{
		document.getElementById('displayerror').innerHTML = 'You can\'t sell a quest item';
	}
	if(used_tool)
	{
		document.getElementById('displayerror').innerHTML = 'You can\'t sell a used tool';
	}
	document.getElementById('sellconfirm').style.display = 'block';
}

function hidesellconfirm()
{
	document.getElementById('sellconfirm').style.display = 'none';
	selector_open = false;
}

function sell_item()
{
	if(item_questitem)
	{
		document.getElementById('displayerror').innerHTML = 'You can\'t sell a quest item!';
	}
	else if(used_tool)
	{
		document.getElementById('displayerror').innerHTML = 'You can\'t sell a used tool!';
	}
	else
	{
		document.getElementById('displayerror').innerHTML = '';
		document.getElementById('sellconfirm').innerHTML += '<div class="loading_sell">Selling item... <img src="../images/loading.gif"></div>';
		sellrequest.onreadystatechange=function()
		{
			if (sellrequest.readyState==4 && sellrequest.status==200)
			{
				var responseraw = sellrequest.responseText;
				var responsesplit = responseraw.split(';split;');
				
				if(responsesplit[0] == 'success')
				{
					alert('Succesfully sold');
					document.getElementById('sellbox').innerHTML = responsesplit[1];
					sellable = new Array();
					total_items = 0;
					var ost = document.getElementById('sellbox').scrollTop;
					eval(responsesplit[2]);
					if(selected_item > total_items)
					{
						selected_item = total_items;
					}
					var els = document.getElementsByClassName('sell_item_selected');
					for(var i in els)
					{
						els[i].className = 'sell_item_normal';
					}
					document.getElementById('sell_item_'+selected_item).className = 'sell_item_selected';
					money = parseInt(money) + parseInt(item_price);
					total_items_in_inventory = parseInt(total_items_in_inventory) - 1;
					document.getElementById('i_have_coins').innerHTML = money;
					document.getElementById('items_in_inventory').innerHTML = total_items_in_inventory;
				}
				else
				{
					alert('Error while selling');
				}
				
				hidesellconfirm();
			}
		}
		sellrequest.open("POST","attemp_sell.php?rand="+Math.random(),true);
		sellrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		sellrequest.send("sell_item="+id);
	}
}



function KeyDownCheck(KeyID)
{
	if(selector_open)
	{
		switch(KeyID)
		{
			case 13: sell_item(); break;
			case 38: howmany_up(1); break;
			case 40: howmany_down(1); break;
		}
	}
	else
	{
		switch(KeyID)
		{
			case 13: select_sellable_item(selected_item); break;
			case 38: sellable_up(1); break;
			case 40: sellable_down(1); break;
		}
	}
}
</script>
</head>
<body>
<div class='screen'>
<img src='../images/items/3.png'> <span id='i_have_coins'><?php echo $money;?></span> | <img src='../images/icons/backpack.png'> <span id='items_in_inventory'><?php echo $total_items;?></span>
<div onClick='parent.close_iframe();' style='background-image: url(../images/icons/close.png); display: inline-block; position: absolute; right: 2px; top: 2px; width: 15px; height: 15px;'></div><br>
<div style='font-weight: bold; text-align: center;'>Sell items</div>
<div class='sellcontain'>
<div class='sellbox' id='sellbox'>
<?php
// Laat de koopbare
$item_class = 'selected';
$idplus = 1;
if($total_sell_items == 0)
{
	echo 'You have no items.';
}
else
{
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
}
echo '<script>'.$pass_to_javascript.'</script>';
?>
</div><div class='sbar'>
<div id='sell_arrow_up' class='arrow_up' style='position: absolute; top: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_up.png);' onClick='sellable_up(1);'></div>

<div id='sell_arrow_down' class='arrow_down' style='position: absolute; bottom: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_down.png);' onClick='sellable_down(1);'></div>
</div>
</div>
<div id='sellconfirm' style='display: none;'>Are you sure you want to sell this item?</div>
</div>
</body>
</html>