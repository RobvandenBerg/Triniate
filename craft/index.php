<?php
include('../include_this.php');
light_login();
?>
<html>
<head>
<style>
.craftbox
{
	width: 15px;
	height: 15px;
	font-size: 0px;
	background-color: white;
}
.inventorybox
{
	width: 15px;
	height: 15px;
	font-size: 0px;
	background-color: white;
}
#output_amount
{
	font-size: 12px;
}
</style>
<script>
var selected_box = 0;
var output_array = 0;
var craftbox_array = new Array([[0,0],[0,0],[0,0]],[[0,0],[0,0],[0,0]],[[0,0],[0,0],[0,0]]);
var inventorybox_array = new Array(<?php
mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db);
$select_items_request = mysql_query("SELECT id,item_id from inventories where belongs_to='$player_id'") or die(mysql_error());
$backup_request = mysql_query("SELECT id,item_id from inventories where belongs_to='$player_id'") or die(mysql_error());
mysql_close();
for($a = 0; $a < 3; $a++)
{
	if($a != 0)
	{
		echo ',';
	}
	echo '[';
	for($i = 0; $i < 10; $i++)
	{
		if($i != 0)
		{
			echo ',';
		}
		$select_items_array = mysql_fetch_array($select_items_request);
		if($select_items_array)
		{
			$id = $select_items_array['id'];
			$item_id = $select_items_array['item_id'];
		}
		else
		{
			$id = 0;
			$item_id = 0;
		}
		echo '['.$id.','.$item_id.']';
	}
	echo ']';
}
?>);


var output = 0;

function clickoutput()
{
	if(selected_box == 0 && output != 0 && output[1] != 0)
	{
		var free_spot_ids = check_free_spots(output[1]);
		if(free_spot_ids == false)
		{
			alert('You don\'t have enough space in your inventory to keep all these items');
			return;
		}
		//alert('choo choo!');
		communicator2.open("POST","communicate.php?rand="+Math.random(),false);
		communicator2.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		communicator2.send(convert_craftbox_to_string()+'&get_output=1');
		var newitems = communicator2.responseText;
		// alert(newitems);
		var newitems_split = newitems.split(';');
		for(var b in newitems_split)
		{
			var citem = newitems_split[b];
			var split_citem = citem.split(',');
			var insert_array = new Array(split_citem[0],split_citem[1]);
			set_item_array(free_spot_ids[b],insert_array);
			document.getElementById(free_spot_ids[b]).style.backgroundImage = 'url("../images/items/'+split_citem[1]+'.png")';
		}
		output = 0;
		document.getElementById('output').style.backgroundImage = "url('')";
		document.getElementById('output_amount').innerHTML = '';
		for(var i = 0; i < 3; i++)
		{
			for(var j = 0; j < 3; j++)
			{
				set_item_array('craftbox_'+j+'_'+i,[0,0]);
				document.getElementById('craftbox_'+j+'_'+i).style.backgroundImage = "url('')";
			}
		}
		return;
	}
}

function clickbox(id)
{
	var idsplit = id.split('_');
	var type = idsplit[0];
	var col = idsplit[1];
	var row = idsplit[2];
	if(selected_box != 0)
	{
		var clicked_box_value = get_item_array(id);
		if(clicked_box_value[0] != 0 || clicked_box_value[1] != 0)
		{
			var selsrc = "url('../images/items/"+clicked_box_value[1]+".png')";
		}
		else
		{
			var selsrc = "url('')";
		}
		var selected_box_value = get_item_array(selected_box);
		if(selected_box_value[0] != 0 || selected_box_value[1] != 0)
		{
			var clicksrc = "url('../images/items/"+selected_box_value[1]+".png')";
		}
		else
		{
			var clicksrc = "url('')";
		}
		
		set_item_array(id,selected_box_value);
		set_item_array(selected_box,clicked_box_value);
		//alert(3);
		// alert('selsrc: '+selsrc+'. clicksrc: '+clicksrc);
		document.getElementById(selected_box).style.backgroundImage = selsrc;
		document.getElementById(id).style.backgroundImage = clicksrc;
		document.getElementById(selected_box).style.border = 'none;';
		document.getElementById(selected_box).style.border ="1px solid black";
		selected_box = 0;
		communicate();
	}
	else
	{
		selected_box = id;
		document.getElementById(selected_box).style.border ="1px solid red";
	}
}

function get_item_array(id)
{
	var idsplit = id.split('_');
	var type = idsplit[0];
	var col = idsplit[1];
	var row = idsplit[2];
	eval("var returnarray = "+type+"_array["+row+"]["+col+"];");
	return returnarray;
}

function set_item_array(id,newarray)
{
	var idsplit = id.split('_');
	var type = idsplit[0];
	var col = idsplit[1];
	var row = idsplit[2];
	eval(type+"_array["+row+"]["+col+"] = ["+newarray+"];");
}

