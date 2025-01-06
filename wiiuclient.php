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

// Session's already started
// session_start();
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
Triniate - Wii U client
</title>
<script>
// -- OBJECTS SCRIPT
var objects = new Array;
</script>
<script>
var room = <?php echo round($room);?>;
var player_id = '<?php echo $player_id;?>';
var player_name = "<?php echo $player_name;?>";
var wallsarray = new Array(<?php echo $converted_wallsarray;?>);
var currentsprite = '<?php echo $my_sprite;?>';
var mapsize_width = <?php echo $mapsize_width;?>;
var mapsize_height = <?php echo $mapsize_height;?>;
var fitness = <?php echo $stamina;?>;
var magic_value = <?php echo $magic;?>;
var coords = '<?php echo $my_left_pos . "," . $my_top_pos;?>';
var pos = {x: <?php echo $my_left_pos;?>, y: <?php echo $my_top_pos;?>};
var SYSTEM = '<?php echo detect_system();?>';
var viewportLeft = <?php echo round($viewport_left) - 300;?>;
var viewportTop = <?php echo round($viewport_top) - 125;?>;
var viewportWidth = 640;
var viewportHeight = 270;
var viewport = 320;
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
<?php
for($m = 0; $m < count($objects); $m++)
{
	$object = $objects[$m];
	echo "objects[$m] = new Array(\"$object[0]\",\"$object[1]\",\"$object[2]\",\"$object[3]\",\"$object[4]\",\"$object[5]\",\"$object[6]\");\n";
}
?>
// alert(objects);
// -- OBJECTS SCRIPT END

</script>
<script type='text/javascript' src='scripts/main.js?r=<?php echo rand(0,10000);?>'></script>
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
<script type='text/javascript' src='scripts/inventory.js'></script>


<?php
// END INVENTORY SCRIPTS
?>








