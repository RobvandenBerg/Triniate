<?php
include('../include_this.php');

light_login('../please_login.php');

mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());
$select_shop_request = mysql_query('SELECT id, sells, retailer from pages where in_room=\''.$room.'\' and type=\'shop\'') or die(mysql_error());
if(mysql_num_rows($select_shop_request) != 1)
{
	// The shop you are visiting does not exist
	mysql_close();
	header('location: ../redirect.php');
	exit();
}
$select_shop_array = mysql_fetch_array($select_shop_request);
$sellstring = $select_shop_array['sells'];
$retailer = $select_shop_array['retailer'];
$sellsplit = explode(',',$sellstring);
$sellcount = count($sellsplit);
$select_items_string = "(";
for($a = 0; $a < $sellcount; $a++)
{
	if($a == ($sellcount-1)){
		$select_items_string .= 'id='.$sellsplit[$a].")";
	} else {
		$select_items_string .= 'id='.$sellsplit[$a]." OR ";
	}
	
}
// echo 'SELECT id, name, price from items where '.$select_items_string.' and quest_item=\'no\' order by id';

$selling = array();
if(isset($select_items_string))
{
	$select_sells_request = mysql_query('SELECT id, name, price from items where '.$select_items_string.' and quest_item=\'no\' order by id') or die(mysql_error());
	$total_sell_items = mysql_num_rows($select_sells_request);
	for($a = 0; $a < $total_sell_items; $a++)
	{
		$buyable[] = mysql_fetch_array($select_sells_request);
		// echo 'Sells '.$selling[count($selling) - 1][0].': '.$selling[count($selling) - 1][1].'<br>';
	}
}

$select_money_request = mysql_query('SELECT position.money, count(*) from position LEFT JOIN inventories on inventories.belongs_to=position.id where position.id='.$player_id .' LIMIT 0,1') or die(mysql_error());
$select_money_array = mysql_fetch_array($select_money_request);
$money = $select_money_array['money'];
$total_items = $select_money_array[1];
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

.buybox
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
.buycontain
{
	width: 282px;
	height: 102px;
	position: relative;
	left: 9px;
	top: 0px;
}
.buy_item_normal
{
	height: 19px;
	width: 260px;
	border-bottom: 1px solid gray;
	background-color: #F0F0F0;
	text-align: center;
	position: relative;
	
}
.buy_item_selected
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

.buy_item_image
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
#buyconfirm
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
#buy_button
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

.loading_buy
{
	font-size: 12px;
}

.buyable_price
{
	display: inline-block;
	position: absolute;
	right: 1px;
	top: 0px;
	width: 45px;
	text-align: left;
}
</style>
<script>
var buyrequest;

if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
	buyrequest=new XMLHttpRequest();
}
else
{// code for IE6, IE5
	buyrequest=new ActiveXObject("Microsoft.XMLHTTP");
}
  
var money = <?php echo $money;?>;
var total_items_in_inventory = <?php echo $total_items;?>;
var selected_item = 1;
var buyable = new Array(0);
var total_items = 0;
var selector_open = false;

var items_in_box = 5;
var item_bar_height = 20;

var downclick = 0;
var upclick = 0;

var nbuttoncolor = '#F0F0F0';
var sbuttoncolor = 'aqua';

function buyable_down(makeblink)
{
	if(selected_item >= total_items)
	{
		return;
	}
	
	if(makeblink)
	{
		document.getElementById('buy_arrow_down').style.backgroundColor = sbuttoncolor;
		downclick++;
		setTimeout('if(downclick == '+downclick+'){document.getElementById("buy_arrow_down").style.backgroundColor = "'+nbuttoncolor+'";}',200);
	}
	
	
	selected_item++;
	var els = document.getElementsByClassName('buy_item_selected');
	for(var i in els)
	{
		els[i].className = 'buy_item_normal';
	}
	document.getElementById('buy_item_'+selected_item).className = 'buy_item_selected';
	var boxscrolltop = document.getElementById('buybox').scrollTop;
	if(((selected_item + 1) * item_bar_height) > (boxscrolltop + items_in_box * item_bar_height))
	{
		document.getElementById('buybox').scrollTop = (selected_item + 1) * item_bar_height - items_in_box * item_bar_height;
	}
}

