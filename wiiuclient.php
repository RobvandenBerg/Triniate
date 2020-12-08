<?php
include('include_this.php');

light_login('please_login.php');



$mapsize_height = 1000;
$mapsize_width = 1000;
mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());

	$my_settings_request = mysql_query("SELECT room, head, body, legs from position where id='$player_id'");
	$my_settings_row = mysql_fetch_row($my_settings_request);
	$room = $my_settings_row[0];
	$head = $my_settings_row[1];
	$body = $my_settings_row[2];
	$legs = $my_settings_row[3];
	if($head == 0 or $body == 0 or $legs == 0)
	{
		mysql_close();
		header("location: customize/build.php");
		exit("Create a character to play Triniate. <a href='customize/build.php'>Click here</a> to create a character");
	}

session_start();
$_SESSION['room'] = $room;



// -- GET ROOM SETTINGS CODE --
$myFile = 'rooms/' . $room . '.txt';
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);
$settingsexplode = explode(';',$theData);
$set1explode = explode(',',$settingsexplode[0]);
$mapsize_width = $set1explode[0];
$mapsize_height = $set1explode[1];
$background = $set1explode[2];
$wallsarray = $settingsexplode[1];
$pvp = 'no';
if($room == '35')
{
	$pvp = 'yes';
}
$explode_wallsarray = explode("','",$wallsarray);
$converted_wallsarray = '';
for($a = 0; $a < count($explode_wallsarray); $a++)
{
	$cwall = str_replace("'",'',$explode_wallsarray[$a]);
	$cwall = str_replace('teleport','\'teleport\'',$cwall);
	$cwall = str_replace('shop','\'shop\'',$cwall);
	$cwall = str_replace('use_function','\'use_function\'',$cwall);
	$cwall = str_replace('open_craftbox','\'open_craftbox\'',$cwall);
	if($a != 0)
	{
		$converted_wallsarray .= ',';
	}
	$converted_wallsarray .= '['.$cwall.']';
}

$standing_objects = array();

$sobjectsarray = str_replace("\n","",$settingsexplode[3]);
if(!empty($sobjectsarray))
{
	// echo "<script>alert(\"object in the room: $sobjectsarray\");</script>";
	$sobjsplit = explode(":",$sobjectsarray);
	for($y = 0; $y < count($sobjsplit); $y++)
	{
		$sobjsplit2 = explode(".",$sobjsplit[$y]);
		$sobjcoords = array_shift($sobjsplit2);
		$sobjbackground = implode('.', $sobjsplit2);
		$standing_objects[$y] = array($sobjcoords,$sobjbackground);
	}
}

// -- END GET ROOM SETTINGS CODE --


// echo "<script>alert('$room - $background');</script>";
// echo "<script>alert('You are in room $room');</script>";
$select_position_request = mysql_query("SELECT id,pos_left,pos_top,health,name,sprite,magic,max_magic,stamina from position where room='$room'") or die(mysql_error());
$numbah = mysql_num_rows($select_position_request);
for($m = 0; $m < $numbah; $m++)
{
	$select_position_array = mysql_fetch_array($select_position_request);
	/*$select_position_number = mysql_num_rows($select_position_request);
	echo "numb: $select_position_number<br>";*/
	$play_id[$m] = $select_position_array['id'];
	$pos_left[$m] = $select_position_array['pos_left'];
	$pos_top[$m] = $select_position_array['pos_top'];
	if($play_id[$m] == $player_id)
	{
		$my_top_pos = $pos_top[$m];
		$my_left_pos = $pos_left[$m];
		$my_sprite = $select_position_array['sprite'];
		if(!is_numeric($my_top_pos))
		{
			$my_top_pos = '0';
		}
		if(!is_numeric($my_left_pos))
		{
			$my_left_pos = '0';
		}
		$viewport_top = $pos_top[$m];
		$viewport_left = $pos_left[$m];
		$health = $select_position_array['health'];
		$player_name = $select_position_array['name'];
		$magic = $select_position_array['magic'];
		$max_magic = $select_position_array['max_magic'];
		$stamina = $select_position_array['stamina'];
	}
}
// echo $position;
mysql_close();




$objects = array();

// OBJECTS SCRIPT
if(file_exists('npcs/rooms/'.$room.'.php'))
{
	include('npcs/rooms/'.$room.'.php');
}
// END OF OBJECTS SCRIPT



?>
<html>
<http-equiv="pragma" content="NO-CACHE">
<meta name='viewport' content='width=640, height=300, user-scalable=no'>
<head>
<title>
Wii U client
</title>
<script>
// -- OBJECTS SCRIPT
var objects = new Array;
</script>
<script>
<?php
for($m = 0; $m < count($objects); $m++)
{
	$object = $objects[$m];
	echo "objects[$m] = new Array(\"$object[0]\",\"$object[1]\",\"$object[2]\",\"$object[3]\",\"$object[4]\",\"$object[5]\",\"$object[6]\");\n";
}
?>
// alert(objects);
// -- OBJECTS SCRIPT END

function start_npc_action(array)
{
	var arrayid = npc_action_arrays.length
	npc_action_arrays[arrayid] = array;
	npc_action(arrayid,0);
}


function npc_action(arrayid,step)
{
	var total_in_array = npc_action_arrays[arrayid];
	var total_steps = total_in_array.length;
	if(step < total_steps)
	{
		var inarray = total_in_array[step];
		var npcid = inarray[0];
		if(npcid == 0)
		{
			var npchandle = document.getElementById('player_'+player_id);
		}
		else
		{
			var npchandle = document.getElementById('object_'+npcid);
		}

		var npcaction = inarray[1];
		switch(npcaction)
		{
			case 'walk':
			var to_leftcoord = inarray[2];
			var to_topcoord = inarray[3];

			if(inarray[5] && inarray[5] == 'relative')
			{
				to_leftcoord = to_leftcoord + parseInt(npchandle.style.left);
				to_topcoord = to_topcoord + parseInt(npchandle.style.top);
			}
			var sprite = inarray[4];
			npc_walk(arrayid,to_leftcoord,to_topcoord,npcid,step);
			break;

			case 'changesprite':
			var sprite = inarray[2];
			change_sprite(npchandle.id + '_sprite',sprite.src);
			step++;
			npc_action(arrayid,step);
			break;
			
			case 'wait':
			var wait_time = inarray[2];
			step++;
			setTimeout("npc_action("+arrayid+","+step+");",wait_time);
			break;

			case 'blockmoving':
			stop_movement();
			npc_blocks_moving = true;
			step++;
			npc_action(arrayid,step);
			break;

			case 'unblockmoving':
			npc_blocks_moving = false;
			step++;
			npc_action(arrayid,step);
			break;

			case 'warp':
			var method = inarray[2];
			var lcoord = inarray[3];
			var tcoord = inarray[4];

			if(method == 'coords')
			{
				npchandle.style.left = lcoord + 'px';
				npchandle.style.top = tcoord + 'px';
				npchandle.style.zIndex = tcoord + charheight;
			}
			else if(method == 'from_player')
			{
				var from = inarray[3];
				var plcoord = parseInt(document.getElementById('player_'+player_id).style.left);
				var ptcoord = parseInt(document.getElementById('player_'+player_id).style.top);
				
				var nlcoord = plcoord + lcoord;
				var ntcoord = ptcoord + tcoord;
				npchandle.style.left = nlcoord + 'px';
				npchandle.style.top = ntcoord + 'px';
				npchandle.style.zIndex = ntcoord + charheight;
				
			}
			step++;
			npc_action(arrayid,step);
			break;

			case 'touser':
			var to_leftcoord = parseInt(document.getElementById('player_'+player_id).style.left);
			var to_topcoord = parseInt(document.getElementById('player_'+player_id).style.top);
			var curr_leftcoord = parseInt(npchandle.style.left);
			var curr_topcoord = parseInt(npchandle.style.top);
			
			var charid = inarray[2];

			var approach_set = false;

			if(inarray[3] && (inarray[3] == 'right' || inarray[3] == 'left' || inarray[3] == 'top' || inarray[3] == 'bottom'))
			{
				approach_set = true;
				var approach = inarray[3];
				switch(approach)
				{
					case 'left':
					to_leftcoord = to_leftcoord - 15;
					break;
					
					case 'right':
					to_leftcoord = to_leftcoord + 15;
					break;

					case 'top':
					to_topcoord = to_topcoord - 15;
					break;

					case 'bottom':
					to_topcoord = to_topcoord + 15;
					break;
					
				}
			}

			var topdiff = to_topcoord - curr_topcoord;
			if(topdiff < 0)
			{
				var testtopdiff = topdiff * -1;
			}
			else
			{
				var testtopdiff = topdiff;
			}

			var leftdiff = to_leftcoord - curr_leftcoord;
			if(leftdiff < 0)
			{
				var testleftdiff = leftdiff * -1;
			}
			else
			{
				var testleftdiff = leftdiff;
			}

			if(testtopdiff > leftdiff)
			{
				if(topdiff < 0)
				{
					var sprite = sprite_move_up[charid];
					if(!approach_set)
					{
						to_topcoord = to_topcoord + 15;
					}
				}
				else
				{
					var sprite = sprite_move_down[charid];
					if(!approach_set)
					{
						to_topcoord = to_topcoord - 15;
					}
				}
			}
			else
			{
				if(leftdiff < 0)
				{
					var sprite = sprite_move_left[charid];
					if(!approach_set)
					{
						to_leftcoord = to_leftcoord + 15;
					}
				}
				else
				{
					var sprite = sprite_move_right[charid];
					if(!approach_set)
					{
						to_leftcoord = to_leftcoord - 15;
					}
				}
			}

			npc_action_arrays[arrayid][step][4] = sprite;

			npc_walk(arrayid,to_leftcoord,to_topcoord,npcid,step);
			break;

			case 'conversation':
			var conversation = inarray[2];
			dialogue_feedback = true;
			step++;
			dialogue_feedback_array = new Array(arrayid,step);
			converttodialogue(conversation);
			break;
			
			case 'redirect':
			var rurl = inarray[2];
			redirecting_page = true;
			window.location = rurl;
			break;
		}
	}

}

var npc_blocks_moving = false;

function npc_walk(arrayid,to_leftcoord,to_topcoord,npcid,step)
{
	var npchandle = document.getElementById('object_'+npcid);
	if(npcid == 0)
	{
		npchandle = document.getElementById('player_'+player_id);
	}
	var curr_leftcoord = parseInt(npchandle.style.left);
	var curr_topcoord = parseInt(npchandle.style.top);

	var sprite = npc_action_arrays[arrayid][step][4];
	// alert('sprite: ' + npc_action_arrays[arrayid][step][1]);
	change_sprite(npchandle.id + '_sprite',sprite.src);


	if(curr_leftcoord < to_leftcoord - 3)
	{
		var new_leftcoord = curr_leftcoord + 3;
	}
	else if(curr_leftcoord > to_leftcoord + 3)
	{
		var new_leftcoord = curr_leftcoord - 3;
	}
	else
	{
		var new_leftcoord = to_leftcoord;
	}
	
	if(curr_topcoord < to_topcoord - 3)
	{
		var new_topcoord = curr_topcoord + 3;
	}
	else if(curr_topcoord > to_topcoord + 3)
	{
		var new_topcoord = curr_topcoord - 3;
	}
	else
	{
		var new_topcoord = to_topcoord;
	}
	
	npchandle.style.top = new_topcoord + 'px';
	npchandle.style.left = new_leftcoord + 'px';
	npchandle.style.zIndex = new_topcoord + charheight;

	if(new_leftcoord != to_leftcoord || new_topcoord != to_topcoord)
	{
		setTimeout("npc_walk("+arrayid+","+to_leftcoord+","+to_topcoord+","+npcid+","+step+");",100);
	}
	else
	{
		step++;
		npc_action(arrayid,step);
	}
}



var redirecting_page = false;
var walkspeed = 3;
// var wallsarray = new Array('381,0,410,363','382,378,407,445','407,402,500,445');
var wallsarray = new Array(<?php echo $converted_wallsarray;?>);
// wallsarray[wallsarray.length] = [0,0,100,100,'use_function','open_craftbox'];
function check_movable(coordtop,coordleft)
{
	var coordbottom = coordtop + charheight;
	coordtop = coordbottom - 2;
	// var coordmiddle = coordleft + Math.floor(charwidth / 2);
	var coordright = coordleft + charwidth;
	var no_collision = true;
	for(var a in wallsarray)
	{
		var wallsplit = wallsarray[a];
		var wallleft = wallsplit[0];
		var walltop = wallsplit[1];
		var wallright = wallsplit[2];
		var wallbottom = wallsplit[3];
		if(coordleft <= wallright && coordright >= wallleft && coordtop <= wallbottom && coordbottom >= walltop)
		{
			if(wallsplit[4] && wallsplit[4] == 'teleport')
			{
				stop_movement();
				if(window.confirm('Walk to next area?'))
				{
					redirecting_page = true;
					window.location = 'switchrooms.php?newroom=' + wallsplit[5] + '&&inoption=' + wallsplit[6];
				}
			}
			else if(wallsplit[4] && wallsplit[4] == 'use_function' && wallsplit[5])
			{
				eval(wallsplit[5] + '();');
			}
			return false;
		}
	}
	if(no_collision)
	{
		for(var a in extrablocks)
		{
			var wallsplit = extrablocks[a];
			var wallleft = wallsplit[0];
			var walltop = wallsplit[1];
			var wallright = wallsplit[2];
			var wallbottom = wallsplit[3];
			if(coordleft <= wallright && coordright >= wallleft && coordtop <= wallbottom && coordbottom >= walltop)
			{
					return false;
			}
		}
	}
	return true;
}
// alert(check_movable(50,101));


var player_id = '<?php echo $player_id;?>';
var player_name = "<?php echo $player_name;?>";
var my_character = player_id;
var others_character = 1;
var villain_character = 2;



	var sprite_up = new Array();
	var sprite_down = new Array();
	var sprite_left = new Array();
	var sprite_right = new Array();
	
	var sprite_move_up = new Array();
	var sprite_move_down = new Array();
	var sprite_move_left = new Array();
	var sprite_move_right = new Array();
	
	var sprite_attack_up = new Array();
	var sprite_attack_down = new Array();
	var sprite_attack_left = new Array();
	var sprite_attack_right = new Array();
	
	var sprite_attack_up_mana = new Array();
	var sprite_attack_down_mana = new Array();
	var sprite_attack_left_mana = new Array();
	var sprite_attack_right_mana = new Array();
	
	var sprite_hurt_up = new Array();
	var sprite_hurt_down = new Array();
	var sprite_hurt_left = new Array();
	var sprite_hurt_right = new Array();

	var sprite_dead = new Array();
	

// ------------------
	for(var i in objects)
	{
		load_sprite(objects[i][3],objects[i][1]);
	}

	load_sprite(player_id);
	load_sprite(2,'Goblin');
	load_sprite(3,'Caveman');
	load_sprite(4,'Madeye');
	











function load_sprite(id,name)
{
	var beforeid = 'characters/';
	if(!name)
	{
		name = id;
		beforeid = 'customize/saved/';
	}
	else if(parseInt(name) == name)
	{
		// The sprite is from a NPC
		name = 'npc_'+name;
		beforeid = 'npcs/sprites/';
	}
	var addrand = '?r='+Math.random();
    sprite_up[name] = new Image();
    sprite_up[name].src = beforeid+id+"/stand_up.gif"+addrand;
    sprite_down[name] = new Image();
    sprite_down[name].src = beforeid+id+"/stand_down.gif"+addrand;
    sprite_left[name] = new Image();
    sprite_left[name].src = beforeid+id+"/stand_left.gif"+addrand;
    sprite_right[name] = new Image();
    sprite_right[name].src = beforeid+id+"/stand_right.gif"+addrand;
    
    	sprite_move_up[name] = new Image();
    sprite_move_up[name].src = beforeid+id+"/walk_up.gif"+addrand;
    sprite_move_down[name] = new Image();
    sprite_move_down[name].src = beforeid+id+"/walk_down.gif"+addrand;
    sprite_move_left[name] = new Image();
    sprite_move_left[name].src = beforeid+id+"/walk_left.gif"+addrand;
    sprite_move_right[name] = new Image();
    sprite_move_right[name].src = beforeid+id+"/walk_right.gif"+addrand;
    
    	sprite_attack_up[name] = new Image();
    sprite_attack_up[name].src = beforeid+id+"/attack_up.gif"+addrand;
    sprite_attack_down[name] = new Image();
    sprite_attack_down[name].src = beforeid+id+"/attack_down.gif"+addrand;
    sprite_attack_left[name] = new Image();
    sprite_attack_left[name].src = beforeid+id+"/attack_left.gif"+addrand;
    sprite_attack_right[name] = new Image();
    sprite_attack_right[name].src = beforeid+id+"/attack_right.gif"+addrand;
	
	sprite_attack_up_mana[name] = sprite_attack_up[name];
	sprite_attack_down_mana[name] = sprite_attack_down[name];
	sprite_attack_left_mana[name] = sprite_attack_left[name];
	sprite_attack_right_mana[name] = sprite_attack_right[name];
    
	sprite_hurt_up[name] = new Image();
    sprite_hurt_up[name].src = beforeid+id+"/hurt_up.gif"+addrand;
    sprite_hurt_down[name] = new Image();
    sprite_hurt_down[name].src = beforeid+id+"/hurt_down.gif"+addrand;
    sprite_hurt_left[name] = new Image();
    sprite_hurt_left[name].src = beforeid+id+"/hurt_right.gif"+addrand;
    sprite_hurt_right[name] = new Image();
    sprite_hurt_right[name].src = beforeid+id+"/hurt_left.gif"+addrand;


	sprite_dead[name] = new Image();
    sprite_dead[name].src = beforeid+id+"/dead.gif";

}


