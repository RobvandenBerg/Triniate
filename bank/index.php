<?php
include_once('../include_this.php');
include_once('bankfunc.php');
light_login('../please_login.php');

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

$returned_array = get_bank($player_id);
$returned = $returned_array[0];
$js_returned = $returned_array[1];

$select_money_request = mysql_query('SELECT position.money, position.money_bank, count(*) from position LEFT JOIN inventories on inventories.belongs_to=position.id where position.id='.$player_id .' LIMIT 0,1') or die(mysql_error());
$select_money_array = mysql_fetch_array($select_money_request);
$money_inventory = $select_money_array['money'];
$money_bank = $select_money_array['money_bank'];
$total_items_in_inventory = $select_money_array[2];
mysql_close();
?>
<html>
<head>
<title>
Triniate - Bank
</title>
<style>
body
{
	padding: 0px;
	margin: 0px;
}
#screen
{
	width: 302px;
	height: 166px;
	padding: 2px;
	padding-right: 3px;
	padding-left: 1px;
	background-color: brown;
}
.td
{
	width: 20px;
	height: 20px;
	background-color: white;
	position: relative;
	display: table-cell;
}

.item_normal
{

}

.item_selected
{
	border: 1px solid blue;
	background-color: aqua;
}

.bank_howmany
{
	position: absolute;
	bottom: 0px;
	right: 0px;
	font-size: 10px;
	color: red;
	background-color: white;
	border: 1px solid black;
	height: 10px;
}

#confirmbox
{
	position: absolute;
	top: 10px;
	left: 10px;
	padding: 5px;
	width: 272px;
	height: 136px;
	background-color: white;
	border: 2px outset blue;
	z-index: 3;
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

.displayerror
{
	color: red;
	font-size: 12px;
	max-width: 100px;
	display: inline-block;
}
#withdraw_button
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
	right: 80px;
	display: inline-block;
}

.tab_normal
{
	border: 1px solid black;
	background-color: #CDCDCD;
	width: 145px;
	display: inline-block;
	position: relative;
	top: 1px;
	padding-left: 2px;
	z-index: 2;
	font-size: 12px;
}

.tab_selected
{
	border: 1px solid black;
	border-bottom: 1px solid #F0F0F0;
	background-color: #F0F0F0;
	width: 145px;
	display: inline-block;
	position: relative;
	top: 1px;
	padding-left: 2px;
	z-index: 2;
	font-size: 12px;
}
.tab_box
{
	padding: 1px;
	padding-top: 2px;
	width: 300px;
	height: auto;
	border: 1px solid black;
	background-color: #F0F0F0;
	position: absolute;
	top: 0px;
	font-size: 14px;
}


.durability_bar
{
	height: 1px;
	width: 11px;
	border: 1px solid black;
	background-color: white;
	position: absolute;
	bottom: 3px;
	left: 1px;
}

.in_durability_bar
{
	height: 1px;
	background-color: lime;
}


.tab_stats
{
	display: inline-block;
	position: absolute;
	right: 2px;
	top: 2px;
	font-size: 12px;
}

.small_icon
{
	width: 12px;
	height: 12px;
}

.transfer_money_button
{
	display: inline-block;
	border: 1px solid black;
	background-color: #CDCDCD;
	padding: 2px;
	margin-left: 10px;
	font-size: 14px;
}
</style>
<script>
var loadrequest;
var processrequest;
var transferrequest;

if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
	processrequest=new XMLHttpRequest();
	loadrequest = new XMLHttpRequest();
	transferrequest = new XMLHttpRequest();
}
else
{// code for IE6, IE5
	processrequest=new ActiveXObject("Microsoft.XMLHTTP");
	processrequest=new ActiveXObject("Microsoft.XMLHTTP");
	transferrequest=new ActiveXObject("Microsoft.XMLHTTP");
}

var total_items_in_bank;
var total_items_in_inventory = <?php echo $total_items_in_inventory;?>;
var money_inventory = <?php echo $money_inventory;?>;
var money_bank = <?php echo $money_bank;?>;
var bank_items;
var inventory_items;
var selected_bank_item = 1;
var selected_inventory_item = 1;
var columns = 15;
<?php echo $js_returned;?>