function convert_craftbox_to_string()
{
	var return_string = '';
	for(var i = 0; i < 3; i++)
	{
		if(i != 0)
		{
			return_string += '&';
		}
		return_string += 'craftrow_'+i+'=';
		for(var a = 0; a < 3; a++)
		{
			if(a != 0)
			{
				return_string += ';';
			}
			return_string += craftbox_array[i][a][0] + ',' + craftbox_array[i][a][1];
		}
	}
	return return_string;
}

var communicator;
var communicator2;

if(window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
	communicator = new XMLHttpRequest();
	communicator2 = new XMLHttpRequest();
}
else
{// code for IE6, IE5
	communicator = new ActiveXObject("Microsoft.XMLHTTP");
	communicator2 = new ActiveXObject("Microsoft.XMLHTTP");
}

function communicate()
{
	// alert(convert_craftbox_to_string());
	communicator.onreadystatechange=function()
	{
		if (communicator.readyState==4 && communicator.status==200)
		{
			var responsetext = communicator.responseText;
			// alert('responsetext: '+responsetext);
			var responsesplit = responsetext.split(',');
			output = [responsesplit[0],responsesplit[1]];
			
			if(output[0] != 0)
			{
				document.getElementById('output').style.backgroundImage = "url('../images/items/"+output[0]+".png')";
				document.getElementById('output_amount').innerHTML = 'x' + output[1];
			}
			else
			{
				document.getElementById('output').style.backgroundImage = "url('')";
				document.getElementById('output_amount').innerHTML = '';
			}
		}
	}
	communicator.open("POST","communicate.php?rand="+Math.random(),true);
		
	communicator.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	communicator.send(convert_craftbox_to_string());
}

function check_free_spots(return_ids)
{
	var free_spots = 0;
	/*
	for(var i in craftbox_array)
	{
		for(var j in craftbox_array[i])
		{
			if(craftbox_array[i][j][0] == 0 && craftbox_array[i][j][0] == 0)
			{
				free_spots++;
			}
		}
	}
	*/
	var return_array = new Array();
	for(var i in inventorybox_array)
	{
		for(var j in inventorybox_array[i])
		{
			if(inventorybox_array[i][j][0] == 0 && inventorybox_array[i][j][0] == 0)
			{
				if(return_ids && free_spots < return_ids)
				{
					return_array[return_array.length] = 'inventorybox_'+j+'_'+i;
				}
				free_spots++;
			}
		}
	}
	
	if(return_ids)
	{
		if(free_spots >= return_ids)
		{
			return return_array;
		}
		return false;
	}
	return free_spots;
}
</script>
</head>
<body>
<table border='0' cellpadding='0' cellspacing='0'>
<tr><td class='craftbox' id='craftbox_0_0' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_1_0' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_2_0' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td>
<td colspan='2'></td>
</tr>
<tr><td class='craftbox' id='craftbox_0_1' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_1_1' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_2_1' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td>
<td style='width: 15px;'></td><td id='output' onClick='clickoutput();' class='craftbox' style='border: 1px solid black; background-image: url();'></td><td id='output_amount'></td>
</tr>
<tr><td class='craftbox' id='craftbox_0_2' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_1_2' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_2_2' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td>
<td colspan='2'></td></tr>
</table>
<br>
<table border='1' cellpadding='0' cellspacing='0'>
<?php
$select_items_request = $backup_request;
for($a = 0; $a < 3; $a++)
{
	echo '<tr>
	';
	for($i = 0; $i < 10; $i++)
	{
		$select_items_array = mysql_fetch_array($select_items_request);
		if($select_items_array)
		{
			$id = $select_items_array['id'];
			$item_id = $select_items_array['item_id'];
			$src = '\'../images/items/'.$item_id.'.png\'';
		}
		else
		{
			$id = 0;
			$item_id = 0;
			$src = '';
		}
		echo '<td class="inventorybox" id="inventorybox_'.$i.'_'.$a.'" onClick="clickbox(this.id);" style="border: 1px solid black; background-image: url('.$src.');"></td>
		';
	}
	echo '</tr>
	';
}
/*for($a = 0; $a < 3; $a++)
{
	echo '<tr>';
	for($b = 0; $b < 10; $b++)
	{
		echo '<td class="inventorybox" id="inventorybox_'.$b.'_'.$a.'" onClick="clickbox(this.id);" style="border: 1px solid black; background-image: url();"></td>';
	}
	echo '</tr>';
}*/
?>
</table>
<tr><td class='craftbox' id='craftbox_1_1' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_2_1' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_3_1' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td></tr>
<tr><td class='craftbox' id='craftbox_1_2' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_2_2' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_3_2' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td></tr>
<tr><td class='craftbox' id='craftbox_1_3' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_2_3' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td><td class='craftbox' id='craftbox_3_3' onClick='clickbox(this.id);' style='border: 1px solid black; background-image: url();'></td></tr>
</table>

<input type='button' value='Close' onClick="parent.close_craftbox();">
</body>
</html>