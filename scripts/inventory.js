var selected_item = 1;
var in_inventory = new Array(0);
var total_items = 0;

var items_in_box = 5;
var item_bar_height = 18;

var idownclick = 0;
var iupclick = 0;

var nbuttoncolor = '#F0F0F0';
var sbuttoncolor = 'aqua';

function make_blink(id,ncolor,scolor)
{
	document.getElementById(id).style.backgroundColor = scolor;
	setTimeout('document.getElementById("'+id+'").style.backgroundColor = "'+ncolor+'";',200);
}

function inventory_down(makeblink)
{
	if(selected_item >= total_items)
	{
		return;
	}
	
	if(makeblink)
	{
		document.getElementById('inventory_arrow_down').style.backgroundColor = sbuttoncolor;
		idownclick++;
		setTimeout('if(idownclick == '+idownclick+'){document.getElementById("inventory_arrow_down").style.backgroundColor = "'+nbuttoncolor+'";}',200);
	}
	
	
	selected_item++;
	var els = document.getElementsByClassName('item_bar_selected');
	for(var i in els)
	{
		els[i].className = 'item_bar_normal';
	}
	document.getElementById('item_bar_'+selected_item).className = 'item_bar_selected';
	var boxscrolltop = document.getElementById('inventorybox').scrollTop;
	if(((selected_item + 1) * item_bar_height) > (boxscrolltop + items_in_box * item_bar_height))
	{
		document.getElementById('inventorybox').scrollTop = (selected_item + 1) * item_bar_height - items_in_box * item_bar_height;
	}
}

function inventory_up(makeblink)
{
	if(selected_item <= 1)
	{
		return;
	}
	
	if(makeblink)
	{
		document.getElementById('inventory_arrow_up').style.backgroundColor = sbuttoncolor;
		iupclick++;
		setTimeout('if(iupclick == '+iupclick+'){document.getElementById("inventory_arrow_up").style.backgroundColor = "'+nbuttoncolor+'";}',200);
	}
	
	selected_item--;
	var els = document.getElementsByClassName('item_bar_selected');
	for(var i in els)
	{
		els[i].className = 'item_bar_normal';
	}
	document.getElementById('item_bar_'+selected_item).className = 'item_bar_selected';
	var boxscrolltop = document.getElementById('inventorybox').scrollTop;
	if((selected_item - 1) * item_bar_height <= (boxscrolltop + item_bar_height))
	{
		document.getElementById('inventorybox').scrollTop = (selected_item - 2) * item_bar_height;
	}
}

function select_inventory_item(in_line)
{
	if(in_line == selected_item)
	{
		use_item(in_inventory[selected_item][0],in_inventory[selected_item][1]);
	}
	else
	{
		var bdiff = in_line - selected_item;
		var dofunction = 'inventory_down';
		if(bdiff < 0)
		{
			bdiff = bdiff * -1;
			dofunction = 'inventory_up';
		}
		for(var a = 0; a < bdiff; a++)
		{
			eval(dofunction+'();');
		}
	}
}