function KeyDownCheck(KeyID)
{
	switch(KeyID)
	{
		case 37:
		//left
		eval(opened+'_navigate_left();');
		break;
		
		case 38:
		// up
		eval(opened+'_navigate_up();');
		break;
		
		case 39:
		//right
		eval(opened+'_navigate_right();');
		break;
		
		case 40:
		//down
		eval(opened+'_navigate_down();');
		break;
		
		case 13:
		// enter
		if(confirm_open)
		{
			if(opened == 'bank')
			{
				withdraw_item();
			}
			else
			{
				deposit_item();
			}
		}
		else
		{
			eval(opened+'_showconfirm();');
		}
		break;
	}
}

function bank_navigate_right()
{
	if(selected_bank_item >= total_items_in_bank)
	{
		return;
	}
	if(selected_bank_item % 15 == 0)
	{
		return;
	}
	selected_bank_item++;
	bank_navigate();
}
function bank_navigate_left()
{
	if(selected_bank_item <= 1)
	{
		return;
	}
	if((selected_bank_item - 1) % 15 == 0)
	{
		return;
	}
	selected_bank_item--;
	bank_navigate();
}
function bank_navigate_down()
{
	if(selected_bank_item + columns > total_items_in_bank)
	{
		return;
	}
	selected_bank_item = selected_bank_item + columns;
	bank_navigate();
}

function bank_navigate_up()
{
	if(selected_bank_item - columns < 1)
	{
		return;
	}
	selected_bank_item = selected_bank_item - columns;
	bank_navigate();
}

function bank_navigate_to(in_line)
{
	if(selected_bank_item == in_line)
	{
		bank_showconfirm();
	}
	else
	{
		selected_bank_item = in_line;
		bank_navigate();
	}
}

function bank_navigate()
{
	var els = document.getElementsByClassName('item_selected');
	for(var i in els)
	{
		els[i].className = 'item_normal';
	}
	
	document.getElementById('bank_item_'+selected_bank_item).className = 'item_selected';
}






function inventory_navigate_right()
{
	if(selected_inventory_item >= total_items_in_inventory)
	{
		return;
	}
	if(selected_inventory_item % 15 == 0)
	{
		return;
	}
	selected_inventory_item++;
	inventory_navigate();
}
function inventory_navigate_left()
{
	if(selected_inventory_item <= 1)
	{
		return;
	}
	if((selected_inventory_item - 1) % 15 == 0)
	{
		return;
	}
	selected_inventory_item--;
	inventory_navigate();
}
function inventory_navigate_down()
{
	if(selected_inventory_item + columns > total_items_in_inventory)
	{
		return;
	}
	selected_inventory_item = selected_inventory_item + columns;
	inventory_navigate();
}

function inventory_navigate_up()
{
	if(selected_inventory_item - columns < 1)
	{
		return;
	}
	selected_inventory_item = selected_inventory_item - columns;
	inventory_navigate();
}

function inventory_navigate_to(in_line)
{
	if(selected_inventory_item == in_line)
	{
		inventory_showconfirm();
	}
	else
	{
		selected_inventory_item = in_line;
		inventory_navigate();
	}
}

function inventory_navigate()
{
	var els = document.getElementsByClassName('item_selected');
	for(var i in els)
	{
		els[i].className = 'item_normal';
	}
	
	document.getElementById('inventory_item_'+selected_inventory_item).className = 'item_selected';
}