function unload_sprite(id)
{


// alert('unload sprite van id '+id+'. sprite up is nu: '+sprite_up[id].src);
	sprite_up.splice(id,1);
	sprite_down.splice(id,1);
	sprite_left.splice(id,1);
	sprite_right.splice(id,1);

	sprite_move_up.splice(id,1);
	sprite_move_down.splice(id,1);
	sprite_move_left.splice(id,1);
	sprite_move_right.splice(id,1);

	sprite_attack_up.splice(id,1);
	sprite_attack_down.splice(id,1);
	sprite_attack_left.splice(id,1);
	sprite_attack_right.splice(id,1);

	sprite_hurt_up.splice(id,1);
	sprite_hurt_down.splice(id,1);
	sprite_hurt_left.splice(id,1);
	sprite_hurt_right.splice(id,1);


	sprite_dead.splice(id,1);
}


function get_villain_id(villain_name)
{
	var output = 2;
	var villain_name_array = villain_name.split(' ');
	villain_name = villain_name_array[0];
	switch(villain_name)
	{
		case 'Goblin':
		output = 2;
		break;
	
		case 'Caveman':
		output = 3;
		break;
		
		case 'Madeye':
		output = 4;
		break;
	}

	return output;
}

var charheights = new Array();
charheights['Goblin'] = 30;
charheights['Caveman'] = 30;
charheights['Madeye'] = 20;
var charwidths = new Array();
charwidths['Goblin'] = 20;
charwidths['Caveman'] = 20;
charwidths['Madeye'] = 20;

var charheight = 30;
var charwidth = 20;
var currsprite;
var moveable = true;
var currentsprite = '<?php echo $my_sprite;?>';
var stop_moving = false;
var movement = false;
var leftclick = true;
var upclick = true;
var downclick = true;
var rightclick = true;
var aclick = true;
var bclick = false;
var mapsize_width = <?php echo $mapsize_width;?>;
var mapsize_height = <?php echo $mapsize_height;?>;
var maxscroll_left = mapsize_width - 320;
// var maxscroll_top = mapsize_height - 106;
var maxscroll_top = mapsize_height - 155;
var maxmove_down = mapsize_height - charheight - 2;
var maxmove_right = mapsize_width - 15;

document.onkeydown = function(event){
     var holder;
     //IE uses this
     if(window.event){
            holder=window.event.keyCode;
     }
     //FF uses this
     else{
            holder=event.which;
     }
     KeyDownCheck(holder);
}

/*function keyz(holder){
	alert(holder);
     if(holder == 13){
            alert('13!');
     }
}*/



// document.onkeydown = KeyDownCheck;
/*document.onkeydown = function(e)
{
	if(window.event){
	            KeyDownCheck(0);
	     }
	     //FF uses this
	     else{
	     	alert(e.KeyCode);
	           KeyDownCheck(e);
	     } 
}*/
// document.onkeydown = KeyDownCheck(e);
document.onkeyup = stop_movement;  


function use_item(id,item_id,notoggle,in_line)
{
	switch(item_id)
	{	
		case 1:
  		var itemconfirm = confirm('Are you sure you want to eat your apple?');
		if(itemconfirm)
		{
			// --- start add extra script ---
				
			var extras_count = send_extras.length;
			send_extras[extras_count] = 'use_' + id;

			// --- End add extra script ---
			document.getElementById('inventory_box').innerHTML = 'Eating apple...';
			setTimeout("get_inventory();",2000);
		}
  		break;

		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
		case 10:
		if(confirm('Are you sure you want to use your potion?'))
		{
			// --- start add extra script ---
				
			var extras_count = send_extras.length;
			send_extras[extras_count] = 'use_' + id;

			// --- End add extra script ---
			document.getElementById('inventory_box').innerHTML = 'Drinking potion...';
			setTimeout("get_inventory();",2000);
		}
		break;
		
		case 17:
		use_tool('axe',1,id);
		close_screen("inventory",notoggle);
		break;
		case 18:
		use_tool('axe',2,id);
		close_screen("inventory",notoggle);
		break;
		case 19:
		use_tool('axe',3,id);
		close_screen("inventory",notoggle);
		break;
		case 20:
		use_tool('axe',4,id);
		close_screen("inventory",notoggle);
		break;
		case 21:
		use_tool('axe',5,id);
		close_screen("inventory",notoggle);
		break;
		
		case 22:
		use_tool('pickaxe',1,id);
		close_screen("inventory",notoggle);
		break;
		
		case 23:
		use_tool('pickaxe',2,id);
		close_screen("inventory",notoggle);
		break;
		
		case 24:
		use_tool('pickaxe',3,id);
		close_screen("inventory",notoggle);
		break;
		
		case 25:
		use_tool('pickaxe',4,id);
		close_screen("inventory",notoggle);
		break;
		
		case 26:
		use_tool('pickaxe',5,id);
		close_screen("inventory",notoggle);
		break;

		default:
		//toggle_screen("craftbox");
		alert('You cannot use this item');
		break;
	}
	// alert('done');
}

function open_craftbox()
{
	if(confirm('Do you want to craft?'))
	{
		open_iframe('craft','craftbox');
	}
}

function close_craftbox()
{
	close_iframe();
}

function open_iframe(opensrc,setclass)
{
	crafting = true;
	show_screen("iframebox");
	document.getElementById("iframebox").className = setclass;
	document.getElementById("iframebox").src = opensrc + '?rand=' + Math.random();
}

function close_iframe()
{
	setTimeout('close_screen("iframebox");',20);
	document.getElementById("iframebox").src = '';
	crafting = false;
}

function check_existance(cplayer)
{
	var productElement = document.getElementById(cplayer);
	if (productElement != null)
	{
		// alert('existance');
		return true;
	}
	else
	{
		// alert('no existance');
		return false;
	}
}

var dead = false;


function KeyDownCheck(KeyID)
{
	if(typing && KeyID == 13)
	{
		sendpost();
		return false;
	}
	if(typing) { return false; }
	if(KeyID == 8)
	{
		// alert('woooot');
	}
	if(dead == true || typing == true || npc_blocks_moving == true)
	{
		return;
	}
	if(inventory_open)
	{
		switch(KeyID)
		{
			case 13: case 32: select_inventory_item(selected_item); break;
			case 38: inventory_up(1); break;
			case 40: inventory_down(1); break;
		}
	}
	if(windows_open == 1)
	{
		if(document.getElementById('iframebox').src != '')
		{
			document.getElementById('iframebox').contentWindow.KeyDownCheck(KeyID);
		}
	}
	if(moveable == false && do_choose == true)
	{
		switch(KeyID)
		{
			case 13:
			case 32:
			multipile_choice('enter');
			break;

			case 37:
			multipile_choice('prev');
			break;

			case 39:
			multipile_choice('next');
			break;
		}
	}
	if(moveable == true && mining == false && windows_open == 0)
	{
		switch(KeyID)
		{
			case 13:
			case 32:
case 83:
			if(aclick==true)
			{
				aclick = false; setTimeout("aclick=true;",200); stop_movement(); attack();
			}
			break;
			case 37:
			case 65:
			if(!direction)
			{
				wcount++; direction=1;  move_player(wcount);
			}
			break;
			case 38:
			case 87:
			if(!direction)
			{
				wcount++;  direction=2;  move_player(wcount);
			}
			break;
			case 39:
case 68:

			if(movement == false && rightclick==true)
			{
				if(!direction)
				{
					wcount++; direction=3;  move_player(wcount);
				}
			}
			break;
			case 40:
			if(!direction)
			{
				wcount++;  direction=4;  move_player(wcount);
			}
			break;
			
			
			<?php
			/*
			if(detect_system() == 'pc')
			{
			?>
			case 32:
			// B-button simulation
			if(aclick == true)
			{
				aclick = false;
				setTimeout("aclick=true;",200);
				alert('simulate hit');
				attack(true);
			}
			break;
			<?php
			}
			*/
			?>
		}
	}
	else
	{
		if((KeyID == 13 || KeyID == 32) && in_dialogue == true)
		{
			start_dialogue(next_dialogue);
		}
	}
}

function KeyUpCheck()
{
	if(window.event){
	            var KeyID =event.keyCode;}
}

var fitness = <?php echo $stamina;?>;

function changewalkspeed()
{
	if(walkspeed == 3)
	{
		if(fitness > 0)
		{
			walkspeed = 5;
			document.getElementById('fitness_span').innerHTML = 'Running';
		}
	}
	else if(walkspeed == 5)
	{
		walkspeed = 3;
		document.getElementById('fitness_span').innerHTML = 'Walking';
	}
	// walkspeed=parseInt(window.prompt('What is the walkspeed?'));
}

function drain_fitness()
{
	if(walkspeed == 5)
	{
		if(fitness > 0)
		{
			fitness--;
			fitness--;
		}
		if(fitness > 0)
		{
			fitness--;
			fitness--;
		}
		else if(fitness == 0)
		{
			changewalkspeed();
		}
	}
	else if(walkspeed == 3)
	{
		if(fitness < 100)
		{
			fitness++;
			fitness++;
		}
	}
	document.getElementById('fitness_bar').style.width = fitness + '%';
	setTimeout("drain_fitness();",500);
}
setTimeout("drain_fitness();",3000);

var direction = 0;
var f_direction = 4;

function stop_movement()
{
	if(npc_blocks_moving)
	{
		return;
	}
	stop_moving = true;
	movement = false;
	if(moveable == true)
	{
		if(direction==2)
		{
			change_sprite('player_' + player_id + '_sprite',sprite_up[my_character].src);
			currentsprite = 'sprite_up';
		}
		if(direction==4)
		{
			change_sprite('player_' + player_id + '_sprite',sprite_down[my_character].src);
			currentsprite = 'sprite_down';
		}
		if(direction==1)
		{
			change_sprite('player_' + player_id + '_sprite',sprite_left[my_character].src);
			currentsprite = 'sprite_left';
		}
		if(direction==3)
		{
			change_sprite('player_' + player_id + '_sprite',sprite_right[my_character].src);
			currentsprite = 'sprite_right';
		}
	}
	direction = 0;
}

var wcount = 0;

function move_player(gwcount)
{
	if(connect_problems || npc_blocks_moving || direction == false || dead || gwcount != wcount)
	{
		return;
	}
	f_direction = direction;
	wcount++;
			if(direction == 2)
			{
				var goupperm = check_movable((parseInt(document.getElementById('player_' + player_id).style.top) - walkspeed),parseInt(document.getElementById('player_' + player_id).style.left))
				// alert((parseInt(document.getElementById('player_' + player_id).style.top) + 2) + ',' + parseInt(document.getElementById('player_' + player_id).style.left));
				// alert(goupperm);
				if(goupperm)
				{
					var get_position = parseInt(document.getElementById('player_' + player_id).style.top);
					var new_position = get_position - walkspeed;
					
					
					currentsprite = 'sprite_move_up';
					change_sprite('player_' + player_id + '_sprite',sprite_move_up[my_character].src);
					if(get_position < maxscroll_top)
					{
						document.getElementById('maincontain').scrollTop = document.getElementById('maincontain').scrollTop - walkspeed;
					}
					if(new_position > 0)
					{
						document.getElementById('player_' + player_id).style.top = new_position + 'px';
						document.getElementById('player_' + player_id).style.zIndex = new_position + charheight;
						setTimeout("move_player("+wcount+");",100);
					}
				}
			}
			if(direction == 4)
			{
				var godownperm = check_movable((parseInt(document.getElementById('player_' + player_id).style.top) + walkspeed),parseInt(document.getElementById('player_' + player_id).style.left))
				if(godownperm)
				{
					currentsprite = 'sprite_move_down';
					change_sprite('player_' + player_id + '_sprite',sprite_move_down[my_character].src);
					var get_position = parseInt(document.getElementById('player_' + player_id).style.top);
					// if(get_position > 106)
					if(get_position > 125)
					{
						document.getElementById('maincontain').scrollTop = (document.getElementById('maincontain').scrollTop + walkspeed);
					}
					var new_position = (get_position + walkspeed);
					if(new_position < maxmove_down)
					{
						document.getElementById('player_' + player_id).style.top = new_position + 'px';
						document.getElementById('player_' + player_id).style.zIndex = new_position + charheight;
						setTimeout("move_player("+wcount+");",100);
					}
				}
			}
			if(direction == 1)
			{
				var goleftperm = check_movable((parseInt(document.getElementById('player_' + player_id).style.top)),parseInt(document.getElementById('player_' + player_id).style.left) - walkspeed)
				if(goleftperm)
				{
					currentsprite = 'sprite_move_left';
					change_sprite('player_' + player_id + '_sprite',sprite_move_left[my_character].src);
					var get_position = parseInt(document.getElementById('player_' + player_id).style.left);
					if(get_position < maxscroll_left)
					{
						document.getElementById('maincontain').scrollLeft = document.getElementById('maincontain').scrollLeft - walkspeed;
					}
					var new_position = get_position - walkspeed;
					if(new_position > 0)
					{
						document.getElementById('player_' + player_id).style.left = new_position + 'px';
						setTimeout("move_player("+wcount+");",100);
					}
				}
			}
			if(direction == 3)
			{
				var gorightperm = check_movable((parseInt(document.getElementById('player_' + player_id).style.top)),parseInt(document.getElementById('player_' + player_id).style.left) + walkspeed)
				if(gorightperm)
				{
				
					currentsprite = 'sprite_move_right';
					change_sprite('player_' + player_id + '_sprite',sprite_move_right[my_character].src);
					var get_position = parseInt(document.getElementById('player_' + player_id).style.left);
					if(get_position > 310)
					{
						document.getElementById('maincontain').scrollLeft = (document.getElementById('maincontain').scrollLeft + walkspeed);
					}
					var new_position = (get_position + walkspeed);
					if(new_position < maxmove_right)
					{
						document.getElementById('player_' + player_id).style.left = new_position + 'px';
						setTimeout("move_player("+wcount+");",100);
					}
				}
			}
			// alert(player_id + 'cplayer top: '+document.getElementById('player_'+player_id).style.zIndex)
}

var attacking = '';
var attack_left;
var attack_right;
var attack_top;
var attack_bottom;

function attack(b_button)
{
	stop_movement();
	moveable = false;
	var mana = false;
	if(b_button)
	{
		mana = true;
		if(magic_value < 20)
		{
			return;
		}
		magic_value = magic_value - 20;
	}
	// alert(currentsprite);
	if(f_direction == 2)
	{
		var hitdirec = 'up';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + 7;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 5;
		attack_bottom = attack_top + charwidth - 5;
	}
	if(f_direction == 4)
	{
		var hitdirec = 'down';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + 7;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + 5;
	}
	if(f_direction == 1)
	{
		var hitdirec = 'left';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) - 5;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + charheight - 30;
	}
	if(f_direction == 3)
	{
		var hitdirec = 'right';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + charwidth;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + charheight - 30;
	}
	
	
	var doattack = true;
	var contloop = true;
	// -- OBJECTS CHECK SCRIPT

	var arr = new Array();
	arr = document.getElementsByName('object'); 
	for(var a in objects)
	{
		var cobject = objects[a];
		var cobjtype = cobject[0];
		var cobjid = cobject[1];
		var cobjname = cobject[2];
		var cobjsprite = cobject[3];
		var cobjcoords = cobject[4];
		var cobjcoordssplit = cobjcoords.split(',');
		var cobjleft = cobjcoordssplit[0];
		var cobjtop = cobjcoordssplit[1];
		var cobjdimensions = cobject[5];
		var cobjdimensionssplit = cobjdimensions.split(',');
		var cobjwidth = cobjdimensionssplit[0];
		var cobjheight = cobjdimensionssplit[1];
		var cobjproperty1 = cobject[6];

		save_for_after_dialogue = a;
		
		if(contloop == true && !b_button)
		{
			// Check the player attemps to talk to a NPC
			var obj = document.getElementById('object_'+cobjid);
			var obj_top = parseInt(obj.style.top);
			var obj_bottom = obj_top + charheight;
			var obj_left = parseInt(obj.style.left);
			var obj_right = obj_left + charwidth;
		
			if((attack_left > obj_left && attack_left < obj_right) || (attack_right > obj_left && attack_right < obj_right))
			{

				if((attack_top > obj_top && attack_top < obj_bottom) || (attack_bottom > obj_top && attack_bottom < obj_bottom))
				{
					// alert('How dare you hit a NPC...?');
					doattack = false;
					movable = true;
					contloop = false;
					if(cobjtype == 'npc')
					{
						converttodialogue(cobjproperty1);
						if(f_direction == 2)
						{
							change_sprite(obj.id + '_sprite',sprite_down['npc_'+cobjid].src);
						}
						else if(f_direction == 4)
						{
							change_sprite(obj.id + '_sprite',sprite_up['npc_'+cobjid].src);
						}
						else if(f_direction == 1)
						{
							change_sprite(obj.id + '_sprite',sprite_right['npc_'+cobjid].src);
						}
						else if(f_diretion == 3)
						{
							change_sprite(obj.id + '_sprite',sprite_left['npc_'+cobjid].src);
						}
					}
				}
			}
		}
	}
	if(contloop == true)
	{
		var arr = new Array();
		arr = document.getElementsByName('lying_item'); 
		for(var i = 0; i < arr.length; i++)
		{
			var obj = document.getElementsByName('lying_item').item(i);

			var item_top = parseInt(obj.style.top);
			var item_bottom = item_top + 15;
			var item_left = parseInt(obj.style.left);
			var item_right = item_left + 15;
	
			if((item_left > item_left && attack_left < item_right) || (attack_right > item_left && attack_right < item_right))
			{
				//alert('Hit 1...');
				if((attack_top > item_top && attack_top < item_bottom) || (attack_bottom > item_top && attack_bottom < item_bottom))
				{
					// alert('Hit item ' + obj.id + '!');
					// --- start add extra script ---
					var extras_count = send_extras.length;
					send_extras[extras_count] = 'pick_' + obj.id;
					// ---- end add extra script
					doattack = false;
					movable = true;
					moveable = true;
					contloop = false;
				}
			}
		}
	}
	// -- END OF OBJECTS CHECK SCRIPT
	if(doattack == true)
	{
		// alert('doattack');
		if(f_direction == 2)
		{
			// alert(currentsprite);
			if(mana)
			{
				currentsprite = 'sprite_attack_up_mana';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_up_mana[my_character].src);
			}
			else
			{
				currentsprite = 'sprite_attack_up';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_up[my_character].src);
			}
			setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_up[my_character].src); currentsprite = 'sprite_up'; moveable = true;}", 1000);
		}
		if(f_direction == 4)
		{
			if(mana)
			{
				currentsprite = 'sprite_attack_down_mana';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_down_mana[my_character].src);
			}
			else
			{
				currentsprite = 'sprite_attack_down';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_down[my_character].src);
			}
			setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_down[my_character].src); currentsprite = 'sprite_down'; moveable = true;}", 1000);
		}
		if(f_direction == 1)
		{
			if(mana)
			{
				currentsprite = 'sprite_attack_left_mana';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_left_mana[my_character].src);
			}
			else
			{
				currentsprite = 'sprite_attack_left';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_left[my_character].src);
			}
			setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_left[my_character].src); currentsprite = 'sprite_left'; moveable = true;}", 1000);
		}
		if(f_direction == 3)
		{
			if(mana)
			{
				currentsprite = 'sprite_attack_right_mana';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_right_mana[my_character].src);
			}
			else
			{
				currentsprite = 'sprite_attack_right';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_right[my_character].src);
			}
			setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_right[my_character].src); currentsprite = 'sprite_right'; moveable = true;}", 1000);
		}
		if(b_button)
		{
			check_attack(hitdirec,true);
		}
		else
		{
			check_attack(hitdirec);
		}
	}
}