function buyable_up(makeblink)
{
	if(selected_item <= 1)
	{
		return;
	}
	
	if(makeblink)
	{
		document.getElementById('buy_arrow_up').style.backgroundColor = sbuttoncolor;
		upclick++;
		setTimeout('if(upclick == '+upclick+'){document.getElementById("buy_arrow_up").style.backgroundColor = "'+nbuttoncolor+'";}',200);
	}
	
	selected_item--;
	var els = document.getElementsByClassName('buy_item_selected');
	for(var i in els)
	{
		els[i].className = 'buy_item_normal';
	}
	document.getElementById('buy_item_'+selected_item).className = 'buy_item_selected';
	var boxscrolltop = document.getElementById('buybox').scrollTop;
	if((selected_item - 1) * item_bar_height <= (boxscrolltop + item_bar_height))
	{
		document.getElementById('buybox').scrollTop = (selected_item - 2) * item_bar_height;
	}
}

function select_buyable_item(in_line)
{
	if(in_line == selected_item)
	{
		showbuyconfirm();
	}
	else
	{
		var bdiff = in_line - selected_item;
		var dofunction = 'buyable_down';
		if(bdiff < 0)
		{
			bdiff = bdiff * -1;
			dofunction = 'buyable_up';
		}
		for(var a = 0; a < bdiff; a++)
		{
			eval(dofunction+'();');
		}
	}
}

var item_id;
var item_name;
var item_price;
function showbuyconfirm()
{
	selector_open = true;
	item_id = buyable[selected_item][0];
	item_name = buyable[selected_item][1];
	item_price = buyable[selected_item][2];
	var html_in_box = '<b>Buy how many?</b>';
	
	html_in_box += "<table style='width: 220px;'><tr><td><div style='width: 21px; height: 40px; border: 1px solid purple; position: relative;'><div id='howmany_arrow_up' class='arrow_up' style='border: 1px solid gray; position: absolute; top: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_up.png);' onClick='howmany_up(1);'></div><div id='howmany_arrow_down' class='arrow_down' style='border: 1px solid gray; border-top: none; position: absolute; bottom: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_down.png);' onClick='howmany_down(1);'></div></div></td><td><img src='../images/items/"+item_id+".png'> x<span id='howmany'>1</span> (Costs <img src='../images/items/3.png'> <span id='price'>"+item_price+"</span>)</td></tr></table><div id='displayerror'></div><div id='cancel_button' onClick='hidebuyconfirm();'>Cancel</div><div id='buy_button' onClick='buy_item();'>Buy</div>";
	
	document.getElementById('buyconfirm').innerHTML = html_in_box;
	document.getElementById('buyconfirm').style.display = 'block';
}

function hidebuyconfirm()
{
	document.getElementById('buyconfirm').style.display = 'none';
	selector_open = false;
}

function howmany_up(makeblink)
{
	var currhowmany = parseInt(document.getElementById('howmany').innerHTML);
	var newhowmany = currhowmany + 1;
	
	
	if(newhowmany * item_price > money)
	{
		document.getElementById('displayerror').innerHTML = 'You don\'t have enough money';
	}
	else if(newhowmany + total_items_in_inventory > 30)
	{
		document.getElementById('displayerror').innerHTML = 'Your inventory doesn\'t have enough room';
	}
	else
	{
		document.getElementById('howmany').innerHTML = newhowmany;
		document.getElementById('displayerror').innerHTML = '';
		document.getElementById('price').innerHTML = item_price * newhowmany;
		if(makeblink)
		{
			document.getElementById('howmany_arrow_up').style.backgroundColor = sbuttoncolor;
			upclick++;
			setTimeout('if(upclick == '+upclick+'){document.getElementById("howmany_arrow_up").style.backgroundColor = "'+nbuttoncolor+'";}',200);
		}
	}
}

function howmany_down(makeblink)
{
	var currhowmany = parseInt(document.getElementById('howmany').innerHTML);
	var newhowmany = currhowmany - 1;
	if(newhowmany < 1)
	{
		document.getElementById('displayerror').innerHTML = 'Buy at least one';
	}
	else
	{
		document.getElementById('howmany').innerHTML = newhowmany;
		document.getElementById('displayerror').innerHTML = '';
		document.getElementById('price').innerHTML = item_price * newhowmany;
		if(makeblink)
		{
			document.getElementById('howmany_arrow_down').style.backgroundColor = sbuttoncolor;
			downclick++;
			setTimeout('if(downclick == '+downclick+'){document.getElementById("howmany_arrow_down").style.backgroundColor = "'+nbuttoncolor+'";}',200);
		}
	}
}