var id_bank;
var item_id_bank;
var item_name_bank;
var quantity_bank;
var durability_bank;
function inventory_showconfirm()
{
	if(!inventory_items[selected_inventory_item])
	{
		return;
	}
	
	confirm_open = true;
	
	id_inventory = inventory_items[selected_inventory_item][0];
	item_id_inventory = inventory_items[selected_inventory_item][1];
	item_name_inventory = inventory_items[selected_inventory_item][2];
	durability_inventory = inventory_items[selected_inventory_item][3];
	var percentage = durability_inventory;
	
	var html_in_box = '<div style="font-weight: bold; text-align: center;">Deposit?</div>';
	html_in_box += "<table style='width: 220px;'><td style='text-align: center;'><div style='position: relative; width: 15px; height: 15px; display: inline-block;'><img src='../images/items/"+item_id_inventory+".png'>";
	
	html_in_box += "<div id='durability_bar_confirm' class='durability_bar' style='bottom: 1px; display: none;'><div id='in_durability_bar_confirm' style='width: 90%; background-color: lime;' class='in_durability_bar'></div></div>";
	
	html_in_box += "</div> "+item_name_inventory+"</td></tr></table><div id='displayerror_inventory' class='displayerror'></div><div id='cancel_button' onClick='hideconfirm();'>Cancel</div><div id='withdraw_button' onClick='deposit_item();'>Deposit</div>";
	
	document.getElementById('confirmbox').innerHTML = html_in_box;
	document.getElementById('confirmbox').style.display = 'block';
	
	if(percentage && percentage != 0)
	{
		var barcolor = 'lime';
		if(percentage <= 35)
		{
			barcolor = 'orange';
		}
		if(percentage <= 15)
		{
			barcolor = 'red';
		}
		document.getElementById('durability_bar_confirm').style.display = 'block';
		document.getElementById('in_durability_bar_confirm').style.width = percentage + '%';
		document.getElementById('in_durability_bar_confirm').style.backgroundColor = barcolor;
	}
}


function bank_showconfirm()
{
	if(!bank_items[selected_bank_item])
	{
		return;
	}
	
	confirm_open = true;
	
	id_bank = bank_items[selected_bank_item][0];
	item_id_bank = bank_items[selected_bank_item][1];
	item_name_bank = bank_items[selected_bank_item][2];
	quantity_bank = bank_items[selected_bank_item][3];
	durability_bank = bank_items[selected_bank_item][4];
	var percentage = durability_bank;
	
	var html_in_box = '<div style="font-weight: bold; text-align: center;">Withdraw how many?</div>';
	html_in_box += "<table style='width: 220px;'><tr><td><div style='width: 21px; height: 40px; border: 1px solid purple; position: relative;'><div id='howmany_bank_arrow_up' class='arrow_up' style='border: 1px solid gray; position: absolute; top: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_up.png);' onClick='howmany_bank_up(1);'></div><div id='howmany_bank_arrow_down' class='arrow_down' style='border: 1px solid gray; border-top: none; position: absolute; bottom: 0px; background-color: #F0F0F0; background-image: url(../images/icons/arrow_down.png);' onClick='howmany_bank_down(1);'></div></div></td><td><div style='position: relative; width: 15px; height: 15px; display: inline-block;'><img src='../images/items/"+item_id_bank+".png'>";
	
	html_in_box += "<div id='durability_bar_confirm' class='durability_bar' style='bottom: 1px; display: none;'><div id='in_durability_bar_confirm' style='width: 90%; background-color: lime;' class='in_durability_bar'></div></div>";
	
	html_in_box += "</div> "+item_name_bank+" x<span id='howmany_bank'>1</span></td></tr></table><div id='displayerror_bank' class='displayerror'></div><div id='cancel_button' onClick='hideconfirm();'>Cancel</div><div id='withdraw_button' onClick='withdraw_item();'>Withdraw</div>";
	
	document.getElementById('confirmbox').innerHTML = html_in_box;
	document.getElementById('confirmbox').style.display = 'block';
	
	if(percentage && percentage != 0)
	{
		var barcolor = 'lime';
		if(percentage <= 35)
		{
			barcolor = 'orange';
		}
		if(percentage <= 15)
		{
			barcolor = 'red';
		}
		document.getElementById('durability_bar_confirm').style.display = 'block';
		document.getElementById('in_durability_bar_confirm').style.width = percentage + '%';
		document.getElementById('in_durability_bar_confirm').style.backgroundColor = barcolor;
	}
}

var confirm_open = false;

function hideconfirm()
{
	confirm_open = false;
	document.getElementById('confirmbox').style.display = 'none';
}


var upclick_bank = 0;
var downclick_bank = 0;
var nbuttoncolor = '#F0F0F0';
var sbuttoncolor = 'aqua';