function check_attack(hitdirec,b_button)
{

	var display_attack_bottom = attack_bottom;
var display_attack_left = attack_left;
var display_attack_right = attack_right;
var display_attack_top = attack_top;
	if(b_button)
	{
		var extras_count = send_extras.length;
		send_extras[extras_count] = 'mana';
		if(hitdirec == 'left')
		{
			attack_left = attack_left - 100;
		}
		if(hitdirec == 'right')
		{
			attack_right = attack_right + 100;
		}
		if(hitdirec == 'up')
		{
			attack_top = attack_top - 100;
		}
		if(hitdirec == 'down')
		{
			attack_bottom = attack_bottom + 100;
		}
		display_attack_left = attack_left;
		display_attack_top = attack_top;
		display_attack_right = attack_right;
		display_attack_bottom = attack_bottom;
		if(hitdirec == 'right' || hitdirec == 'left')
		{
			display_attack_bottom = display_attack_bottom + 5;
		}
	}
	else
	{
		display_attack_right = attack_left + 5;
		display_attack_bottom = attack_top + 5;
	}

	var newdiv = document.createElement('div');
	  var divIdName = 'attack_' + player_id;
	  newdiv.setAttribute('id',divIdName);
	  newdiv.setAttribute('style', 'position: absolute; left: '+display_attack_left+'; top: '+display_attack_top+'; width: '+(display_attack_right - display_attack_left)+'px; height: '+(display_attack_bottom - display_attack_top)+'px; background-image: url("images/hit_ind.png"); z-index: '+document.getElementById('player_'+player_id).style.zIndex+';');
	  document.getElementById('maincontain').appendChild(newdiv);
	setTimeout("var elObj = document.getElementById('"+divIdName+"'); elObj.parentNode.removeChild(elObj);",500);

	
	
	
	var arr = new Array();
	arr = document.getElementsByName('villain');
	for(var i in villains)
	{
		var obj = document.getElementById('villain_'+villains[i][0]);
		var villain_left = villains[i][1];
		var villain_top = villains[i][2];
		var villain_right = villain_left + villains[i][5];
		var villain_bottom = villain_top + villains[i][6];
		
		if(attack_left <= villain_right && attack_right >= villain_left && attack_top <= villain_bottom && attack_bottom >= villain_top)
		{
			// alert('Hit villain ' + obj.id + '!');
			// --- start add extra script ---
					
			var extras_count = send_extras.length;
			if(b_button)
			{
				send_extras[extras_count] = 'attack_' + obj.id + '_' + hitdirec + '_villain' + '_mana';
			}
			else
			{
					send_extras[extras_count] = 'attack_' + obj.id + '_' + hitdirec + '_villain';
			}
				// ---- end add extra script
		}
	}
	
	
}

var other_attack_top;
var other_attack_left;
var other_attack_bottom;
var other_attack_right;

<?php
if($pvp == 'yes')
{
	echo "var pvp = true;";
}
else
{
	echo "var pvp = false;";
}
?>

function opponent_attack(hitdirec,opponent_id,mana,is_player)
{

	var attack_top = other_attack_top;
	var attack_bottom = other_attack_bottom;
	var attack_left = other_attack_left;
	var attack_right = other_attack_right;
	var display_attack_top = attack_top;
	var display_attack_bottom = attack_bottom;
	var display_attack_left = attack_left;
	var display_attack_right = attack_right;
	
	if(mana)
	{
		if(hitdirec == 'left')
		{
			attack_left = attack_left - 100;
			display_attack_left = attack_left;
		}
		if(hitdirec == 'right')
		{
			attack_right = attack_right + 100;
			display_attack_right = attack_right;
		}
		if(hitdirec == 'up')
		{
			attack_top = attack_top - 100;
			display_attack_top = attack_top;
		}
		if(hitdirec == 'down')
		{
			attack_bottom = attack_bottom + 100;
			display_attack_bottom = attack_bottom;
		}
	}
	var zindex = display_attack_top;
	if(hitdirec == 'left' || hitdirec == 'right')
	{
		display_attack_bottom = attack_bottom + 5;
		var zindex = display_attack_bottom;
	}
	if(!mana)
	{
		if(hitdirec == 'up')
		{
			//alert(display_attack_left + ',' +  display_attack_top + ',' +  display_attack_right + ',' +  display_attack_bottom);
			//alert(display_attack_right-display_attack_left);
			display_attack_top = attack_top;
			display_attack_bottom = attack_bottom - 10;
			var zindex = display_attack_bottom;
		}
	}
	
	var newdiv = document.createElement('div');
	  var divIdName = 'attack_' + opponent_id;
	  newdiv.setAttribute('id',divIdName);
	  // newdiv.setAttribute("class", "tab_iframe");
	  newdiv.setAttribute('style', 'position: absolute; left: '+display_attack_left+'; top: '+display_attack_top+'; width: '+(display_attack_right-display_attack_left)+'px; height: '+(display_attack_bottom-display_attack_top)+'px; background-image: url("images/hit_ind.png"); z-index: '+(document.getElementById('player_'+player_id).style.zIndex + 1)+';');
	  // newdiv.setAttribute('src', 'newtab.php');
	  // newdiv.innerHTML = 'Element Number '+divIdName+' has been added! <a href=\'#\' onclick=\'removeElement('+divIdName+')\'>Remove the div "'+divIdName+'"</a>';
	  document.getElementById('maincontain').appendChild(newdiv);
	setTimeout("var elObj = document.getElementById('"+divIdName+"'); elObj.parentNode.removeChild(elObj);",500);


	
if(dead == false)
{	
// alert(attack_left + ',' + attack_top + ',' + attack_right + ',' + attack_bottom);
		var obj = document.getElementById('player_'+player_id);
		// obj.style.left;
		var villain_top = parseInt(obj.style.top);
		var villain_bottom = villain_top + charheight;
		var villain_left = parseInt(obj.style.left);
		var villain_right = villain_left + charwidth;
		//alert('Top: ' + villain_top + '. Left: ' + villain_left +  '. Villain right: ' + villain_right + '. Attack left: '  + attack_left + '. Attack right: ' + attack_right);

		//if((attack_left > villain_left && attack_left < villain_right) || (attack_right > villain_left && attack_right < villain_right))
		if(attack_left <= villain_right && attack_right >= villain_left)
		{
			// alert('hit 1');
			//if((attack_top > villain_top && attack_top < villain_bottom) || (attack_bottom > villain_top && attack_bottom < villain_bottom))
			// alert(attack_top +' <= ' + villain_bottom + ' && ' + attack_bottom + ' >= ' + villain_top);
			if(attack_top <= villain_bottom && attack_bottom >= villain_top)
			{
				// document.getElementById('health_' + obj.id).style.width = parseInt(document.getElementById('health_' + obj.id).style.width) - 5 + '%';
				// --- start add extra script ---
				
				var extras_count = send_extras.length;
				// alert(extras_count + ': ');
				if(is_player == false || (is_player == true && pvp == true))
				{
					if(is_player == true)
					{
						send_extras[extras_count] = 'hit_' + opponent_id + '_' + hitdirec;
					}
					else
					{
						send_extras[extras_count] = 'hit_' + opponent_id + '_' + hitdirec + '_villain';
					}
					// ---- end add extra script
					// alert('he gotcha');
					stop_movement();
					moveable = false;
					if(hitdirec == 'up')
					{
						f_direction = 4;
						currentsprite = 'sprite_hurt_up';
						change_sprite('player_' + player_id + '_sprite',sprite_hurt_up[my_character].src);
						document.getElementById('player_' + player_id).style.top = parseInt(document.getElementById('player_' + player_id).style.top) - 2 + 'px';
						setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_down[my_character].src); currentsprite = 'sprite_down'; moveable = true;}", 1000);
					}
					if(hitdirec == 'down')
					{
						f_direction = 2;
						currentsprite = 'sprite_hurt_down';
						change_sprite('player_' + player_id + '_sprite',sprite_hurt_down[my_character].src);
						document.getElementById('player_' + player_id).style.top = parseInt(document.getElementById('player_' + player_id).style.top) + 2 + 'px';
						setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_up[my_character].src); currentsprite = 'sprite_up'; moveable = true;}", 1000);
					}
					if(hitdirec == 'left')
					{
						f_direction = 3;
						currentsprite = 'sprite_hurt_left';
						change_sprite('player_' + player_id + '_sprite',sprite_hurt_left[my_character].src);
						document.getElementById('player_' + player_id).style.left = parseInt(document.getElementById('player_' + player_id).style.left) - 2 + 'px';
						setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_right[my_character].src); currentsprite = 'sprite_right'; moveable = true;}", 1000);
					}
					if(hitdirec == 'right')
					{
						f_direction = 1;
						currentsprite = 'sprite_hurt_right';
						change_sprite('player_' + player_id + '_sprite',sprite_hurt_right[my_character].src);
						document.getElementById('player_' + player_id).style.left = parseInt(document.getElementById('player_' + player_id).style.left) + 2 + 'px';
						setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_left[my_character].src); currentsprite = 'sprite_left'; moveable = true;}", 1000);
					}
					// alert('he got ya');
					document.getElementById('health_player_' + player_id).style.width = parseInt(document.getElementById('health_player_' + player_id).style.width) - 5 + '%';
					
				}
			}
		}
	

}

}







var responseraw;
var cplayer;
var updaterequest;
var characterrequest;
var inventoryrequest;
var msgsrequest;
var postrequest;
var requeststats;
var traderequest;
var questrequest;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
   updaterequest=new XMLHttpRequest();
   characterrequest=new XMLHttpRequest();
   inventoryrequest=new XMLHttpRequest();
   msgsrequest = new XMLHttpRequest();
   postrequest=new XMLHttpRequest();
   requeststats=new XMLHttpRequest();
   traderequest=new XMLHttpRequest();
   questrequest=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  updaterequest=new ActiveXObject("Microsoft.XMLHTTP");
  characterrequest=new ActiveXObject("Microsoft.XMLHTTP");
  inventoryrequest=new ActiveXObject("Microsoft.XMLHTTP");
  msgsrequest=new ActiveXObject("Microsoft.XMLHTTP");
  postrequest=new ActiveXObject("Microsoft.XMLHTTP");
  requeststats=new ActiveXObject("Microsoft.XMLHTTP");
  traderequest=new ActiveXObject("Microsoft.XMLHTTP");
  questrequest=new ActiveXObject("Microsoft.XMLHTTP");
  }

var check_existance_var;
var smooth_started = new Array();
var currenttop = new Array();
var currentleft = new Array();
var newtop = new Array();
var newleft = new Array();


function microtime (get_as_float) {
    // Returns either a string or a float containing the current time in seconds and microseconds  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/microtime    // +   original by: Paulo Freitas
    // *     example 1: timeStamp = microtime(true);
    // *     results 1: timeStamp > 1000000000 && timeStamp < 2000000000
    var now = new Date().getTime() / 1000;
    var s = parseInt(now, 10); 
    return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
}

var parse_sprites = new Array();
// [object_id,stage,coordleft,coordtop,toleft,totop,type]

function render_movement()
{
	// drain_fitness();
	var parse_sprites_length = parse_sprites.length;
	for(var i = 0; i < parse_sprites_length; i++)
	{
		var current_sprite_array = parse_sprites[i];
		if(current_sprite_array)
		{
		var object_id = current_sprite_array[0];
		var stage = current_sprite_array[1];
		var current_left = current_sprite_array[2];
		var current_top = current_sprite_array[3];
		var to_left = current_sprite_array[4];
		var to_top = current_sprite_array[5];
		var object_height = charheight;
		if(current_sprite_array[7])
		{
			object_height = current_sprite_array[7];
		}
		// alert(arrayset);
		
		if(stage == 1 && Math.abs(to_top - current_top) < 3 && Math.abs(to_left - current_left < 3))
		{
			var new_top = parseInt(to_top);
			var new_left = parseInt(to_left);
		}
		else
		{
			var new_left = get_new_coordinate(current_left,to_left,stage);
			var new_top = get_new_coordinate(current_top,to_top,stage);
		}
		document.getElementById(object_id).style.left = new_left + 'px';
		document.getElementById(object_id).style.top = new_top + 'px';
		document.getElementById(object_id).style.zIndex = new_top + object_height;
		
		if(stage < 4)
		{
			parse_sprites[i][1] = (stage + 1);
			parse_sprites[i][2] = new_left;
			parse_sprites[i][3] = new_top;
		}
		else
		{
			parse_sprites.splice(i,1);
		}
		if(current_sprite_array[6])
		{
			var arrayset = current_sprite_array[6];
			eval(arrayset+"[1] = "+new_left+"; "+arrayset+"[2] = "+new_top+";");
		}
		}
	}
	var wait_time = 70 + parse_sprites_length * 30;
	setTimeout("render_movement();",wait_time);
}

setTimeout("render_movement();",3000);

function get_new_coordinate(currentcoord,tocoord,stage)
{
	currentcoord = currentcoord;
	tocoord = tocoord;
	stage = stage;
	var oldcoord = Math.floor(currentcoord - (stage - 1)/4 * tocoord)/(1-(stage-1)/4);
	var newcoord = Math.ceil(stage/4 * (tocoord - oldcoord) + oldcoord);
	// stage/4 * (tocoord - currentcoord) + currentcoord
	return newcoord;
}



var currmsg;
var checkmsg;
var delmsg;
var checktoppos;
var checkleftpos;
var checktopdiff;
var checkleftdiff;
var topdirec;
var leftdirec;
var equipped_item = new Array(0,0);
// inv id, item_id

// ------------------------------------
var last_sprite = new Array();
var players = new Array();
var villains = new Array();
var items = new Array();
var specialobjects = new Array();
var extrablocks = new Array();
var lasttraderequest = 0;
var magic_value = <?php echo $magic;?>;
// var currplayers = new Array();
// ------------------------------------