<script type='text/javascript' src='scripts/keyboard.js'></script>
<style>
body {
	image-rendering: pixelated;
}
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
window.onload = function(){setTimeout('update_position();',1000); var iets = setTimeout('var el = document.getElementById(\'loading_span\'); el.parentNode.removeChild(el); el = document.getElementById(\'load_screen\'); el.parentNode.removeChild(el); document.getElementById(\'mainscreen\').style.visibility = \'visible\';',2000); get_character_stats(player_id); document.getElementById('buttoninput').focus(); /*setInterval("check_b_button();",20);*/
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

</head>
<body>
<div id='container'>
<span id='loading_span' style='visibility: visible;'><br>Loading game... <img src='images/loading.gif'></span>
<div class='load_screen' style='visibility: visible; z-index: 1000000;' id='load_screen'>&nbsp;</div>
<div class='main_contain' id='maincontain'>
<div class='main_screen' style='visibility: hidden;' id='mainscreen'>
<div id='player_<?php echo $player_id;?>' style='position: absolute; top:<?php echo $my_top_pos;?>px; left: <?php echo $my_left_pos;?>px; width: 20px; height: 30px; background-image: url(); z-index: <?php echo ($my_top_pos+30);?>; visibility: visible;'><img style='position: absolute; top: 0px; width: 20px; height: 30px;' src='customize/saved/<?php echo $player_id; ?>/<?php echo $my_sprite; ?>.gif' id='player_<?php echo $player_id;?>_sprite'><div class='player_name' style='visibility: visible;' id='player_name_<?php echo $player_id;?>'><div id='health_player_<?php echo $player_id;?>' class='health_bar' style='width: <?php echo $health;?>%;'>&nbsp;</div><?php echo $player_name;?></div><div class='chattextcontain'><div class='chattext' style='display: none;' id='chattext_<?php echo $player_id;?>'>&nbsp;</div></div></div>
<script>change_sprite('player_' + player_id + '_sprite',<?php echo $my_sprite;?>[<?php echo $player_id;?>].src);</script>



<script type='text/javascript' src='scripts/objects.js'></script>


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



<script type='text/javascript' src='scripts/dialogue.js'></script>


<?php //<div id='player_2' style='position: absolute; top:<?php echo $pos_top[1];?px; left: <?php echo $pos_left[1];>px; width: 20px; height: 30px; background-image: url("images/stand_front.gif"); z-index: <?php echo $pos_top[1];>; visibility: visible;'><div class='chattext' style='display: none;' id='chattext_2'>&nbsp;</div></div>?>
</div>
</div>
<div class='chat_bar' style='z-index: 0; visibility: visible;' id='sayblock'>
<div class='hotkey_holder' style='position: absolute; right: 5px; top: 5px; border: 1px outset gray; background-color: #F0F0F0;' onClick="use_hotkey();"><img id='hotkey' src='images/items/0.png'><div style='display: none;' id='durability_bar_hkey' class='durability_bar'><div id='in_durability_bar_hkey' class='in_durability_bar' style='background-color: lime; width: 90%;'></div></div></div><span id='menubutton' class='menubutton_normal' oNclick='toggle_menu();'>Menu</span><span class='show_chat' oNclick="showchat();">Chat <img src='images/ChatIcon.png'></span><div id='fitness_contain' style='position: relative; top: -15px; height: 15px; font-size: 12px; left: 110px; width: 90px; border: 1px solid black; background-color: yellow; z-index: 0;' oNclick="changewalkspeed();"><div id='fitness_bar' style='position: absolute; z-index: 1; background-color: lime; height: 100%; width: <?php echo $stamina;?>%;'>&nbsp;</div><div id='fitness_span' style='position: absolute; z-index: 2;'>Walking</div></div>
<div id='magic_contain' style='position: relative; top: -32px; height: 15px; font-size: 12px; left: 205px; width: 90px; border: 1px solid black; background-color: #F0F0F0; z-index: 0;'><div id='magic_bar' style='position: absolute; z-index: 1; background-color: aqua; height: 100%; width: <?php echo round($magic/$max_magic * 100);?>%;'>&nbsp;</div><div id='fitness_span' style='position: absolute; z-index: 2;'>Mana</div></div>
<img style='position: relative; right: 10px;' src='http://3dsplaza.com/apps/triniate_spriter/saved/8296/11/Saw.png'>
</div>
<div id='menu' style='display: none;'>
	<div id='menu_title'>Menu<div id='close_menu_icon' oNclick='toggle_menu();'></div></div>
	<div class='menu_option' onClick='toggle_menu(); show_screen("map");'>View map</div>
	<div class='menu_option' oNclick='toggle_menu(); get_inventory(); show_screen("inventory");'>Inventory</div>
	<div class='menu_option' oNclick='toggle_menu(); show_screen("charstats");'>Character stats</div>
	<div class='menu_option' oNclick='toggle_menu(); quit();'>Quit</div>
</div>
</div>

<div class='keyboard' style='display: none;' id='chat_bar'>


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

<div id='player_stats' style='opacity:0.8; border: 1px solid black; background-color: white; overflow: hidden; display: none; z-index: 9999; position: absolute; top: 130px; left: 0px; height: 50px; width: 198px; font-size: 10px;'>Not in use</div>

<div id='inventory' style='display: none; z-index: 10000000000002;'><center><b>Inventory</b></center><div class='close_icon' onClick='close_screen("inventory");'></div>
<div id='inventory_box'>Loading...</div>
</div>

<div id='tradebox' style='display: none; z-index: 9999999999999;'><center><b>Trade</b></center><div class='close_icon' onClick='trading=false; abort_trade();'></div><br>
<table class='trade_table'>
<tr>
<td id='hide_ready_1'>
Offered coins: <span id='money_offered'>0</span>
</td>
<td id='hide_ready_2'>
<div id='i_offer_money' style='overflow-y: auto; overflow-x: hidden; height: 32px; width: 16px; background-color: yellow;' onscroll='document.getElementById("money_offered").innerHTML=Math.floor(this.scrollTop/40);'><div id='in_offer_money' style='height: 0px; overflow: hidden; background-color: aqua; width: 30px; border: 1px solid black;'>&nbsp;</div></div>
</td>
</tr>
<tr><td id='i_offer'>You offer here</td><td id='you_offer'>Other offers</td></tr>
<tr><td colspan='2' id='trade_inventory'>Your inventory here</td></tr>
</table>
<input id='hide_ready_4' type='button' class='accept_trade_button' value='Ready' oNclick='accept_the_trade();'>
</div>

<div id='confirm_trade' style='display: none; z-index: 9999999999998;'><center><b>Confirm trade?</b></center><div class='close_icon' onClick='trading=false; abort_trade();'></div><br>
<table class='trade_table'>
<tr>
<tr><td id='i_offered'>You offer here</td><td id='you_offered'>Other offers</td></tr>
</table>
Are you sure you want to trade these items?<br>
<input type='button' class='accept_trade_button' value='Yes' oNclick='confirm_trade();' id='hide_ready_5'><input type='button' class='accept_trade_button' value='No' oNclick='abort_trade();'  id='hide_ready_6'>
</div>

<div id='requesting_trade' style='display: none; z-index: 9999999999999;'><center><b>Trade</b></center><div class='close_icon' onClick='abort_trade();'></div><br>
<span id='waiting_state' style='color: black;'>Waiting for the other player to accept or decline your request...</span><br>
<div value='Abort trade' class='abort_trade_button' oNclick='abort_trade();'>Abort trade</div>
</div>

<div id='accept_decline_trade' style='display: none; z-index: 9999999999998;'><center><b>Trade request</b></center><br>
... Invited you to trade. Accept or decline?<br>
<input type='button' value='Accept' class='accept_trade_button' oNclick='abort_trade();'> <input type='button' value='Abort trade' class='abort_trade_button' oNclick='abort_trade();'>
</div>

<iframe id='iframebox' class='craftbox' src='' style='display: none; z-index: 9999999999999;'>No iframe support.</iframe>


<input type="button" id='buttoninput' style='position: absolute; left: -16px; bottom: -16px; width: 1px; height: 1px; border: none; padding: 0; z-index: -1; line-height: 0; font-size: 0; -webkit-user-select: none;'>

<?php
/*
<input type="button" id='buttoninput' style='position: absolute; left: 100px; bottom: 100px; width: 20px; height: 20px; border: none; padding: 0; z-index: 1000000000000000000000; line-height: 20; font-size: 10; -webkit-user-select: none;' onClick='alert("radio hit");'>
*/
?>

<div id='map' style='display: none; z-index: 10000000000000;'>Map<div class='close_icon' onClick='toggle_screen("map");'></div><br>
<img style='border: 1px solid black;' <?php if($mapsize_height >= $mapsize_width){ echo 'height="140"';}elseif($mapsize_height < $mapsize_width){ echo 'width="270"';}?> src='<?php echo $background;?>'>
</div>
<div id='charstats' style='display: none; z-index: 10000000000001;'>
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

<div id='dialogue_box' style='position: absolute; top: 188px; left: 0px; border: 2px outset brown; width: 632px; padding: 2px; height: 74px; background-color: orange; z-index: 999999; font-size: 14px; display: none;'>
<span id='dialogue_character'></span>
<span id='dialogue' style='white-space: normal;'><font color='red'>Press the A button</font><script>document.write(newdialogue);</script></span>
</div>
<div id='dialogue_character_left' style='display: none; z-index: 999999;'>
</div>

<div id='dialogue_character_right' style='display: none; z-index: 999999;'>
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