function howmany_bank_up(makeblink)
{
	var currhowmany = parseInt(document.getElementById('howmany_bank').innerHTML);
	var newhowmany = currhowmany + 1;
	
	
	if(newhowmany > quantity_bank)
	{
		return;
	}
	else if(newhowmany + total_items_in_inventory > 30)
	{
		if(total_items_in_inventory < 29)
		{
			document.getElementById('displayerror_bank').innerHTML = 'You only have space for '+(30 - total_items_in_inventory) + ' more items in your inventory';
		}
		else if(total_items_in_inventory == 29)
		{
			document.getElementById('displayerror_bank').innerHTML = 'You only have space for one more item in your inventory';
		}
		else
		{
			document.getElementById('displayerror_bank').innerHTML = 'You have no space left in your inventory';
		}
		return;
	}
	else
	{
		document.getElementById('howmany_bank').innerHTML = newhowmany;
		document.getElementById('displayerror_bank').innerHTML = '';
		if(makeblink)
		{
			document.getElementById('howmany_bank_arrow_up').style.backgroundColor = sbuttoncolor;
			upclick_bank++;
			setTimeout('if(upclick_bank == '+upclick_bank+'){document.getElementById("howmany_bank_arrow_up").style.backgroundColor = "'+nbuttoncolor+'";}',200);
		}
	}
}

function howmany_bank_down(makeblink)
{
	var currhowmany = parseInt(document.getElementById('howmany_bank').innerHTML);
	var newhowmany = currhowmany - 1;
	if(newhowmany < 1)
	{
		return;
	}
	else
	{
		document.getElementById('howmany_bank').innerHTML = newhowmany;
		document.getElementById('displayerror_bank').innerHTML = '';
		if(makeblink)
		{
			document.getElementById('howmany_bank_arrow_down').style.backgroundColor = sbuttoncolor;
			downclick_bank++;
			setTimeout('if(downclick_bank == '+downclick_bank+'){document.getElementById("howmany_bank_arrow_down").style.backgroundColor = "'+nbuttoncolor+'";}',200);
		}
	}
}

function withdraw_item()
{
	var currhowmany = parseInt(document.getElementById('howmany_bank').innerHTML);
	
	
	if(currhowmany > quantity_bank)
	{
		document.getElementById('displayerror_bank').innerHTML = 'You don\'t have that much!';
		return;
	}
	if(currhowmany + total_items_in_inventory > 30)
	{
		if(total_items_in_inventory < 29)
		{
			document.getElementById('displayerror_bank').innerHTML = 'You only have space for '+(30 - total_items_in_inventory) + ' more items in your inventory!';
		}
		else if(total_items_in_inventory == 29)
		{
			document.getElementById('displayerror_bank').innerHTML = 'You only have space for one more item in your inventory!';
		}
		else
		{
			document.getElementById('displayerror_bank').innerHTML = 'You have no space left in your inventory!';
		}
		return;
	}
	document.getElementById('confirmbox').innerHTML += '<br>Withdrawing... <img src="../images/loading.gif">';
	
	processrequest.onreadystatechange=function()
	{
		if (processrequest.readyState==4 && processrequest.status==200)
		{
			var responseraw = processrequest.responseText;
			var responsesplit = responseraw.split(';split;');
			if(responsesplit[0] != 'success')
			{
				alert('Unknown error while withdrawing.');
				window.location = window.location;
				return;
			}
			if(responsesplit.length == 3)
			{
				update_bank(responsesplit[1],responsesplit[2]);
				hideconfirm();
			}
		}
	}
	processrequest.open("POST","withdraw.php?rand="+Math.random(),true);
	processrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	processrequest.send('withdraw_item='+id_bank+'&amount='+currhowmany);
}


function deposit_item()
{
	document.getElementById('confirmbox').innerHTML += '<br>Depositing... <img src="../images/loading.gif">';
	
	processrequest.onreadystatechange=function()
	{
		if (processrequest.readyState==4 && processrequest.status==200)
		{
			var responseraw = processrequest.responseText;
			var responsesplit = responseraw.split(';split;');
			if(responsesplit[0] != 'success')
			{
				alert('Unknown error while depositing.');
				//window.location = window.location;
				//return;
			}
			if(responsesplit.length == 3)
			{
				update_inventory(responsesplit[1],responsesplit[2]);
				hideconfirm();
			}
		}
	}
	processrequest.open("POST","deposit.php?rand="+Math.random(),true);
	processrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	processrequest.send('deposit_item='+id_inventory);
}