function handle_info(responseraw)
{
			responseraw = responseraw.split(';')
			var currplayers = new Array();
			var currvillains = new Array();
			var curritems = new Array();
			var currspecialobjects = new Array();
			parse_sprites = new Array();
			for(var i in responseraw)
			{
				responsetext = responseraw[i].split(',');
				if(responsetext[7] == 'traderequest')
				{
					if(responsetext[0] != lasttraderequest)
					{
						// alert('You got a trade request. id: ' +responsetext[0]);
						lasttraderequest = responsetext[0];

						get_trade_request(responsetext[0],responsetext[2]);
						/*if(confirm('Accept trade request from '+responsetext[2]+'?'))
						{
							accept_trade(responsetext[0]);
						}
						else
						{
							alert('Okay :okay:');
						}*/
					}
				}
				else if(responsetext[7] == 'player')
				{
					var checksprite = responsetext[3];
					if(responsetext[0] == player_id)
					{
						cplayer = 'player_' + responsetext[0];
						var eq_item = responsetext[8];
						var eq_item_id = responsetext[9];
						var eq_item_percentage = responsetext[10];
						var magic_percentage = responsetext[11];
						magic_value = responsetext[12];
						var reset_left = responsetext[13];
						var reset_top = responsetext[14];
						if(reset_left != 0)
						{
							alert('Force reset');
							document.getElementById(cplayer).style.left = reset_left + 'px';
							document.getElementById(cplayer).style.top = reset_top + 'px';
							document.getElementById('maincontain').scrollTop = reset_top - 100;
							document.getElementById('maincontain').scrollLeft = reset_left - 155;
							stop_movement();
						}
						if(responsetext[15])
						{
							fitness = responsetext[15];
						}
						document.getElementById('magic_bar').style.width = magic_percentage + '%';
						document.getElementById('my_magic').innerHTML = magic_percentage + '%';
						//alert('eq_item: '+eq_item);
						if(eq_item != equipped_item[0] || eq_item_id != equipped_item[1] || eq_item_percentage != equipped_item[2])
						{
							// alert('item not the same');
							set_equipped_item(eq_item,eq_item_id,eq_item_percentage);
						}
						if(checksprite == 'sprite_dead')
						{
							if(dead == false)
							{
								dead = true;
								if(checksprite == 'sprite_dead' && last_sprite[responsetext[0]] != 'sprite_dead')
								{
									change_sprite(cplayer + '_sprite',sprite_dead[my_character].src);
								}
								setTimeout("redirecting_page = true; window.location = 'die.php';",2000);
								
							}
						}
						document.getElementById('health_player_' + responsetext[0]).style.width = responsetext[6] + '%';
						document.getElementById('my_health').innerHTML = responsetext[6] + '%';
					}
					if(responsetext[0] != player_id)
					{
						cplayer = 'player_' + responsetext[0];
						var cplayerid = responsetext[0];
						
						var 
						//currplayers[responsetext[0]] = 'a';
						//,objstage,objleft,objtop,objwidth,objheight,minetime,minetool,tool_level
						
						check_existance_var = check_existance(cplayer);
						if(check_existance_var)
						{
							// alert('exists');
							currplayers[responsetext[0]] = ['a',players[responsetext[0]][1],players[responsetext[0]][2],responsetext[1],responsetext[2]];
						}
						else
						{
							load_sprite(cplayerid);
							document.getElementById('mainscreen').innerHTML = document.getElementById('mainscreen').innerHTML + "<div oNclick='request_stats("+cplayerid+");' id='"+cplayer+"' style='position: absolute; top: "+responsetext[2]+"px; left: "+responsetext[1]+"px; width: 20px; height: 30px; z-index: "+responsetext[1]+"; visibility: visible;'><img id='player_"+responsetext[0]+"_sprite' style='position: absolute; top: 0px; left: 0px; width: 20px; height: 30px;' src=''><div class='player_name'><div id='health_player_"+responsetext[0]+"' class='health_bar' style='width: "+responsetext[6]+"%;'>&nbsp;</div>"+responsetext[5]+"</div><div class='chattextcontain'><div class='chattext' style='visibility: hidden;' id='chattext_"+responsetext[0]+"'>&nbsp;</div></div></div>";


							eval("change_sprite(cplayer + '_sprite',"+responsetext[3]+"[cplayerid].src);");
							// alert('A new player entered the room');
							// players[responsetext[0]] = 'a';
							currplayers[responsetext[0]] = ['a',responsetext[1],responsetext[2],responsetext[1],responsetext[2]];
							players[responsetext[0]] = currplayers[responsetext[0]];
						}
						// alert(cplayer);
						document.getElementById('health_player_'+responsetext[0]).style.width = responsetext[6] + '%';

							    
							    
						// ---------------------------------------------------------------------
							    
						// checktoppos = parseInt(document.getElementById(cplayer).style.top);
						// checkleftpos = parseInt(document.getElementById(cplayer).style.left);
						checkleftpos = players[cplayerid][1];
						checktoppos = players[cplayerid][2];
						checktopdiff = checktoppos - responsetext[2];
						checkleftdiff = checkleftpos - responsetext[1];
						if(checktopdiff != 0)
						{
							if(checktopdiff > 0)
							{
								topdirec = 'up';
							}
							if(checktopdiff < 0)
							{
							    	topdirec = 'down';
							    	checktopdiff = checktopdiff * -1;
							}
						}
						else
						{
							topdirec = 'still';
						}
						if(checkleftdiff != 0)
						{
							if(checkleftdiff > 0)
							{
							    	leftdirec = 'left';
							}
							if(checkleftdiff < 0)
							{
							    	leftdirec = 'right';
							    	checkleftdiff = checkleftdiff * -1;
							}
						}
						else
						{
							leftdirec = 'still';
						}
						
						currsprite = responsetext[3];
						
						if(leftdirec == 'still' && topdirec == 'still')
						{
							if(currsprite == 'sprite_move_up')
							{
								currsprite = 'sprite_up';
							}
							if(currsprite == 'sprite_move_down')
							{
								currsprite = 'sprite_down';
							}
							if(currsprite == 'sprite_move_left')
							{
								currsprite = 'sprite_left';
							}
							if(currsprite == 'sprite_move_right')
							{
								currsprite = 'sprite_right';
							}
						}
							      
							    
							    
						
						if(currsprite == 'sprite_up' || currsprite == 'sprite_down' || currsprite == 'sprite_left' || currsprite == 'sprite_right')
						{
							if(topdirec != 'still')
							{
								if(leftdirec != 'still')
								{
								    	if(checkleftdiff > checktopdiff)
								    	{
								    		if(leftdirec == 'left')
								    		{
									    		currsprite = 'sprite_move_left';
								    		}
								    		if(leftdirec == 'right')
								    		{
								    			currsprite = 'sprite_move_right';
								    		}
								    	}
								    	else
								    	{
								    		if(topdirec == 'up')
								    		{
								    			currsprite = 'sprite_move_up';
								    		}
								    		if(topdirec == 'down')
								    		{
								    			currsprite = 'sprite_move_down';
								    		}
								    	}
								}
								else
								{
								    	if(topdirec == 'up')
								    	{
								    		currsprite = 'sprite_move_up';
								    	}
								    	if(topdirec == 'down')
								    	{
										currsprite = 'sprite_move_down';
									}
								}
							}
							else
							{
								if(checkleftdiff != 'still')
								{
								    	if(leftdirec == 'left')
								    	{
								    		currsprite = 'sprite_move_left';
								    	}
								    	if(leftdirec == 'right')
								    	{
								    		currsprite = 'sprite_move_right';
									}
								}
							}
						}
							    
						// -----------------------------------------------------------------
	parse_sprites[parse_sprites.length] = [cplayer,1,players[cplayerid][1],players[cplayerid][2],responsetext[1],responsetext[2],'players['+cplayerid+']'];
						currmsg = responsetext[4];
						// alert(currmsg);
						checkmsg = document.getElementById('chattext_' + responsetext[0]).innerHTML;
						// alert(currmsg);
						if(currmsg != '' && currmsg != 'nomsg' && currmsg != checkmsg)
						{
							document.getElementById('chattext_' + responsetext[0]).innerHTML = currmsg;
							document.getElementById('chattext_' + responsetext[0]).style.visibility = 'visible';
							setTimeout("document.getElementById('chattext_"+responsetext[0]+"').style.visibility = 'hidden';",10000);
						}
							    
						// alert(last_sprite[responsetext[0]]);

						if(currsprite == 'sprite_attack_up' && last_sprite[responsetext[0]] != 'sprite_attack_up')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = players[cplayerid][1] + 7;
							other_attack_right = other_attack_left + 5;
							// other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 5;
							other_attack_top = players[cplayerid][2] + 5;
							other_attack_bottom = other_attack_top + charwidth - 5;
							opponent_attack('up',responsetext[0],false,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_up[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_down' && last_sprite[responsetext[0]] != 'sprite_attack_down')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = players[cplayerid][1] + 7;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + 5;
							opponent_attack('down',responsetext[0],false,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_down[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_left' && last_sprite[responsetext[0]] != 'sprite_attack_left')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) - 5;
							other_attack_left = players[cplayerid][1] - 5;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + charheight - 30;
							opponent_attack('left',responsetext[0],false,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_left[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_right' && last_sprite[responsetext[0]] != 'sprite_attack_right')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + charwidth;
							other_attack_left = players[cplayerid][1] + charwidth;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + charheight - 30;
							opponent_attack('right',responsetext[0],false,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_right[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_up_mana' && last_sprite[responsetext[0]] != 'sprite_attack_up_mana')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = players[cplayerid][1] + 7;
							other_attack_right = other_attack_left + 5;
							// other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 5;
							other_attack_top = players[cplayerid][2] + 5;
							other_attack_bottom = other_attack_top + charwidth - 5;
							opponent_attack('up',responsetext[0],true,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_up_mana[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_down_mana' && last_sprite[responsetext[0]] != 'sprite_attack_down_mana')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = players[cplayerid][1] + 7;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + 5;
							opponent_attack('down',responsetext[0],true,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_down_mana[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_left_mana' && last_sprite[responsetext[0]] != 'sprite_attack_left_mana')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) - 5;
							other_attack_left = players[cplayerid][1] - 5;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + charheight - 30;
							opponent_attack('left',responsetext[0],true,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_left_mana[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_right_mana' && last_sprite[responsetext[0]] != 'sprite_attack_right_mana')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + charwidth;
							other_attack_left = players[cplayerid][1] + charwidth;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + charheight - 30;
							opponent_attack('right',responsetext[0],true,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_right_mana[cplayerid].src);
						}

						
						if(currsprite != 'sprite_attack_up' && currsprite != 'sprite_attack_down' && currsprite!='sprite_attack_left' && currsprite!='sprite_attack_right' && currsprite != 'sprite_attack_up_mana' && currsprite != 'sprite_attack_down_mana' && currsprite!='sprite_attack_left_mana' && currsprite!='sprite_attack_right_mana')
						{
							eval("if(currsprite == '"+currsprite+"' && last_sprite[responsetext[0]] != '"+currsprite+"'){change_sprite(cplayer + '_sprite',"+currsprite+"[cplayerid].src);}");
						}
						loadit = true;
						last_sprite[responsetext[0]] = currsprite;
					    	// alert('testa: ' + i);
					}
				}
				else if(responsetext[7] == 'villain')
				{
					//if(responsetext[0] != player_id)
					//{
responsetext[0] = 'v' + responsetext[0];
						cplayer = 'villain_' + responsetext[0];
						var cplayerid = responsetext[0];
						// currvillains[responsetext[0]] = 'a';
						
						var cname = responsetext[5];
						var cnamesplit = cname.split(' ');
						var cname = cnamesplit[0];
						// villain_character = get_villain_id(cname);
						villain_character = cname;
						var villain_height = charheights[villain_character];
						var villain_width = charwidths[villain_character];
						
						
						check_existance_var = check_existance(cplayer);
						if(check_existance_var)
						{
							// alert('exists');
							currvillains[responsetext[0]] = [responsetext[0],villains[responsetext[0]][1],villains[responsetext[0]][2],responsetext[1],responsetext[2],villain_width,villain_height,villain_character];
						}
						else
						{
							document.getElementById('mainscreen').innerHTML = document.getElementById('mainscreen').innerHTML + "<div name='villain' id='"+cplayer+"' style='position: absolute; top: "+responsetext[2]+"px; left: "+responsetext[1]+"px; width: "+villain_width+"px; height: "+villain_height+"px; z-index: "+responsetext[1]+"; visibility: visible;'><img id='"+cplayer+"_sprite' style='position: absolute; top: 0px; left: 0px; width: "+villain_width+"px; height: "+villain_height+"px;' src=''><div class='player_name'><div id='health_villain_"+responsetext[0]+"' class='health_bar' style='width: "+responsetext[6]+"%;'>&nbsp;</div>"+responsetext[5]+"</div><div class='chattext' style='visibility: hidden;' id='chattext_"+responsetext[0]+"'>&nbsp;</div></div>";
							// alert('A new villain entered the room');
							// players[responsetext[0]] = 'a';
							currvillains[responsetext[0]] = [responsetext[0],responsetext[1],responsetext[2],responsetext[1],responsetext[2],villain_width,villain_height,villain_character];
							villains[responsetext[0]] = currvillains[responsetext[0]];
						}
						// alert(cplayer);
						document.getElementById('health_villain_'+responsetext[0]).style.width = responsetext[6] + '%';
							    
							    
						// ---------------------------------------------------------------------
							    
						// checktoppos = parseInt(document.getElementById(cplayer).style.top);
						// checkleftpos = parseInt(document.getElementById(cplayer).style.left);
						checkleftpos = villains[cplayerid][1];
						checktoppos = villains[cplayerid][2];
						checktopdiff = checktoppos - responsetext[2];
						checkleftdiff = checkleftpos - responsetext[1];
						if(checktopdiff != 0)
						{
							if(checktopdiff > 0)
							{
								topdirec = 'up';
							}
							if(checktopdiff < 0)
							{
							    	topdirec = 'down';
							    	checktopdiff = checktopdiff * -1;
							}
						}
						else
						{
							topdirec = 'still';
						}
						if(checkleftdiff != 0)
						{
							if(checkleftdiff > 0)
							{
							    	leftdirec = 'left';
							}
							if(checkleftdiff < 0)
							{
							    	leftdirec = 'right';
							    	checkleftdiff = checkleftdiff * -1;
							}
						}
						else
						{
							leftdirec = 'still';
						}
						
						currsprite = responsetext[3];
						
						
						if(leftdirec == 'still' && topdirec == 'still')
						{
							if(currsprite == 'sprite_move_up')
							{
								currsprite = 'sprite_up';
							}
							if(currsprite == 'sprite_move_down')
							{
								currsprite = 'sprite_down';
							}
							if(currsprite == 'sprite_move_left')
							{
								currsprite = 'sprite_left';
							}
							if(currsprite == 'sprite_move_right')
							{
								currsprite = 'sprite_right';
							}
						}
							      
							    
							    
						
						if(currsprite == 'sprite_up' || currsprite == 'sprite_down' || currsprite == 'sprite_left' || currsprite == 'sprite_right')
						{
							if(topdirec != 'still')
							{
								if(leftdirec != 'still')
								{
								    	if(checkleftdiff > checktopdiff)
								    	{
								    		if(leftdirec == 'left')
								    		{
									    		currsprite = 'sprite_move_left';
								    		}
								    		if(leftdirec == 'right')
								    		{
								    			currsprite = 'sprite_move_right';
								    		}
								    	}
								    	else
								    	{
								    		if(topdirec == 'up')
								    		{
								    			currsprite = 'sprite_move_up';
								    		}
								    		if(topdirec == 'down')
								    		{
								    			currsprite = 'sprite_move_down';
								    		}
								    	}
								}
								else
								{
								    	if(topdirec == 'up')
								    	{
								    		currsprite = 'sprite_move_up';
								    	}
								    	if(topdirec == 'down')
								    	{
										currsprite = 'sprite_move_down';
									}
								}
							}
							else
							{
								if(checkleftdiff != 'still')
								{
								    	if(leftdirec == 'left')
								    	{
								    		currsprite = 'sprite_move_left';
								    	}
								    	if(leftdirec == 'right')
								    	{
								    		currsprite = 'sprite_move_right';
									}
								}
							}
						}
						
						
						
						
						
						
						
						// ------------------------------------------------------------------
							    
						parse_sprites[parse_sprites.length] = [cplayer,1,villains[cplayerid][1],villains[cplayerid][2],responsetext[1],responsetext[2],"villains['"+cplayerid+"']",villains[cplayerid][6]];

						
							    
						// alert(last_sprite[responsetext[0]]);
							    
						
 
						// -------------------
							    
							    
						if(currsprite == 'sprite_attack_up' && last_sprite[responsetext[0]] != 'sprite_attack_up')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = villains[cplayerid][1] + Math.floor(villains[cplayerid][5]/2);
							other_attack_right = other_attack_left + 5;
							// other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 5;
							other_attack_top = villains[cplayerid][2] + 10;
							other_attack_bottom = other_attack_top + 5;
							opponent_attack('up',responsetext[0],false,false);
							
							change_sprite(cplayer + '_sprite',sprite_attack_up[villain_character].src);
						}
						if(currsprite == 'sprite_attack_down' && last_sprite[responsetext[0]] != 'sprite_attack_down')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = villains[cplayerid][1] + Math.floor(villains[cplayerid][5]/2);
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = villains[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + 5;
							opponent_attack('down',responsetext[0],false,false);
							
							change_sprite(cplayer + '_sprite',sprite_attack_down[villain_character].src);
						}
						if(currsprite == 'sprite_attack_left' && last_sprite[responsetext[0]] != 'sprite_attack_left')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) - 5;
							other_attack_left = villains[cplayerid][1] - 5;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = villains[cplayerid][2] + villains[cplayerid][6] - 15;
							other_attack_bottom = other_attack_top;
							opponent_attack('left',responsetext[0],false,false);
							
							change_sprite(cplayer + '_sprite',sprite_attack_left[villain_character].src);
						}
						if(currsprite == 'sprite_attack_right' && last_sprite[responsetext[0]] != 'sprite_attack_right')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + charwidth;
							other_attack_left = villains[cplayerid][1] + villains[cplayerid][5];
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = villains[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top;
							opponent_attack('right',responsetext[0],false,false);
							
							change_sprite(cplayer + '_sprite',sprite_attack_right[villain_character].src);
						}



						if(currsprite != 'sprite_attack_up' && currsprite != 'sprite_attack_down' && currsprite!='sprite_attack_left' && currsprite!='sprite_attack_right')
						{
							eval("if(currsprite == '"+currsprite+"' && last_sprite[responsetext[0]] != '"+currsprite+"'){change_sprite(cplayer + '_sprite',"+currsprite+"[villain_character].src);}");
						}

						loadit = true;
						last_sprite[responsetext[0]] = currsprite;
					    	// alert('testa: ' + i);
					//}
				}
				else if(responsetext[7] == 'item')
				{
					curritems[responsetext[3]] = 'a';


					// alert('item discovered!');
					
					

					cplayer = 'item_' + responsetext[3];
					var cplayerid = responsetext[3];
					check_existance_var = check_existance(cplayer);
					if(check_existance_var)
					{
						// alert('exists');
					}
					else
					{
						
						document.getElementById('mainscreen').innerHTML = document.getElementById('mainscreen').innerHTML + "<div name='lying_item' id='"+cplayer+"' style=\"position: absolute; top: "+responsetext[1]+"px; left: "+responsetext[2]+"px; width: 15px; height: 15px; background-image: url('images/items/"+responsetext[0]+".png'); z-index: "+(parseInt(responsetext[1]) + 15)+"; visibility: visible;\">&nbsp;</div>";
						// alert('A new item entered the room');
					}


				}
				else if(responsetext[7] == 'special_object')
				{
					// alert('special object test');
					cplayer = 'special_object_' + responsetext[0];
					var cplayerid = responsetext[3];
					var objobjid = responsetext[3];
					var objleft = responsetext[1];
					var objtop = responsetext[2];
					var objwidth = responsetext[4];
					var objheight = responsetext[5];
					var objstage = responsetext[6];
					var minetool = responsetext[8];
					var tool_level = responsetext[9];
					var minetime = responsetext[10];
					var objzindex = responsetext[11];
					
					
					if(!extrablocks[responsetext[0]])
					{
						if(responsetext[12] && responsetext[13] && responsetext[14] && responsetext[15])
						{
							// alert('Extra block found!');
							extrablocks[responsetext[0]] = [responsetext[12],responsetext[13],responsetext[14],responsetext[15]];
						}
					}
					
					
					var objbottom = parseInt(objtop) + parseInt(objheight);
		
					
					currspecialobjects[responsetext[0]] = [responsetext[0],objstage,objleft,objtop,objwidth,objheight,minetime,minetool,tool_level,objzindex];
					
					
					check_existance_var = check_existance(cplayer);
					if(check_existance_var)
					{
						if(specialobjects[responsetext[0]][1] != currspecialobjects[responsetext[0]][1])
						{
							// CHANGE STAGE IMG HERE
							// alert('change img');
							specialobjects[responsetext[0]][1] = currspecialobjects[responsetext[0]][1];
							document.getElementById(cplayer).style.backgroundImage = "url('images/special_objects/"+objobjid+"/"+objstage+".png')";
						}
					}
					else
					{
						specialobjects[responsetext[0]] = currspecialobjects[responsetext[0]];
						document.getElementById('mainscreen').innerHTML = document.getElementById('mainscreen').innerHTML + "<div name='lying_item' id='"+cplayer+"' style=\"position: absolute; top: "+objtop+"px; left: "+objleft+"px; width: "+objwidth+"px; height: "+objheight+"px; background-image: url('images/special_objects/"+objobjid+"/"+objstage+".png'); z-index: "+(parseInt(objbottom) + parseInt(objzindex)) +"; visibility: visible;\">&nbsp;</div>";
						// alert('A new special object entered the room');
					}


				}
				// alert('testb: ' + i);
			}
			// leave game test
			for(var s in players)
			{
				var currplaying = false;
				if(!currplayers[s])
				{
					var elObj = document.getElementById('player_'+s);
            		elObj.parentNode.removeChild(elObj);
					// unload_sprite(s);
				}
			}
			players = currplayers;




// villains leave game test
			for(var s in villains)
			{
				// alert(s);
				var currplaying = false;
				if(!currvillains[s])
				{
					var elObj = document.getElementById('villain_'+s);
            		elObj.parentNode.removeChild(elObj);
				}
			}
			villains = currvillains;


// items leave game test
			for(var s in items)
			{
				var currplaying = false;
				if(items[s] == curritems[s])
				{
					currplaying = true;
				}
				if(currplaying == false)
				{
					var elObj = document.getElementById('item_'+s);
            		elObj.parentNode.removeChild(elObj);
				}
			}
			items = curritems;
			
			for(var s in specialobjects)
			{
				var currplaying = false;
				if(currspecialobjects[s])
				{
					currplaying = true;
				}
				if(currplaying == false)
				{
					var elObj = document.getElementById('special_object_'+s);
            		elObj.parentNode.removeChild(elObj);
					if(extrablocks[s])
					{
						extrablocks.splice(s,1,[]);
					}
				}
			}
			specialobjects = currspecialobjects;
}

var send_extras = new Array();

var firstspeedtest = 0;
var secondspeedtest = 0;
var coords = '<?php echo $my_left_pos . "," . $my_top_pos;?>';
var count_number = 0;
var connect_problems = false;
var code503 = false;
function update_position()
{
	if(redirecting_page) { return; }
	count_number++;
	var fetch_messages_string = '';
	var do_fetch_messages = false;
	if(count_number % 30 == 0 || count_number == 1)
	{
		fetch_messages_string = '&fetch_messages=yes';
		do_fetch_messages = true;
	}
	var coords = parseInt(document.getElementById('player_' + player_id).style.left) + ',' + parseInt(document.getElementById('player_' + player_id).style.top);
	
	// -- SEND EXTRAS SCRIPT START --
	var extras_count = send_extras.length;
	var send_extra_string = '';
	if(extras_count > 0)
	{
		for (w in send_extras) { 
		   send_extra_string = send_extra_string + '&&extra_' + w+'='+send_extras[w];
		}
		send_extras = new Array();
	}
	else
	{
		var send_extra_string = '';
	}
	// -- SEND EXTRAS SCRIPT END --
	
	updaterequest.onreadystatechange=function()
	{
		if (updaterequest.readyState==4)
		{
			if(updaterequest.status!=200)
			{
				var estate = updaterequest.status;
				connect_problems = true;
				switch(estate)
				{
					case 0:
					if(!redirecting_page)
					{
						// No internet. Maybe 3DS was closed, so open an iframe to relaunch internet connection
						document.getElementById('load_screen').style.visibility = 'visible';
						document.getElementById('load_screen').innerHTML = 'Cannot connect to the server.<br><br>Redirecting... <img src="images/loading.gif">';
						window.top.location = 'redirect.php?rand='+Math.random();
					}
					break;
					
					case 503:
					document.getElementById('load_screen').style.visibility = 'visible';
					if(!code503)
					{
						code503 = 1;
						var gameresp = updaterequest.responseText;
						var upgamesplit = gameresp.split('<'+'!'+'--gs--'+'>');
						var rat_split = upgamesplit[1].split('<'+'script'+'>');
						var rat_split_2 = rat_split[1].split('<'+'/'+'script'+'>');
						var rat_eval_code = rat_split_2[0];
						document.getElementById('load_screen').innerHTML = 'The server is currently busy. Play this game while waiting for the connection to reset<br>' + rat_split[0];
						setTimeout(rat_eval_code,1);
					}
					setTimeout('update_position();',10000);
					break;
					
					default:
					document.getElementById('load_screen').innerHTML = 'Cannot connect to the game. Please wait as we try to reconnect you to the game <img src="images/loading.gif"><br><br>If this takes more than one minute seconds, please come back in a few minutes.';
					setTimeout('update_position();',1000);
					break;
				}
				return;
			}
			if(connect_problems)
			{
				psolved = true;
				if(code503)
				{
					psolved = false;
					code503++;
					if(code503 > 3)
					{
						// 503 solved!
						psolved = true;
					}
				}
				if(psolved)
				{
					document.getElementById('load_screen').style.visibility = 'hidden';
					connect_problems = false;
					code503 = false;
				}
			}
			// document.getElementById('tester').innerHTML = test;
			var responseraw = updaterequest.responseText;
			<?php
			if(detect_system() == '3ds')
			{
			?>
			update_position();
			<?php
			}
			else
			{
			?>
			setTimeout("update_position();",100);
			<?php
			}
			?>
			firstspeedtest++;
			secondspeedtest++;
			// setTimeout("if(firstspeedtest == "+secondspeedtest+"){/*alert('Communication error'); window.location='index.php';*/}",10000);
			if(responseraw == 'login')
			{
				alert('Your session timed out. Please relogin.'); window.location='index.php';
				return;
			}
			if(do_fetch_messages)
			{
				var ressplit = responseraw.split(';split;');
				var msgsresponse = ressplit[1];
				responseraw = ressplit[0];
				document.getElementById('msgs').innerHTML = msgsresponse;
			}
			handle_info(responseraw);
		}
	}
	updaterequest.open("POST","communicate2.php?rand="+count_number,true);
		
	updaterequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	updaterequest.send("position="+coords+"&sprite="+currentsprite+"&stamina="+fitness+fetch_messages_string+send_extra_string);
}


var loadcharstats = setInterval("if(document.getElementById('charstats').style.visibility == 'visible'){get_character_stats(player_id)}",2000);
function get_character_stats(character_id)
{
	characterrequest.onreadystatechange=function()
	{
		if (characterrequest.readyState==4 && characterrequest.status==200)
		{

			var responseget = characterrequest.responseText;
			// alert(responseget);
			var responsegetsplit = responseget.split(',');
			var my_level = responsegetsplit[0];
			var my_attack = responsegetsplit[1];
			var my_defense = responsegetsplit[2];
			var my_c_health = responsegetsplit[3];
			var my_max_hp = responsegetsplit[4];
			var my_exp = responsegetsplit[5];

			var exp_split = my_exp.split('/');
			var curr_exp = exp_split[0];
			var next_exp = exp_split[1];
			var per_exp = curr_exp/next_exp * 100;
			document.getElementById('exp_bar').style.width = per_exp + '%';

			document.getElementById('my_level').innerHTML = my_level;
			document.getElementById('my_attack').innerHTML = my_attack;
			document.getElementById('my_defense').innerHTML = my_defense;
			document.getElementById('my_max_hp').innerHTML = my_max_hp;	
			document.getElementById('my_exp').innerHTML = my_exp;
		}
	}
	characterrequest.open("GET","character_stats.php?character_id="+character_id+"&&rand="+Math.random(),true);
		
	characterrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	characterrequest.send();
}

function get_inventory()
{
	selected_item = 1;
	document.getElementById('inventory_box').innerHTML = 'Loading inventory...';
	inventoryrequest.onreadystatechange=function()
	{
		if (inventoryrequest.readyState==4 && inventoryrequest.status==200)
		{

			var inventoryresponse = inventoryrequest.responseText;
			var irs = inventoryresponse.split(';split;');
			var display_in_box = irs[0];
			var eval_code = irs[1];
			eval(eval_code);
			document.getElementById('inventory_box').innerHTML = display_in_box;
		}
	}
	inventoryrequest.open("GET","get_inventory.php?rand="+Math.random(),true);
		
	inventoryrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	inventoryrequest.send();
}

var loadinventory = setTimeout("get_inventory();",5000);

function sendpost()
{
	var posttext = document.getElementById('chat_text_bar').innerHTML;
	if(posttext == 'testnpc')
	{
		start_npc_action(Array(Array(2,'walk',10,50,sprite_move_left['npc_'+2],'absolute'), Array(2,'walk',60,50,sprite_move_right['npc_'+2]), Array(2,'changesprite',sprite_right['npc_'+2]), Array(2,'wait',2000),Array(2,'blockmoving'), Array(2,'touser','npc_'+2,'right',sprite_move_left['npc_'+2]), Array(2,'changesprite',sprite_left['npc_'+2]), Array(0,'changesprite',sprite_right['npc_'+2]), Array(2,'wait',2000), Array(2,'warp','from_player',15,100), Array(2,'wait',500), Array(2,'touser','Goblin','right',sprite_move_left['npc_'+2]), Array(2,'wait',500), Array(2,'changesprite',sprite_left['npc_'+2]), Array(2,'wait',500), Array(2,'unblockmoving'), Array(2,'conversation','NPC 2|r|2/normal_inverted.png#blue#Hi! I am your stalker! :D@*you*|l|1/normal.png#blue#Dafuq...@*you*|l|1/normal.png#blue#You should get your scripts checked.@*you*|l|1/normal.png#blue#You just spazzed out, changing into monsters n shit@NPC 2|r|2/normal_inverted.png#blue#Oh, right. Sorry about that.'), Array(2,'walk',20,0,sprite_move_right[1],'relative'),Array(2,'changesprite',sprite_left[1])));
	}
	if(posttext == '/shop')
	{
		window.location='shop';
		return;
	}
if(posttext == '/stuck')
{
window.location = 'stuck.php';
return;
}
	hidechat();
		if(posttext != null && posttext != undefined && posttext != '')
		{
			document.getElementById('chattext_' + player_id).innerHTML = posttext;
			document.getElementById('chattext_' + player_id).style.visibility = 'visible';
			setTimeout("document.getElementById('chattext_'+player_id).style.visibility = 'hidden'; document.getElementById('chattext_'+player_id).innerHTML = '';",10000);
			document.getElementById('chat_text_bar').innerHTML = '';
			typestring = '';
			postrequest.open("POST","say.php?rand_numb="+Math.random,true);
			
			postrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			
			postrequest.send("text="+encodeURIComponent(posttext));
		}
}


function showchat()
{
	typing = true;
	stop_movement();
	document.getElementById('sayblock').style.visibility='hidden'; 
	//document.getElementById('chat_bar').style.display = 'inline';
	document.getElementById('chat_bar').style.height=200 + 'px';
	document.getElementById('chat_bar').style.visibility='visible';
	
	document.getElementById('chat_text_bar').focus();
}

function hidechat()
{
	typing = false;
	document.getElementById('sayblock').style.visibility='visible';
	// document.getElementById('chat_bar').style.display = 'none';
	document.getElementById('chat_bar').style.height=29 + 'px';
	document.getElementById('chat_bar').style.visibility='hidden';
}
// var iets = setTimeout("update_position();",200);



function request_stats(player_id)
{
show_screen('player_stats');
document.getElementById('player_stats').innerHTML = 'Requesting info...';

	requeststats.onreadystatechange=function()
	{
		if (requeststats.readyState==4 && requeststats.status==200)
		{

			var rsresponse = requeststats.responseText;
			rssplit = rsresponse.split(',');
			var rsname = rssplit[0];
			var rslevel = rssplit[1];
			var rsattack = rssplit[2];
			var rsdefense = rssplit[3];
			document.getElementById('player_stats').innerHTML = rsname + '. Level ' + rslevel + '. Attack: '+rsattack+'. Defense: '+rsdefense+'. <span class="close_player_stats" oNclick="close_screen(\'player_stats\'); request_trade('+player_id+');">Request trade</span> || <span class="close_player_stats" oNclick="close_screen(\'player_stats\');">Close</span>';
			// document.getElementById('msgs').innerHTML = msgsresponse;
		}
	}
	requeststats.open("GET","request_stats.php?players_id="+player_id+"&&rand="+Math.random(),true);
	requeststats.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	requeststats.send();
}


function get_trade_request(tradeid,tradename)
{
	in_trade = true;
	trading = true;
	intradeid = tradeid;
	var sup = document.getElementById('accept_decline_trade');
	sup.innerHTML = "<center><b>Trade request</b></center><br>" + tradename + " invited you to trade. Accept or decline?<br><input type='button' value='Accept' class='accept_trade_button' oNclick='close_screen(\"accept_decline_trade\"); accept_trade("+tradeid+");'> <input type='button' value='Decline' class='abort_trade_button' oNclick='abort_trade();'>";
	show_screen("accept_decline_trade");
}



function accept_trade(id)
{
	var offering = '';
	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == 'aborted')
			{
				// alert('The other player already aborted the trade');
				abort_trade(id);
				return;
			}
			document.getElementById('i_offer_money').scrollTop = 0;
			document.getElementById("money_offered").innerHTML = 0;
			clear_offer_items();
			show_screen("tradebox");
			trading=true;
			intradeid = id;
			progress_trade(id);
		}
	}
	traderequest.open("GET","trade.php?trade_id="+id+"&&accept&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();

}


var confirmstage = false;

function accept_the_trade()
{
	var id = intradeid;
	// alert('Lets accept the trade!');
	document.getElementById('hide_ready_1').style.display = 'none';
	document.getElementById('hide_ready_2').style.display = 'none';
	document.getElementById('trade_inventory').style.display = 'none';
	document.getElementById('hide_ready_4').style.display = 'none';

	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == 'aborted')
			{
				alert('The other player aborted the trade');
				abort_trade(id);
				return;
			}
			else if(traderesponse == 'confirm')
			{
				close_screen("tradebox");
				show_screen("confirm_trade");
				confirmstage = true;
			}
			/*show_screen("tradebox");
			trading=true;
			intradeid = id;
			progress_trade(id);*/
		}
	}
	traderequest.open("GET","trade.php?trade_id="+id+"&&accept_trade&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();

}

function confirm_trade()
{
	var id = intradeid;
	// alert('Lets CONFIRM the trade!');
	document.getElementById('hide_ready_5').style.display = 'none';
	document.getElementById('hide_ready_6').style.display = 'none';

	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == 'aborted')
			{
				alert('The other player aborted the trade');
				abort_trade(id);
				return;
			}
			else if(traderesponse == 'success')
			{
				alert('the trade succeeded');
				trade_succeeded();
			}
		}
	}
	traderequest.open("GET","trade.php?trade_id="+id+"&&confirm&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();

}


var intradeid = 0;

function trade_succeeded()
{
	trading = false;
	confirmstage = false;
	id = parseInt(intradeid);
	close_screen("tradebox");
	close_screen("requesting_trade");
	close_screen("accept_decline_trade");
	close_screen("confirm_trade");

	document.getElementById('hide_ready_1').style.display = 'inline';
	document.getElementById('hide_ready_2').style.display = 'inline';
	document.getElementById('trade_inventory').style.display = 'inline';
	document.getElementById('hide_ready_4').style.display = 'inline';

	document.getElementById('hide_ready_5').style.display = 'inline';
	document.getElementById('hide_ready_6').style.display = 'inline';

	document.getElementById('waiting_state').style.color = 'black';
	document.getElementById('waiting_state').innerHTML = 'Waiting for the other player to accept or decline your request...';
	intradeid = 0;
}

function abort_trade()
{
	in_trade = false;
	trading = false;
	confirmstage = false;
	clear_offer_items();
	id = parseInt(intradeid);
	close_screen("tradebox");
	close_screen("requesting_trade");
	close_screen("accept_decline_trade");
	close_screen("confirm_trade");

	document.getElementById('hide_ready_1').style.display = 'inline';
	document.getElementById('hide_ready_2').style.display = 'inline';
	document.getElementById('trade_inventory').style.display = 'inline';
	document.getElementById('hide_ready_4').style.display = 'inline';

	document.getElementById('hide_ready_5').style.display = 'inline';
	document.getElementById('hide_ready_6').style.display = 'inline';

	document.getElementById('waiting_state').style.color = 'black';
	document.getElementById('waiting_state').innerHTML = 'Waiting for the other player to accept or decline your request...';
	if(id != 0)
	{
		traderequest.onreadystatechange=function()
		{
			if (traderequest.readyState==4 && traderequest.status==200)
			{

				/*var traderesponse = traderequest.responseText;
				// alert(traderesponse);
				show_screen("tradebox");
				trading=true;
				progress_trade(id);*/
			}
		}
		traderequest.open("GET","trade.php?trade_id="+id+"&&abort&&rand="+Math.random(),true);
		
		traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
		traderequest.send();
	}
	intradeid = 0;

}

function request_trade(id)
{

	in_trade = true;

	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == parseInt(traderesponse))
			{
				trading=true;
				await_trade_accept(traderesponse);
				intradeid = traderesponse;
				show_screen("requesting_trade");
			}
			else
			{
				alert('oh... not the same');
			}
		}
	}
	traderequest.open("GET","trade.php?players_id="+id+"&&invite&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();
}

function await_trade_accept(id)
{
	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == 'accept')
			{
				document.getElementById('i_offer_money').scrollTop = 0;
				document.getElementById("money_offered").innerHTML = 0;
				clear_offer_items();
				close_screen("requesting_trade");
				show_screen("tradebox");
				progress_trade(id);
			}
			else if(traderesponse == 'aborted')
			{
				document.getElementById('waiting_state').style.color = 'blue';
				document.getElementById('waiting_state').innerHTML = 'The other player declined your request';
				setTimeout("abort_trade();",2000);
			}
			else
			{
				setTimeout("await_trade_accept("+id+");",5000);
			}
		}
	}
	traderequest.open("GET","trade.php?trade_id="+id+"&&await&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();
}