function buy_item()
{
	var howmany = parseInt(document.getElementById('howmany').innerHTML);
	if(howmany * item_price > money)
	{
		document.getElementById('displayerror').innerHTML = 'You don\'t have enough money!';
	}
	else if(howmany + total_items_in_inventory > 30)
	{
		document.getElementById('displayerror').innerHTML = 'Your inventory doesn\'t have enough room!';
	}
	else
	{
		document.getElementById('displayerror').innerHTML = '';
		document.getElementById('buyconfirm').innerHTML += '<div class="loading_buy">Purchasing item... <img src="../images/loading.gif"></div>';
		buyrequest.onreadystatechange=function()
		{
			if (buyrequest.readyState==4 && buyrequest.status==200)
			{
				var responseraw = buyrequest.responseText;
				var responsesplit = responseraw.split(',');
				if(responsesplit.length > 2)
				{
					if(responsesplit[0] == 'success')
					{
						alert('Succesfully purchased');
					}
					else if(responsesplit[3])
					{
						alert('Error while buying: ' + responsesplit[3]);
					}
					else
					{
						alert('Unknown error while buying!');
					}
					money = parseInt(responsesplit[1]);
					total_items_in_inventory = parseInt(responsesplit[2]);
					document.getElementById('i_have_coins').innerHTML = money;
					document.getElementById('items_in_inventory').innerHTML = total_items_in_inventory;
					hidebuyconfirm();
				}
				else
				{
					alert('Unknown error while buying');
					hidebuyconfirm();
				}
			}
		}
		buyrequest.open("POST","attemp_buy.php?rand="+Math.random(),true);
		buyrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		buyrequest.send("howmany="+howmany+"&&buy_item="+item_id);
	}
}



function KeyDownCheck(KeyID)
{
	if(selector_open)
	{
		switch(KeyID)
		{
			case 13: buy_item(); break;
			case 38: howmany_up(1); break;
			case 40: howmany_down(1); break;
		}
	}
	else
	{
		switch(KeyID)
		{
			case 13: select_buyable_item(selected_item); break;
			case 38: buyable_up(1); break;
			case 40: buyable_down(1); break;
		}
	}
}
</script>
</head>
<body>
<div class='screen'>
<img src='../images/items/3.png'> <span id='i_have_coins'><?php echo $money;?></span> | <img src='../images/icons/backpack.png'> <span id='items_in_inventory'><?php echo $total_items;?></span>
<div onClick='parent.close_iframe();' style='background-image: url(../images/icons/close.png); display: inline-block; position: absolute; right: 2px; top: 2px; width: 15px; height: 15px;'></div><br>
<div style='font-weight: bold; text-align: center;'>Buy items</div>
<div class='buycontain'>
<div class='buybox' id='buybox'>
<?php
// Laat de koopbare
$item_class = 'selected';
$idplus = 1;
$pass_to_javascript = "";
foreach($buyable as $buyarray)
{
	$item_id = $buyarray[0];
	$item_name = $buyarray[1];
	$item_price = $buyarray[2];
	$item_name = str_replace('\'','&apos;',$item_name);
	echo '<div onClick=\'select_buyable_item('.$idplus.');\' id=\'buy_item_'.$idplus.'\'class=\'buy_item_'.$item_class.'\'><img class=\'buy_item_image\' src=\'../images/items/'.$item_id.'.png\'> '.$item_name.'<div class=\'buyable_price\'><img src="../images/items/3.png"> '.$item_price.'</div></div>';
	$item_class = 'normal';
	$pass_to_javascript .= 'buyable['.$idplus.'] = new Array('.$item_id.',\''.$item_name.'\','.$item_price.'); total_items++; ';
	$idplus++;
}
echo '<script>'.$pass_to_javascript.'</script>';
?>
</div><div class='sbar'>
<div id='buy_arrow_up' class='arrow_up' style='position: absolute; top: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_up.png);' onClick='buyable_up(1);'></div>

<div id='buy_arrow_down' class='arrow_down' style='position: absolute; bottom: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_down.png);' onClick='buyable_down(1);'></div>
</div>
</div>
<div id='buyconfirm' style='display: none;'>Are you sure you want to buy this item?</div>
</div>
</body>
</html>