var opened = 'bank';
function switch_bank()
{
	if(opened == 'bank')
	{
		return;
	}
	
	var els = document.getElementsByClassName('item_selected');
	var cset = false;
	for(var i in els)
	{
		if(!cset)
		{
			cset = true;
			els[i].className = 'item_normal';
		}
	}
	
	
	opened = 'bank';
	
	document.getElementById('tab_box_bank').innerHTML = 'Loading bank <img src="../images/loading.gif">';
	
	loadrequest.onreadystatechange=function()
	{
		if (loadrequest.readyState==4 && loadrequest.status==200)
		{
			var responseraw = loadrequest.responseText;
			var responsesplit = responseraw.split(';split;');
			if(responsesplit.length == 2)
			{
				update_bank(responsesplit[0],responsesplit[1]);
			}
		}
	}
	loadrequest.open("GET","load_bank.php?rand="+Math.random(),true);
	loadrequest.send();
	
	document.getElementById('tab_box_inventory').style.display = 'none';
	document.getElementById('tab_box_bank').style.display = 'block';
	document.getElementById('tab_inventory').className = 'tab_normal';
	document.getElementById('tab_bank').className = 'tab_selected';
}

function update_inventory(html_in_box,eval_code)
{
	document.getElementById('tab_box_inventory').innerHTML = html_in_box;
	eval(eval_code);
	
	update_barstats();
}

function update_bank(html_in_box,eval_code)
{
	document.getElementById('tab_box_bank').innerHTML = html_in_box;
	eval(eval_code);
	
	update_barstats();
}

function update_barstats()
{
	document.getElementById('inventory_items_tab').innerHTML = total_items_in_inventory;
	document.getElementById('bank_money_tab').innerHTML = money_bank;
	document.getElementById('inventory_money_tab').innerHTML = money_inventory;
}

function update_money()
{
	document.getElementById('bank_money_tab').innerHTML = money_bank;
	document.getElementById('inventory_money_tab').innerHTML = money_inventory;
}

function switch_inventory()
{
	if(opened == 'inventory')
	{
		return;
	}
	
	var els = document.getElementsByClassName('item_selected');
	var cset = false;
	for(var i in els)
	{
		if(!cset)
		{
			cset = true;
			els[i].className = 'item_normal';
		}
	}
	
	opened = 'inventory';
	
	document.getElementById('tab_box_inventory').innerHTML = 'Loading inventory <img src="../images/loading.gif">';
	
	loadrequest.onreadystatechange=function()
	{
		if (loadrequest.readyState==4 && loadrequest.status==200)
		{
			var responseraw = loadrequest.responseText;
			var responsesplit = responseraw.split(';split;');
			if(responsesplit.length == 2)
			{
				update_inventory(responsesplit[0],responsesplit[1]);
			}
		}
	}
	loadrequest.open("GET","load_inventory.php?rand="+Math.random(),true);
	loadrequest.send();
	
	document.getElementById('tab_box_inventory').style.display = 'block';
	document.getElementById('tab_box_bank').style.display = 'none';
	document.getElementById('tab_inventory').className = 'tab_selected';
	document.getElementById('tab_bank').className = 'tab_normal';
}




function transfer_money_bank_up(makeblink)
{
	var currvalue = parseInt(document.getElementById('transfer_money_bank').innerHTML);
	var newvalue = currvalue + 1;
	if(newvalue > money_bank)
	{
		return;
	}
	document.getElementById('transfer_money_bank').innerHTML = newvalue;
}

function transfer_money_bank_down(makeblink)
{
	var currvalue = parseInt(document.getElementById('transfer_money_bank').innerHTML);
	var newvalue = currvalue - 1;
	if(newvalue < 0)
	{
		return;
	}
	document.getElementById('transfer_money_bank').innerHTML = newvalue;
}



function transfer_money_inventory_up(makeblink)
{
	var currvalue = parseInt(document.getElementById('transfer_money_inventory').innerHTML);
	var newvalue = currvalue + 1;
	if(newvalue > money_inventory)
	{
		return;
	}
	document.getElementById('transfer_money_inventory').innerHTML = newvalue;
}

function transfer_money_inventory_down(makeblink)
{
	var currvalue = parseInt(document.getElementById('transfer_money_inventory').innerHTML);
	var newvalue = currvalue - 1;
	if(newvalue < 0)
	{
		return;
	}
	document.getElementById('transfer_money_inventory').innerHTML = newvalue;
}