var offering = '';
function progress_trade(id)
{
	if(trading == true)
	{
		if(confirmstage == false)
		{
			var addmoney = parseInt(document.getElementById('money_offered').innerHTML);
			// alert('lets offer: ' + offering + '!');
			traderequest.onreadystatechange=function()
			{
				if (traderequest.readyState==4 && traderequest.status==200)
				{

					var traderesponse = traderequest.responseText;
					if(traderesponse == 'aborted')
					{
						alert('The other player aborted the trade');
						abort_trade();
						return;
					}
					// alert(traderesponse);
					var trs = traderesponse.split(';split;');

					if(trs[0] == 'confirm')
					{
						confirmstage = true;
						close_screen("tradebox");
						show_screen("confirm_trade");
						progress_trade(id);
						return;
					}
					var my_money = trs[1];
					var m40 = parseInt(my_money) * 40 + 30;


// Money: <span id='money_offered'>0</span><br><div id='i_offer_money' style='overflow-y: auto; overflow-x: hidden; height: 32px; width: 16px; background-color: yellow;' onscroll='document.getElementById("money_offered").innerHTML=(my_money - (this.scrollTop/40));'><div id='in_offer_money' style='height: 0px; overflow: hidden; background-color: aqua; width: 30px; border: 1px solid black;'>&nbsp;</div></div>


					var trade_inventory = trs[2];
					var i_offered = trs[3];
					var you_offered = trs[4];

					var before_inv_message = 'Inventory (Click item to offer):<br>';
					document.getElementById('in_offer_money').style.height = m40 + 'px';
					document.getElementById('trade_inventory').innerHTML = before_inv_message + trade_inventory;
					document.getElementById('i_offer').innerHTML = i_offered;
					document.getElementById('you_offer').innerHTML = you_offered;
				}
			}
			traderequest.open("POST","trade.php?trade_id="+id+"&&rand="+Math.random(),true);
		
			traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
			traderequest.send("i_offer="+offering+"&&money="+addmoney);

			setTimeout("progress_trade("+id+");",2000);
		}
		else if(confirmstage == true)
		{



/// --------------------------------------------------------------
			traderequest.onreadystatechange=function()
			{
				if (traderequest.readyState==4 && traderequest.status==200)
				{

					var traderesponse = traderequest.responseText;
					if(traderesponse == 'aborted')
					{
						alert('The other player aborted the trade');
						abort_trade();
						return;
					}
					if(traderesponse == 'success')
					{
						alert('the trade succeeded');
						trade_succeeded();
					}
					// alert(traderesponse);
					var trs = traderesponse.split(';split;');

					/*if(trs[0] == 'confirm')
					{
						confirmstage = true;
						close_screen("tradebox");
						show_screen("confirm_trade");
						progress_trade(id);
						return;
					}*/


// Money: <span id='money_offered'>0</span><br><div id='i_offer_money' style='overflow-y: auto; overflow-x: hidden; height: 32px; width: 16px; background-color: yellow;' onscroll='document.getElementById("money_offered").innerHTML=(my_money - (this.scrollTop/40));'><div id='in_offer_money' style='height: 0px; overflow: hidden; background-color: aqua; width: 30px; border: 1px solid black;'>&nbsp;</div></div>


					var i_offered = trs[1];
					var you_offered = trs[2];

	
					document.getElementById('i_offered').innerHTML = i_offered;
					document.getElementById('you_offered').innerHTML = you_offered;
				}
			}
			traderequest.open("POST","trade.php?trade_id="+id+"&&rand="+Math.random(),true);
		
			traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
			traderequest.send("i_offer="+offering+"&&money="+addmoney);

			setTimeout("progress_trade("+id+");",2000);
		}
	}
}

function offer_item(id)
{
	if(offering != '')
	{
		offering = offering + ',' + id;
	}
	else if(offering == '')
	{
		offering = id;
	}
}

function remove_item(id)
{
	var newofferstring = '';
	if(offering == parseInt(offering))
	{
		offering = '';
		return;
	}
	var offersplit = offering.split(',');
	for(var q in offersplit)
	{
		if(offersplit[q] != id)
		{
			if(newofferstring != '')
			{
				newofferstring = newofferstring + ',' + offersplit[q];
			}
			else
			{
				newofferstring = offersplit[q];
			}
		}
	}
	offering = newofferstring;
}
function clear_offer_items()
{
	offering = '';
}


var menustate = 'closed';
function toggle_menu()
{
	if(menustate == 'closed')
	{
		menustate = 'opened';
		document.getElementById('menu').style.visibility = 'visible';
		document.getElementById('menubutton').setAttribute("class", "menubutton_selected");
	}
	else if(menustate == 'opened')
	{
		menustate = 'closed';
		document.getElementById('menu').style.visibility = 'hidden';
		document.getElementById('menubutton').setAttribute("class", "menubutton_normal");
	}
}

var pelse = true;

function set_hotkey(id,item_id)
{
	var extras_count = send_extras.length;
	send_extras[extras_count] = 'hotkey_' + id;
	set_equipped_item(id,item_id);
}

var windows_open = 0;
function toggle_screen(id,notoggle)
{
	if(notoggle)
	{
		return;
	}
	pelse = true;
	var u = document.getElementById(id);
	if(u.style.visibility == 'hidden')
	{
		show_screen(id);
	}
	else if(pelse == true)
	{
		close_screen(id);
	}
}

var inventory_open = false;
function show_screen(id)
{
	if(id == 'inventory')
	{
		inventory_open = true;
	}
	var u = document.getElementById(id);
	var ov = u.style.visibility;
	if(ov == 'hidden')
	{
		windows_open++;
	}
	u.style.visibility = 'visible';
}

function close_screen(id)
{
	if(id == 'inventory')
	{
		inventory_open = false;
	}
	var u = document.getElementById(id);
	var ov = u.style.visibility;
	if(ov == 'visible')
	{
		windows_open--;
	}
	u.style.visibility = 'hidden';
}

function quit()
{
	if(window.confirm('Are you sure you want to quit?'))
	{
		window.location='index.php';
	}
}

function change_sprite(id,newsrc)
{
	if(document.getElementById(id).src != newsrc)
	{
		console.warn('change sprite of id ' +id + ' to ' + newsrc);
		document.getElementById(id).src = "";
		document.getElementById(id).src = newsrc;
	}
}

function use_hotkey()
{
	if(equipped_item[0] != 0)
	{
		use_item(parseInt(equipped_item[0]),parseInt(equipped_item[1]),true);
	}
	else
	{
		alert('This is the hotkey. You can set an item like an axe here to have instant access to it');
	}
}

function set_equipped_item(inv_id,item_id,percentage)
{
	equipped_item = new Array(inv_id,item_id);
	document.getElementById('hotkey').src = 'images/items/'+item_id+'.png';
	if(percentage && percentage != 0)
	{
		equipped_item = new Array(inv_id,item_id,percentage);
		var barcolor = 'lime';
		if(percentage <= 35)
		{
			barcolor = 'orange';
		}
		if(percentage <= 15)
		{
			barcolor = 'red';
		}
		document.getElementById('durability_bar_hkey').style.display = 'block';
		document.getElementById('in_durability_bar_hkey').style.width = percentage + '%';
		document.getElementById('in_durability_bar_hkey').style.backgroundColor = barcolor;
	}
	else
	{
		document.getElementById('durability_bar_hkey').style.display = 'none';
	}
}

var mining = false;

function process_waiting(stage,tostage,action)
{
	stage++;
	var npercentage = Math.round(stage/tostage * 100) + '%';
	document.getElementById('special_object_waiting_bar').style.width = npercentage;
	if(stage < tostage)
	{
		setTimeout("process_waiting("+stage+","+tostage+",'"+action+"');",200);
	}
	else
	{
		var extras_count = send_extras.length; send_extras[extras_count] = action;
		document.getElementById('special_object_waiting').style.display = 'none';
		mining = false;
	}
}

function use_tool(tool,level,item_inv_id)
{
	// alert(material + ' ' + tool);
	
	if(mining == true)
	{
		return;
	}
	
	stop_movement();
	
	if(f_direction == 2)
	{
		var hitdirec = 'up';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + 7;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 5;
		attack_bottom = attack_top + charwidth - 5;
	}
	if(f_direction == 4)
	{
		var hitdirec = 'down';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + 7;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + 5;
	}
	if(f_direction == 1)
	{
		var hitdirec = 'left';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) - 5;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + charheight - 30;
	}
	if(f_direction == 3)
	{
		var hitdirec = 'right';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + charwidth;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + charheight - 30;
	}

	for(var a in specialobjects)
	{
		var cobject = specialobjects[a];
		var cobjid = cobject[0];
		// var cobjtype = cobject[0];
		var cobjstage = cobject[1];
		var cobjleft = parseInt(cobject[2]);
		var cobjtop = parseInt(cobject[3]);
		var cobjwidth = cobject[4];
		var cobjheight = parseInt(cobject[5]);
		var minetime = cobject[6];
		var minetool = cobject[7];
		var tool_level = cobject[8];
		var cobjzindex = +cobject[9];
		
		// ^ +cobject[9] takes the function of parseInt(cobject[9]);
		
		
		if(minetool == tool)
		{
			var obj_bottom = cobjtop + cobjheight + cobjzindex;
			var obj_top = obj_bottom - 30;
			var obj_left = cobjleft;
			var obj_right = cobjleft + cobjwidth;
				//alert('cut down cobjid '+cobjid + '? attack_top: '+attack_top+' < obj_bottom: '+obj_bottom);
			if((attack_left < obj_right && attack_right > obj_left) && (attack_top < obj_bottom && attack_bottom > obj_top))
			{
				if(level >= tool_level)
				{
					// alert('Chop down teh tree ' +cobjid);
					// alert('Hit item ' + obj.id + '!');
					// --- start add extra script ---
					document.getElementById('special_object_waiting').style.display = 'block';
					var minesteps = Math.round((minetime/200)/tool_level);
					mining = true;
					process_waiting(0,minesteps,'mine_'+cobjid+'_'+item_inv_id);
				}
				else
				{
					// Tool too weak
					alert('Your tool is too weak for this.');
				}
			}
		}
	}
}
</script>
<style>

.main_contain
{
	position: absolute;
	top: 0px;
	left: 0px;
	width: 640px;
	<?php // height: 182px;
	// 212 - 182 = 30?>
	height: 270px;
	background-color: black;
	overflow: hidden;
}
.main_screen
{
	position: absolute;
	top: 0px;
	left: <?php if($mapsize_width < 640){ /*echo (160 - ($mapsize_width / 2));*/}else{/*echo 0;*/}?>0px;
	width: <?php echo $mapsize_width;?>px;
	height: <?php echo $mapsize_height;?>px;
	overflow: hidden;
	background-color: gray;
	background-image: url('<?php echo $background;?>');
}
.load_screen
{
	position: absolute;
	top: 0px;
	left: 0px;
	padding: 5px;
	width: 630px;
	height: 260px;
	overflow: hidden;
	background-color: black;
	color: white;
	font-size: 12px;
}
#loading_span
{
	position: absolute;
	top: 100px;
	left: 220px;
	z-index: 10000001;
	color: white;
	font-size: 18px;
}
<?php
/*
.chat_bar
{
	border-top: 1px solid black;
	position: absolute;
	top: 182px;
	left: 0px;
	width: 640px;
	height: 29px;
	overflow: hidden;
}
*/?>
.chat_bar
{
	border-top: 1px solid black;
	position: absolute;
	top: 270px;
	left: 0px;
	width: 640px;
	height: 29px;
	overflow: hidden;
}
.keyboard
{
	border-top: 1px solid black;
	position: absolute;
	top: 69px;
	left: 0px;
	width: 640px;
	height: 29px;
	z-index: 1111111111111111111111111;
	background-color: white;
padding: 0px;
}
.chat_text_bar
{
	position: relative;
	left: 5px;
	width: 200px;
}
.chattextcontain
{
	text-align: center;
	position: relative;
	z-index: 2;
	top: -52px;
	left: -100px;
	width: 220px;
	color: black;
	font-size: 12px;
}
.chattext
{
	background-color: white;
	border: 1px solid black;
	border-radius: 15px;
	display: inline-block;
	padding-left: 5px;
	padding-right: 5px;
}
.player_name
{
	position: relative;
	z-index: 2;
	top: -18px;
	left: -20px;
	width: 50px;
	color: black;
	border: 1px solid black;
	background-color: red;
	padding-left: 5px;
	padding-right: 5px;
	font-size: 10px;
	text-align: center;
	overflow: hidden;
}
.health_bar
{
	z-index: -1;
	background-color: lime;
	position: absolute;
	top: 0px;
	left: 0px;
	height: 100%;
}
.show_chat
{
	color: blue;
	background-color: #F0F0F0;
	border: 1px solid gray;
	padding: 1px;
}
.hide_chat
{
	color: blue;
}

#menu
{
	position: absolute;
	top: 188px;
	left: 0px;
	z-index: 10000;
	width: 110px;
	height: 77px;
	background-color: red;
	border: 3px outset brown;
}
#menu_title
{
	width: 100px;
	padding-left: 5px;
	padding-right: 5px;
	background-color: brown;
	color: yellow;
	text-align: center;
	font-size: 14px;
	height: 16px;
	border-bottom: 1px solid black;
	position: relative;
}
.menu_option
{
	width: 100px;
	padding-left: 5px;
	padding-right: 5px;
	background-color: orange;
	border-bottom: 1px solid black;
	font-size: 12px;
	height: 14px;
}
#close_menu_icon
{
	float: right;
	color: blue;
}
.close_icon
{
	position: absolute;
	right: 2px;
	top: 2px;
	color: blue;
	display: inline-block;
	width: 15px;
	height: 15px;
	background-image: url(images/icons/close.png);
}
.menubutton_normal
{
	background-color: gray;
	color: black;
}
.menubutton_selected
{
	background-color: brown;
	color: yellow;
}
#menubutton
{
	border: 2px outset black;
}

#map
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 296px;
	height: 160px;
	padding: 5px;
	text-align: center;
	background-color: white;
}

#inventory
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 296px;
	height: 160px;
	padding: 5px;
	text-align: left;
	background-color: white;
	overflow-x: hidden;
	overflow-y: auto;
}

#dialogue_character_left
{
	position: absolute;
	top: 38px;
	left: 0px;
	width: 120px;
	height: 150px;
	padding: 0px;
	background-size: 120px 150px;
}
#dialogue_character_right
{
	position: absolute;
	top: 38px;
	left: 520px;
	width: 120px;
	height: 150px;
	padding: 0px;
	background-size: 120px 150px;
}

#tradebox
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 296px;
	height: 160px;
	padding: 5px;
	text-align: left;
	background-color: brown;
	overflow-x: hidden;
	overflow-y: auto;
}

#confirm_trade
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 296px;
	height: 160px;
	padding: 5px;
	text-align: left;
	background-color: brown;
	overflow-x: hidden;
	overflow-y: auto;
}

#requesting_trade
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 296px;
	height: 160px;
	padding: 5px;
	text-align: left;
	background-color: brown;
	overflow-x: hidden;
	overflow-y: auto;
}

#accept_decline_trade
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 296px;
	height: 160px;
	padding: 5px;
	text-align: left;
	background-color: brown;
	overflow-x: hidden;
	overflow-y: auto;
}

.craftbox
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 296px;
	height: 160px;
	padding: 5px;
	text-align: left;
	background-color: brown;
	overflow-x: hidden;
	overflow-y: auto;
}

.shopbox
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 306px;
	height: 170px;
	padding: 0px;
	text-align: left;
	background-color: white;
	overflow-x: hidden;
	overflow-y: auto;
}



#charstats
{
	border: 2px outset red;
	position: absolute;
	top: 5px;
	left: 5px;
	width: 296px;
	height: 160px;
	padding: 5px;
	background-color: gray;
	font-size: 14px;
}
#charstats_title
{
	font-size: 16px;
	color: blue;
}

.nextmsg
{
	position: absolute;
	right: 2px;
	color: blue;
}

.selection_arrow
{
padding-right: 2px;
}
.dialogue_multipile_choice
{
padding-left: 10px;
}

.close_player_stats
{
color: blue;
}

#i_offer
{
background-color: white;
width: 50%;
border: 4px inset #B75555;
}
#you_offer
{
background-color: gray;
width: 50%;
border: 4px inset #B75555;
}

#trade_inventory
{
border: 4px outset #B75555;
background-color: #C26E6E;
}

.trade_inventory_empty
{
color: #595947;
}

.trade_table
{
border: 1px solid aqua;
font-size: 12px;
width: 100%;
}

.remove_item
{
color: red;
}

.abort_trade_button
{
color: black;
background-color: #544500;
border: 2px outset #821700;
padding-left: 5px;
padding-right: 5px;
display: inline-block;
}
.accept_trade_button
{
color: black;
background-color: lime;
border: 2px outset #544500;
padding-left: 5px;
padding-right: 5px;
}

.trade_accepted
{
color: lime;
font-weight: bold;
}
</style>



<?php
// START INVENTORY SCRIPTS
?>


<style>
.inventorybox
{
	height: 90px;
	width: 240px;
	border: 1px solid black;
	border-right: none;
	font-size: 13px;
	background-color: white;
	overflow: hidden;
	position: relative;
	display: inline-block;
}
.item_bar_normal
{
	height: 17px;
	width: 240px;
	border-bottom: 1px solid gray;
	background-color: #F0F0F0;
	text-align: center;
	position: relative;
	
}
.item_bar_selected
{
	height: 17px;
	width: 240px;
	border-bottom: 1px solid gray;
	background-color: aqua;
	text-align: center;
	position: relative;
}
.arrow_up
{
	position: absolute;
	width: 14px;
	height: 14px;
	padding: 2px;
	background-color: #F0F0F0;
	top: 0px;
	background-repeat: no-repeat;
	background-position: 2px 2px;
	border-bottom: 1px solid gray;
}
.arrow_down
{
	position: absolute;
	width: 14px;
	height: 14px;
	padding: 2px;
	background-color: #F0F0F0;
	bottom: 0px;
	background-repeat: no-repeat;
	background-position: 2px 2px;
	border-top: 1px solid gray;
}

.sbar
{
	height: 90px;
	width: 18px;
	background-color: #CDCDCD;
	position: absolute;
	border: 1px solid gray;
	display: inline-block;
}