var busym = false;
function transfer_money_inventory()
{
	if(busym)
	{
		return;
	}
	var currvalue = parseInt(document.getElementById('transfer_money_inventory').innerHTML);
	if(currvalue == 0 || currvalue > money_inventory)
	{
		return;
	}
	document.getElementById('transfer_loading_inventory').style.display = 'inline-block';
	transferrequest.onreadystatechange=function()
	{
		if (transferrequest.readyState==4 && transferrequest.status==200)
		{
			document.getElementById('transfer_loading_inventory').style.display = 'none';
			var responseraw = transferrequest.responseText;
			var responsesplit = responseraw.split(';split;');
			if(responsesplit[0] != 'success')
			{
				alert('Unknown error while depositing money.');
				window.location = window.location;
				return;
			}
			if(responsesplit.length == 3)
			{
				money_inventory = responsesplit[1];
				money_bank = responsesplit[2];
				update_money(responsesplit[1],responsesplit[2]);
				document.getElementById('transfer_money_inventory').innerHTML = 0;
			}
			busym = false;
		}
	}
	busym = true;
	transferrequest.open("POST","transfer_money.php?rand="+Math.random(),true);
	transferrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	transferrequest.send('to=bank&amount='+currvalue);
}

function transfer_money_bank()
{
	if(busym)
	{
		return;
	}
	var currvalue = parseInt(document.getElementById('transfer_money_bank').innerHTML);
	if(currvalue == 0 || currvalue > money_bank)
	{
		return;
	}
	document.getElementById('transfer_loading_bank').style.display = 'inline-block';
	transferrequest.onreadystatechange=function()
	{
		if (transferrequest.readyState==4 && transferrequest.status==200)
		{
			document.getElementById('transfer_loading_bank').style.display = 'none';
			var responseraw = transferrequest.responseText;
			var responsesplit = responseraw.split(';split;');
			if(responsesplit[0] != 'success')
			{
				alert('Unknown error while withdrawing money.');
				window.location = window.location;
				return;
			}
			if(responsesplit.length == 3)
			{
				money_inventory = responsesplit[1];
				money_bank = responsesplit[2];
				update_money(responsesplit[1],responsesplit[2]);
				document.getElementById('transfer_money_bank').innerHTML = 0;
			}
			busym = false;
		}
	}
	busym = true;
	transferrequest.open("POST","transfer_money.php?rand="+Math.random(),true);
	transferrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	transferrequest.send('to=inventory&amount='+currvalue);
}
</script>
</head>
<body>
<div id='screen'>
<div style='text-align: center; font-weight: bold;'><img src='../images/icons/coin_gold.png'> Bank <img src='../images/icons/coin_silver.png'></div><div style='position: absolute; top: 2px; right: 2px; width: 15px; height: 15px;' onClick='parent.close_iframe();'><img src='../images/icons/close.png'></div>
<div id='tab_bank' class='tab_selected' onClick='switch_bank();'>Bank<div class='tab_stats'><img class='small_icon' src='../images/items/3.png'> <span id='bank_money_tab'><?php echo $money_bank;?></span></div></div><div id='tab_inventory' class='tab_normal' onClick='switch_inventory();'>Inventory<div class='tab_stats'><img class='small_icon' src='../images/items/3.png'> <span id='inventory_money_tab'><?php echo $money_inventory;?></span> | <img class='small_icon' src='../images/icons/backpack.png'> <span id='inventory_items_tab'><?php echo $total_items_in_inventory;?></span></div></div>
<div style='position: relative; z-index: 1;'>
<div class='tab_box' id='tab_box_bank' style='display: block;'>
<?php echo $returned;?>
</div>

<div class='tab_box' id='tab_box_inventory' style='display: none;'>
TEST
</div>
</div>

<div style='position: absolute; top: 120px; display: none;'>
<input type='button' value='^' onClick='KeyDownCheck(38);'><input type='button' value='Enter' oNclick='KeyDownCheck(13);'><br>
<input type='button' value='<-' onClick='KeyDownCheck(37);'>
<input type='button' value='->' onClick='KeyDownCheck(39);'><br>
<input type='button' value='v' onClick='KeyDownCheck(40);'>
</div>

</div>
<div id='confirmbox' style='display: none;'></div>
</body>
</html>