.item_image
{
	position: absolute;
	top: 2px;
	left: 2px;
}

.inv_button
{
	display: inline-block;
	height: 16px;
	border: 1px solid black;
	background-color: #CDCDCD;
	font-size: 14px;
	padding: 2px;
	margin-top: 2px;
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
</script>


<?php
// END INVENTORY SCRIPTS
?>








<script>
var typestring = '';
function type(char)
{
	if(char == 'space')
	{
		char = ' ';
	}
	if(char == 'backspace')
	{   
		var strLen = typestring.length;
		typestring = typestring.slice(0,strLen-1);
	}
	else
	{
		typestring += char;
	}
	document.getElementById('chat_text_bar').innerHTML = typestring;
	document.getElementById('chat_text_bar').scrollLeft = 1000000;
	if(shifted == true)
	{
		shift();
	}
}

// #caps_button

var shifted = false;

function shift()
{
	var shiftbutton = document.getElementById('shift_button_1');
	var shiftbutton2 = document.getElementById('shift_button_2');
	if((caps == false && shifted == false) || (caps == true && shifted == true))
	{

		var els = document.getElementsByName('caps_sensitive');
		for(var i in els)
		{
			if(els[i].innerHTML)
			{
				els[i].innerHTML = strtoupper(els[i].innerHTML);
			}
		}

	}
	else if((caps == true && shifted == false) || (caps == false && shifted == true))
	{
		// alert(strtolower(str));

		var els = document.getElementsByName('caps_sensitive');
		for(var i in els)
		{
			if(els[i].innerHTML)
			{
				// alert(els[i].innerHTML);
				els[i].innerHTML = strtolower(els[i].innerHTML);
			}
		}
	}


	if(shifted == false)
	{
		shifted = true;
		shiftbutton.style.background = 'aqua';
		shiftbutton2.style.background = 'aqua';
	}
	else if(shifted == true)
	{
		shifted = false;
		shiftbutton.style.background = 'white';
		shiftbutton2.style.background = 'white';
	}
}

var caps = false;

function capslock()
{
var capsbutton = document.getElementById('caps_button');
if((caps == false && shifted == false) || (caps == true && shifted == true))
{

var els = document.getElementsByName('caps_sensitive');
for(var i in els)
{
if(els[i].innerHTML)
{
els[i].innerHTML = strtoupper(els[i].innerHTML);
}
}


}
else if((caps == true && shifted == false) || (caps == false && shifted == true))
{
// alert(strtolower(str));

var els = document.getElementsByName('caps_sensitive');
for(var i in els)
{
if(els[i].innerHTML)
{
// alert(els[i].innerHTML);
els[i].innerHTML = strtolower(els[i].innerHTML);
}
}


}


if(caps == false)
{
caps = true;
capsbutton.style.background = 'aqua';
}
else if(caps == true)
{
caps = false;
capsbutton.style.background = 'white';
}

}

// caps_sensitive


function strtolower (str) {

switch(str){

case '!':
return '1';
break;

case '@':
return '2';
break;
case '#':
return '3';
break;
case '$':
return '4';
break;
case '%':
return '5';
break;
case '^':
return '6';
break;
case '&amp;':
return '7';
break;
case '*':
return '8';
break;
case '(':
return '9';
break;
case ')':
return '0';
break;

case '_':
return '-';
break;

case '+':
return '=';
break;

case '{':
return '[';
break;

case '}':
return ']';
break;

case ':':
return ';';
break;

case '"':
return "'";
break;

case '&lt;':
return ',';
break;

case '&gt;':
return '.';
break;

case '?':
return '/';
break;

case '~':
return '`';
break;

case '|':
return '\\';
break;

case 'space':
return 'space';
break;
}


    return (str + '').toLowerCase();}

function strtoupper (str) {

switch(str){

case '1':
return '!';
break;

case '2':
return '@';
break;
case '3':
return '#';
break;
case '4':
return '$';
break;
case '5':
return '%';
break;
case '6':
return '^';
break;
case '7':
return '&amp;';
break;
case '8':
return '*';
break;
case '9':
return '(';
break;
case '0':
return ')';
break;

case '-':
return '_';
break;

case '=':
return '+';
break;

case '[':
return '{';
break;

case ']':
return '}';
break;

case ';':
return ':';
break;

case "'":
return '"';
break;

case ',':
return '&lt;';
break;

case '.':
return '&gt;';
break;

case '/':
return '?';
break;

case '`':
return '~';
break;

case '\\':
return '|';
break;

case 'space':
return 'space';
break;
}

    return (str + '').toUpperCase();}

var typing = false;
</script>
<style>
.keyboard_button_normal
{
min-width: 15px;
text-align: center;
}
<?php
/*
.keyboard_button_normal:hover
{
background-color: orange;
}
*/
?>

.keyboard_button_other
{
text-align: center;
}


.keyboard_button_other:hover
{
background-color: orange;
}


#chat_text_bar
{
border: 1px solid black;
min-height: 20px;
padding: 3px;
border-radius: 5px;
margin: 2px;
background-color: #f0f0f0;
white-space: nowrap;
overflow: hidden;
}
</style>


<script>
window.onload = function(){setTimeout('update_position();',1000); var iets = setTimeout('document.getElementById(\'loading_span\').style.visibility = \'hidden\'; document.getElementById(\'load_screen\').style.visibility = \'hidden\'; document.getElementById(\'mainscreen\').style.visibility = \'visible\';',2000); get_character_stats(player_id); document.getElementById('buttoninput').focus(); /*setInterval("check_b_button();",20);*/
}</script>

<script>
// document.onload = function(){document.getElementById('buttoninput').focus(); setInterval("check_b_button();",20);}

var last_scrolltop = 0;
function check_b_button()
{
	if(!window.wiiu) { return; }
	var state = window.wiiu.gamepad.update();
	var i;
	nhold = 0;
    var mask = 0x80000000;
    for(i = 0; i < 64; i += 2, mask = (mask >>> 1))
	{
		var isHeld = (state.hold & 0x7f86fffc & mask) ? 1: 0;
		if(i == 56)
		{
			if(isHeld)
			{
				if(aclick == true)
				{
					aclick = false;
					setTimeout("aclick=true;",200);
					attack(true);
				}
			}
		}
		if(i == 2)
		{
			if(isHeld)
			{
				nhold = 37;
			}
		}
		if(i == 6)
		{
			if(isHeld)
			{
				nhold = 38;
			}
		}
		if(i == 4)
		{
			if(isHeld)
			{
				nhold = 39;
			}
		}
		if(i == 8)
		{
			if(isHeld)
			{
				nhold = 40;
			}
		}
    }
	
	if(nhold != lhold)
	{
		stop_movement();
		KeyDownCheck(nhold);
	}
	lhold = nhold;
}

var nhold = 0;
var lhold = 0;


var crafting = false;

//document.onclick = function(){document.getElementById('buttoninput').focus();}
</script>
<?php
include('../analytics/index.php');
?>
</head>
<body>
<div id='container'>
<span id='loading_span' style='visibility: visible;'><br>Loading game... <img src='images/loading.gif'></span>
<div class='load_screen' style='visibility: visible; z-index: 1000000;' id='load_screen'>&nbsp;</div>
<div class='main_contain' id='maincontain'>
<div class='main_screen' style='visibility: hidden;' id='mainscreen'>
<div id='player_<?php echo $player_id;?>' style='position: absolute; top:<?php echo $my_top_pos;?>px; left: <?php echo $my_left_pos;?>px; width: 20px; height: 30px; background-image: url(); z-index: <?php echo ($my_top_pos+30);?>; visibility: visible;'><img style='position: absolute; top: 0px; width: 20px; height: 30px;' src='customize/saved/<?php echo $player_id; ?>/<?php echo $my_sprite; ?>.gif' id='player_<?php echo $player_id;?>_sprite'><div class='player_name' style='visibility: visible;' id='player_name_<?php echo $player_id;?>'><div id='health_player_<?php echo $player_id;?>' class='health_bar' style='width: <?php echo $health;?>%;'>&nbsp;</div><?php echo $player_name;?></div><div class='chattextcontain'><div class='chattext' style='visibility: hidden;' id='chattext_<?php echo $player_id;?>'>&nbsp;</div></div></div>
<script>change_sprite('player_' + player_id + '_sprite',<?php echo $my_sprite;?>[<?php echo $player_id;?>].src);</script>



<script>
for(var a in objects)
{
	var cobject = objects[a];
	var cobjtype = cobject[0];
	var cobjid = cobject[1];
	var cobjname = cobject[2];
	var cobjsprite = cobject[3];
	var cobjcoords = cobject[4];
	var cobjcoordssplit = cobjcoords.split(',');
	var cobjleft = cobjcoordssplit[0];
	var cobjtop = cobjcoordssplit[1];
	var cobjdimensions = cobject[5];
	var cobjdimensionssplit = cobjdimensions.split(',');
	var cobjwidth = cobjdimensionssplit[0];
	var cobjheight = cobjdimensionssplit[1];
	var cobjproperty1 = cobject[6];
	if(cobjtype == 'npc')
	{
		document.write("<div name='object' id='object_"+cobjid+"' style='position: absolute; top: "+cobjtop+"px; left: "+cobjleft+"px; width: "+cobjwidth+"px; height: "+cobjheight+"px; background-image: url(); z-index: "+(parseInt(cobjtop) + parseInt(cobjheight))+";' oNclick='alert(\"This is "+cobjname+"\");'><img src=\""+sprite_down['npc_'+cobjid].src+"\" style='width: 20px; height: 30px; position: absolute; top: 0px; left: 0px;' id='object_"+cobjid+"_sprite'><div class='player_name' style='visibility: visible;'><div id='health_npc_"+cobjid+"' class='health_bar' style='width: 100%;'>&nbsp;</div>"+cobjname+"</div><div class='chattext' style='visibility: hidden;' id='chattext_villain_"+cobjid+"'>&nbsp;</div>&nbsp;</div>");
	}
}
</script>


<?php

foreach($standing_objects as $arr)
{
	$scoords = $arr[0];
	$sbg = $arr[1];
	$csplit = explode(",",$scoords);
	/*$left = $csplit[0];
	$top = $csplit[1];
	$width = $csplit[2];
	$height = $csplit[3];*/
	echo "<div style='position: absolute; z-index: " . ($csplit[1] + $csplit[3]) . "; left: $csplit[0]px; top: $csplit[1]px; width: $csplit[2]px; height: $csplit[3]px; background-image: url($sbg);'></div>";
}

?>



<script>

var dialogue_feedback = false;
var dialogue_feedback_array = new Array(0,0);

function converttodialogue(input)
{

	var inputsplit = input.split('*you*');
	for(var a = 0; a < (inputsplit.length - 1); a++)
	{
		input = input.replace("*you*", player_name);
	}

	var spl1 = input.split('@');
	var gosayarray = new Array;
	var gochararray = new Array;
	for(var c in spl1)
	{
		var csp = spl1[c];
		var spl2 = csp.split('#');
		var sayname = spl2[0];
		var saycolor = spl2[1];
		var saymsg = spl2[2];
		gosayarray[c] = saymsg;
		gochararray[c] = new Array(sayname,saycolor);
	}
	start_dialogue(0,gosayarray,gochararray);
}

var talkspeed = 50;




var next_dialogue = 0;
var said = 0;
var sayable = true;
var dialogue_2 = new Array('Congratulations! You made it to the portal!','Thank you for playing the demo!');
var chararray_2 = new Array(1,1);
var dialogue_1 = new Array('Hello there!','This is a demo','Use the arrow keys and the A button to move and jump');
var chararray_1 = new Array(0,1,0);
var characterarrays = new Array(chararray_1,chararray_2);
var dialogues = new Array(dialogue_1,dialogue_2);
// var sayarray = dialogues[0];
var sayarray;
var characterarray;

var characters = new Array(new Array('Mr. Wtf','green'),new Array('Mr. Wtf','blue'));

var nextmsg = '<span class="nextmsg" oNclick="show_dialogue();">(A) Next</span>';
var newdialogue = '<span class="nextmsg" oNclick="start_dialogue(next_dialogue);">(A) Click</span>';
function start_dialogue(dialogue_num,v1,v2)
{
	if(in_dialogue == false)
	{
		moveable = false;
		in_dialogue = true;
		sayable = true;
		said = 0;
		
		characters = v2;
		sayarray = v1;
		document.getElementById('dialogue_box').style.visibility = 'visible';
		loops = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		loops_passed = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		inloops_passed = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		inloop = 1;
	}
	show_dialogue();
}

var loops = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
var loops_passed = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
var inloops_passed = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
var inloop = 1;

var in_dialogue = false;

var in_trade = false;

var go_choose = false;
var do_choose = false;


var set_after_completing_dialogue = 0;
var save_for_after_dialogue = 0;


function convert_current_message(input)
{
	go_choose = false;
	var output = '';
	var inputsplit = input.split(':multipilechoice:');
var questcheck = input.split('/quest ');
var checkqueststatus = input.split('/queststatus ');
var npcactioncheck = input.split('/npcaction ');
	if(inputsplit.length > 1)
	{
		// alert('dialogue');
		go_choose = true;
		current_choice = 1;
		for(var s in inputsplit)
		{
			if(s == 0)
			{
				output += inputsplit[s] + '<br>';
			}
			if(s != 0)
			{
				if(s == 1)
				{
					output += "<span id='choice_" + s + "' class='dialogue_multipile_choice' style='color: blue;'><img src='images/icons/selection_arrow.png' class='selection_arrow' id='choice_icon_" + s + "' style='visibility: visible;'>" + inputsplit[s] + "</span>";
				}
				else
				{
					output += "<span id='choice_" + s + "' class='dialogue_multipile_choice' style='color: black;'><img src='images/icons/selection_arrow.png' class='selection_arrow' id='choice_icon_" + s + "' style='visibility: hidden;'>" + inputsplit[s] + "</span>";
				}
			}
		}
	}
	else if(checkqueststatus.length > 1)
	{
		if(checkskip() == false)
		{

			handlequestid = checkqueststatus[1];
			// alert('handlequestid: '+handlequestid);
			questrequest.open("GET","quests/index.php?quest_status="+handlequestid,false);
			questrequest.send();

			current_choice = questrequest.responseText;
//alert('response test: '+current_choice);

			loops[(get_loop() + 1)] = current_choice;
			enter_next = false;

			loops_passed[get_loop()] = 1;
			inloops_passed[get_loop()] = 0;

			output = ':skip:';
		}
	}
	else if(questcheck.length > 1)
	{

		if(checkskip() == false)
		{
			// var handlequest;
			var questsplit = questcheck[1].split(';');
			handlequestid = questsplit[0];
			handlequestaction = questsplit[1];
			output = '/quest';
			questrequest.open("GET","quests/index.php?"+handlequestaction+"="+handlequestid,false);
			questrequest.send();

			var qressplit = questrequest.responseText.split(';');
			if((qressplit[0] == 'accept' || qressplit[0] == 'accomplish') && qressplit[2])
			{
				set_after_completing_dialogue = qressplit[2];
			}
			// alert(questrequest.responseText + ' - ' + qressplit[1]);
			output = qressplit[1];
		}

// set_after_completing_dialogue
	}
	else if(npcactioncheck.length > 1)
	{
		if(checkskip() == false)
		{
			// var handlequest;
			var actionid = npcactioncheck[1];
			var doarray = npc_action_arrays[actionid];
			if(doarray)
			{
				eval_after_dialogue = "start_npc_action(npc_action_arrays["+actionid+"]);";
			}
			output = ':skip:';
		}

// set_after_completing_dialogue
	}
	else if(input == '/shop' && checkskip() == false)
	{
		eval_after_dialogue = "open_iframe('shop','shopbox');";
		output = ':skip:';
	}
	else if(input == '/sell' && checkskip() == false)
	{
		eval_after_dialogue = "open_iframe('shop/sell.php','shopbox');";
		output = ':skip:';
	}
	else if(input == '/bank' && checkskip() == false)
	{
		eval_after_dialogue = "open_iframe('bank','shopbox');";
		output = ':skip:';
	}
	else
	{
		output = input;
	}
output = output.replace("Wanna", 'Want to');
//alert('a');
//alert('a: '+output);
	return output;
}


var current_choice = 0;
var enter_loop = 0;
var enter_next = false;

function multipile_choice(pn)
{
	var old_choice = current_choice;
	if(pn == 'next')
	{
		var new_choice = old_choice + 1;
	}
	else if(pn == 'prev')
	{
		var new_choice = old_choice - 1;
	}
	else if(pn == 'enter')
	{

		loops[(get_loop() + 1)] = current_choice;
		enter_next = false;
		loops_passed[get_loop()] = 1;
		inloops_passed[get_loop()] = 0;

	}

	if(pn != 'enter')
	{
		// alert('choice_'+new_choice + ' is de nieuwe keuze en ' + 'choice_'+old_choice+' is de oude');

		if(check_existance('choice_'+new_choice) == true && check_existance('choice_'+old_choice) == true)
		{
			document.getElementById('choice_icon_'+old_choice).style.visibility = 'hidden';
			document.getElementById('choice_'+old_choice).style.color = 'black';
			document.getElementById('choice_'+new_choice).style.color = 'blue';
			document.getElementById('choice_icon_'+new_choice).style.visibility = 'visible';
		}
		current_choice = new_choice;

		// check_existance(cplayer)
	}


}

function get_loop()
{
	for(onloop=10;onloop>=0;onloop=onloop-1)
	{
		if(loops[onloop] != 0)
		{
			return onloop;
		}
	}
	return 0;
}


function checkskip()
{
	if(get_loop() != (inloop - 1) || loops[get_loop()] != inloops_passed[(inloop - 1)])
	{
		// alert('realskip. getloop is ' + get_loop() + ' en (inloop - 1) is ' + (inloop - 1) + '. '+loops[get_loop()] + ' is niet ' + inloops_passed[(inloop - 1)]);
		return true;
	}
	return false;
}


var looping = false;
var skipnext = false;

var handlequestid = 0;
var handlequestaction;
var eval_after_dialogue = false;

function show_dialogue()
{
	if(sayable == true)
	{
		document.getElementById('dialogue_character_right').style.visibility = 'hidden';
		document.getElementById('dialogue_character_left').style.visibility = 'hidden';

		sayable = false;
		if(said < sayarray.length)
		{
			do_choose = false;
			document.getElementById('dialogue').innerHTML = '';
			var char = characters[said];
			var saystring = sayarray[said];
			saystring = convert_current_message(saystring);
			if(saystring == '/quest')
			{
				alert("Call to quest id: "+handlequestid+". Action: "+handlequestaction+".");
			}
			else if(saystring == '}')
			{
				inloop--;
				if(loops[inloop] == inloops_passed[inloop] && loops_passed[inloop] != 0 && get_loop() == inloop)
				{

					var got_loop = get_loop();
					loops[got_loop] = 0;
					loops_passed[got_loop] = 0;
				}
				else
				{
					
				}

				inloops_passed[(inloop+1)] = 0;
				
				said++;
				sayable = true;
				show_dialogue();
				return;
			}

			if(saystring == '{')
			{
				inloop++;
				
				var inloopmin1 = inloop - 1;
				inloops_passed[inloopmin1]++;

				
				if(loops[inloopmin1] == inloops_passed[inloopmin1] && loops_passed[inloopmin1] != 0 && get_loop() == inloopmin1)
				{
					// alert('{.Enter the next loop. Details: loops[inloopmin1] (' + loops[inloopmin1] + ') is inloops_passed[inloopmin1] (' + inloops_passed[inloopmin1] + '). get_loop() (' + get_loop() + ') is inloopmin1 (' + inloopmin1 + ').');
				}
				else
				{
					// alert('{.DO NOT ENTER NEXT LOOP. Details: loops[inloopmin1] (' + loops[inloopmin1] + ') is inloops_passed[inloopmin1] (' + inloops_passed[inloopmin1] + '). get_loop() (' + get_loop() + ') is inloopmin1 (' + inloopmin1 + ').');
				}

				said++;
				sayable = true;
				show_dialogue();
				return;

			}
			if(saystring == ':skip:')
			{
				said++;
				sayable = true;
				show_dialogue();
				return;
			}

			
			var inloopmin1 = inloop - 1;
				


			if(get_loop() != (inloop - 1) || loops[get_loop()] != inloops_passed[(inloop - 1)])
			{
				// alert('realskip. getloop is ' + get_loop() + ' en (inloop - 1) is ' + (inloop - 1) + '. '+loops[get_loop()] + ' is niet ' + inloops_passed[(inloop - 1)]);
				said++;
				sayable = true;
				show_dialogue();
				return;
			}


			document.getElementById('dialogue_character_right').style.visibility = 'hidden';
			document.getElementById('dialogue_character_left').style.visibility = 'hidden';

			var char0split = char[0].split('|');
			if(char0split.length == 3)
			{
				var chardirec = char0split[1];
				var charimage = char0split[2];
				if(chardirec == 'r')
				{
					document.getElementById('dialogue_character_right').style.visibility = 'visible';
					document.getElementById('dialogue_character_right').style.backgroundImage = "url(dialog_chars/"+charimage+")";
				}
				else
				{
					document.getElementById('dialogue_character_left').style.visibility = 'visible';
					document.getElementById('dialogue_character_left').style.backgroundImage = "url(dialog_chars/"+charimage+")";
				}
				char[0] = char0split[0];
			}

			document.getElementById('dialogue_character').innerHTML = '<font color="' + char[1]+ '">' + char[0] + '</font>: ';

			var saysplit = saystring.split('');
			var saytime = talkspeed;
			var sayend = false;
			for(var o in saysplit)
			{
				var msg = saysplit[o];
				// ~ 
				if(msg == '~' || sayend == true)
				{
					sayend = true;
				}
				else
				{
					if(msg == '"')
					{
						msg = '&quot;';
					}
					setTimeout("document.getElementById('dialogue').innerHTML = document.getElementById('dialogue').innerHTML + \""+msg+"\";",saytime);
					saytime = saytime + talkspeed;
					if(o == (saysplit.length - 1))
					{
						setTimeout("document.getElementById('dialogue').innerHTML = document.getElementById('dialogue').innerHTML + '"+nextmsg+"';",saytime);
					}
				}
			}
			if(sayend == true)
			{
				setTimeout("document.getElementById('dialogue').innerHTML = \"" + saystring.replace("~", '') + "\";",saytime);
				
				// maak said gelijk aan sayarray.length om in de said >= sayarray.length te komen
			}
			
			setTimeout("sayable = true;",saytime);
			said++;

			if(go_choose == true)
			{
				setTimeout("do_choose = true;",saytime);
				// setTimeout("alert('do choose!');",saytime);
			}
		}
		else if(said >= sayarray.length)
		{
			in_dialogue = false;
			do_choose = false;
			document.getElementById('dialogue_character').innerHTML = '';
			document.getElementById('dialogue').innerHTML = '-';
			moveable = true;
			sayable = true;
			document.getElementById('dialogue_box').style.visibility = 'hidden';
			if(eval_after_dialogue)
			{
				// Shop openen, bijvoorbeeld
				eval(eval_after_dialogue);
				eval_after_dialogue = false;
			}
			if(dialogue_feedback)
			{
				dialogue_feedback = false;
				setTimeout("npc_action("+dialogue_feedback_array+");",10);
			}
			if(set_after_completing_dialogue != 0 && save_for_after_dialogue != 0)
			{
//alert('set objects['+save_for_after_dialogue+'][4] to '+set_after_completing_dialogue);
				objects[save_for_after_dialogue][4] = set_after_completing_dialogue;
				//alert('done. objects['+save_for_after_dialogue+'][4] is now '+objects[save_for_after_dialogue][4]);
				save_for_after_dialogue = 0;
				set_after_completing_dialogue = 0;
			}
		}
	}
}
</script>


<?php //<div id='player_2' style='position: absolute; top:<?php echo $pos_top[1];?px; left: <?php echo $pos_left[1];>px; width: 20px; height: 30px; background-image: url("images/stand_front.gif"); z-index: <?php echo $pos_top[1];>; visibility: visible;'><div class='chattext' style='visibility: hidden;' id='chattext_2'>&nbsp;</div></div>?>
</div>
</div>
<div class='chat_bar' style='z-index: 0; visibility: visible;' id='sayblock'>
<div class='hotkey_holder' style='position: absolute; right: 5px; top: 5px; border: 1px outset gray; background-color: #F0F0F0;' onClick="use_hotkey();"><img id='hotkey' src='images/items/0.png'><div style='display: none;' id='durability_bar_hkey' class='durability_bar'><div id='in_durability_bar_hkey' class='in_durability_bar' style='background-color: lime; width: 90%;'></div></div></div><span id='menubutton' class='menubutton_normal' oNclick='toggle_menu();'>Menu</span><span class='show_chat' oNclick="showchat();">Chat <img src='images/ChatIcon.png'></span><div id='fitness_contain' style='position: relative; top: -15px; height: 15px; font-size: 12px; left: 110px; width: 90px; border: 1px solid black; background-color: yellow; z-index: 0;' oNclick="changewalkspeed();"><div id='fitness_bar' style='position: absolute; z-index: 1; background-color: lime; height: 100%; width: <?php echo $stamina;?>%;'>&nbsp;</div><div id='fitness_span' style='position: absolute; z-index: 2;'>Walking</div></div>
<div id='magic_contain' style='position: relative; top: -32px; height: 15px; font-size: 12px; left: 205px; width: 90px; border: 1px solid black; background-color: #F0F0F0; z-index: 0;'><div id='magic_bar' style='position: absolute; z-index: 1; background-color: aqua; height: 100%; width: <?php echo round($magic/$max_magic * 100);?>%;'>&nbsp;</div><div id='fitness_span' style='position: absolute; z-index: 2;'>Mana</div></div>
<img style='position: relative; right: 10px;' src='http://3dsplaza.com/apps/triniate_spriter/saved/8296/11/Saw.png'>
</div>
<div id='menu' style='visibility: hidden;'>
	<div id='menu_title'>Menu<div id='close_menu_icon' oNclick='toggle_menu();'></div></div>
	<div class='menu_option' onClick='toggle_menu(); show_screen("map");'>View map</div>
	<div class='menu_option' oNclick='toggle_menu(); get_inventory(); show_screen("inventory");'>Inventory</div>
	<div class='menu_option' oNclick='toggle_menu(); show_screen("charstats");'>Character stats</div>
	<div class='menu_option' oNclick='toggle_menu(); quit();'>Quit</div>
</div>
</div>

<div class='keyboard' style='visibility: hidden;' id='chat_bar'>


<div id='chat_text_bar' contenteditable="true"></div>
<table border="1" cellspacing="0" width="100%" height="160">
<tr>
<td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">`</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">1</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">2</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">3</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">4</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">5</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">6</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">7</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">8</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">9</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">0</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">-</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">+</td><td onclick="type('backspace');" name="backspace_button" class="keyboard_button_other" colspan="2">&larr;</td>
</tr>
<tr>
<td></td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">q</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">w</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">e</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">r</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">t</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">y</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">u</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">i</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">o</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">p</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">[</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">]</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">\</td><td></td>
</tr>
<tr>
<td onclick="capslock();" name="caps_button" class="keyboard_button_other" colspan="2" id='caps_button'>Caps</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">a</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">s</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">d</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">f</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">g</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">h</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">j</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">k</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">l</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">;</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">'</td><td onclick="sendpost();" name="enter_button" class="keyboard_button_other" colspan="2">Enter</td>
</tr>
<tr>
<td onclick="shift();" name="shift_button" class="keyboard_button_other" colspan="2" id='shift_button_1'>Shift</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">z</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">x</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">c</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">v</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">b</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">n</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">m</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">,</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">.</td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_normal">/</td><td onclick="shift();" name="shift_button" class="keyboard_button_other" colspan="3" id='shift_button_2'>Shift</td>
</tr>
<tr>
<td colspan="3"></td><td onclick="type(this.innerHTML);" name="caps_sensitive" class="keyboard_button_other" colspan="8">space</td><td colspan="4"></td>
</tr>
</table>

<span class='hide_chat' oNclick="hidechat();">Hide</span></div>

<div id='msgs' style='border: 1px solid black; background-color: brown; overflow-x: hidden; overflow-y: scroll; visibility: visible; z-index: 999998; position: absolute; top: 199px; left: 520px; height: 70px; width: 118px; font-size: 10px;'>Loading messages...</div>

<div id='player_stats' style='opacity:0.8; border: 1px solid black; background-color: white; overflow: hidden; visibility: hidden; z-index: 9999; position: absolute; top: 130px; left: 0px; height: 50px; width: 198px; font-size: 10px;'>Not in use</div>

<div id='inventory' style='visibility: hidden; z-index: 10000000000002;'><center><b>Inventory</b></center><div class='close_icon' onClick='close_screen("inventory");'></div>
<div id='inventory_box'>Loading...</div>
</div>

<div id='tradebox' style='visibility: hidden; z-index: 9999999999999;'><center><b>Trade</b></center><div class='close_icon' onClick='trading=false; abort_trade();'></div><br>
<table class='trade_table'>
<tr>
<td id='hide_ready_1'>
Offered coins: <span id='money_offered'>0</span>
</td>
<td id='hide_ready_2'>
<div id='i_offer_money' style='overflow-y: auto; overflow-x: hidden; height: 32px; width: 16px; background-color: yellow;' onscroll='document.getElementById("money_offered").innerHTML=(this.scrollTop/40);'><div id='in_offer_money' style='height: 0px; overflow: hidden; background-color: aqua; width: 30px; border: 1px solid black;'>&nbsp;</div></div>
</td>
</tr>
<tr><td id='i_offer'>You offer here</td><td id='you_offer'>Other offers</td></tr>
<tr><td colspan='2' id='trade_inventory'>Your inventory here</td></tr>
</table>
<input id='hide_ready_4' type='button' class='accept_trade_button' value='Ready' oNclick='accept_the_trade();'>
</div>

<div id='confirm_trade' style='visibility: hidden; z-index: 9999999999998;'><center><b>Confirm trade?</b></center><div class='close_icon' onClick='trading=false; abort_trade();'></div><br>
<table class='trade_table'>
<tr>
<tr><td id='i_offered'>You offer here</td><td id='you_offered'>Other offers</td></tr>
</table>
Are you sure you want to trade these items?<br>
<input type='button' class='accept_trade_button' value='Yes' oNclick='confirm_trade();' id='hide_ready_5'><input type='button' class='accept_trade_button' value='No' oNclick='abort_trade();'  id='hide_ready_6'>
</div>

<div id='requesting_trade' style='visibility: hidden; z-index: 9999999999999;'><center><b>Trade</b></center><div class='close_icon' onClick='abort_trade();'></div><br>
<span id='waiting_state' style='color: black;'>Waiting for the other player to accept or decline your request...</span><br>
<div value='Abort trade' class='abort_trade_button' oNclick='abort_trade();'>Abort trade</div>
</div>

<div id='accept_decline_trade' style='visibility: hidden; z-index: 9999999999998;'><center><b>Trade request</b></center><br>
... Invited you to trade. Accept or decline?<br>
<input type='button' value='Accept' class='accept_trade_button' oNclick='abort_trade();'> <input type='button' value='Abort trade' class='abort_trade_button' oNclick='abort_trade();'>
</div>

<iframe id='iframebox' class='craftbox' src='' style='visibility: hidden; z-index: 9999999999999;'>No iframe support.</iframe>


<input type="button" id='buttoninput' style='position: absolute; left: -16px; bottom: -16px; width: 1px; height: 1px; border: none; padding: 0; z-index: -1; line-height: 0; font-size: 0; -webkit-user-select: none;'>

<?php
/*
<input type="button" id='buttoninput' style='position: absolute; left: 100px; bottom: 100px; width: 20px; height: 20px; border: none; padding: 0; z-index: 1000000000000000000000; line-height: 20; font-size: 10; -webkit-user-select: none;' onClick='alert("radio hit");'>
*/
?>

<div id='map' style='visibility: hidden; z-index: 10000000000000;'>Map<div class='close_icon' onClick='toggle_screen("map");'></div><br>
<img style='border: 1px solid black;' <?php if($mapsize_height >= $mapsize_width){ echo 'height="140"';}elseif($mapsize_height < $mapsize_width){ echo 'width="270"';}?> src='<?php echo $background;?>'>
</div>
<div id='charstats' style='visibility: hidden; z-index: 10000000000001;'>
<center><span id='charstats_title'>Character stats</span></center><div class='close_icon' onClick='toggle_screen("charstats");'></div>
<br>
Your nickname: <?php echo $player_name;?><br>
Your health: <span id='my_health'><?php echo $health;?></span><br>
Your magic: <span id='my_magic'><?php echo $magic;?></span><br>
Level: <span id='my_level'>Loading...</span>&nbsp;|&nbsp;
Attack: <span id='my_attack'>Loading...</span>&nbsp;|&nbsp;
Defense: <span id='my_defense'>Loading...</span><br>
Max HP: <span id='my_max_hp'>Loading...</span><br>
<div style='height: 18px; overflow: hidden;'>EXP: <div style='width: 200px; height: 15px; position: relative; top: -18px; left: 30px; background-color: white; border-radius: 10px; border: 1px solid black;'>

<div id='exp_bar' style='position: relative; top: 0px; left: 0px; width: 1%; height: 15px; background-color: aqua; border-radius: 8px;'> </div>
<span id='my_exp' style='color: black; position: relative; top: -15px; left: 5px;'>Loading...</span>
</div></div>
<a href='customize/build.php'>customize character</a>
</div>

<div id='special_object_waiting' style='z-index: 999998; display: none; position: absolute; top: 150px; left; 10px; width: 216px; height: 18px; padding: 5px; background-color: white; border: 2px solid black;'><div id='special_object_waiting_bar' style='position: absolute; top: 0px; left: 0px; width: 100%; height: 28px; background-color: red; z-index: 0;'>&nbsp;</div><span style='z-index: 1;'>Mining object...</span></div>

<div id='dialogue_box' style='position: absolute; top: 188px; left: 0px; border: 2px outset brown; width: 632px; padding: 2px; height: 74px; background-color: orange; z-index: 999999; font-size: 14px; visibility: hidden;'>
<span id='dialogue_character'></span>
<span id='dialogue' style='white-space: normal;'><font color='red'>Press the A button</font><script>document.write(newdialogue);</script></span>
</div>
<div id='dialogue_character_left' style='visibility: visible; z-index: 999999;'>
</div>

<div id='dialogue_character_right' style='visibility: visible; z-index: 999999;'>
</div>

</div>
</body>
<script>
// This still works:
// start_dialogue(0,new Array('Welcome to this... "game"','Use the arrow keys and the A button to... figure it out yourself -__-'),new Array(new Array('Narrator','red'),new Array('Narrator','red')));

var walk_away_array = Array(Array(1,'walk',100,100,sprite_move_down['npc_'+1],'absolute'), Array(1,'warp','coords',0,-100),Array(1,'wait',5000),Array(1,'warp','coords',100,100),Array(1,'walk',100,15,sprite_move_up['npc_'+1],'absolute'),Array(1,'blockmoving'),Array(1,'touser','npc_'+1,'right'),Array(1,'changesprite',sprite_left['npc_'+1]), Array(0,'changesprite',sprite_right[my_character]),Array(1,'unblockmoving'),Array(1,'conversation','Mark|l|2/normal.png#blue#Just kidding. But please leave our shop, because we do not accept this kind of attitude here.@*you*|r|1/normal_inverted.png#blue#Okay... Sorry, I will leave for now'),Array(1,'blockmoving'),Array(0,'walk',100,80,sprite_move_down[my_character],'absolute'),Array(0,'redirect','switchrooms.php?newroom=3&inoption=3'));


var you_walk_away_array = Array(Array(1,'blockmoving'),Array(0,'walk',100,80,sprite_move_down[my_character],'absolute'),Array(0,'redirect','switchrooms.php?newroom=3&inoption=3'));
var npc_action_arrays = new Array(walk_away_array,you_walk_away_array);
</script>


<script>document.getElementById('maincontain').scrollLeft = <?php echo $viewport_left - 300;?>; document.getElementById('maincontain').scrollTop = <?php echo $viewport_top - 125;?>;</script>
</html>