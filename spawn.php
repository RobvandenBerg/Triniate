<?php


if(isset($_GET['display']))
{
	include('include_this.php');
	light_login();
	mysql_pconnect($dbhost,$dbuser,$dbpass);
	mysql_select_db($db);
}

// $room = 1;
$myFile = 'rooms/' . $room . '.txt';
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);
$settingsexplode = explode(';',$theData);
$enemies_raw = $settingsexplode[4];

$enemies_split = explode('/',$enemies_raw);

$max_enemies = $enemies_split[0];
$zones_raw = $enemies_split[1];
$zones_split = explode('-',$zones_raw);
$levels = $enemies_split[2];
$evs = $enemies_split[3];
$names = $enemies_split[4];
$drop_items = $enemies_split[5];
$items_split = explode("_",$drop_items);
$names_split = explode('_',$names);
$agressivities = $enemies_split[6];
$agressivities_split = explode("-",$agressivities);
$agressivities_split = explode(",",$agressivities_split[0]);


$select_enemies_request = mysql_query("SELECT id from villains where room='$room' and visible='0'") or die('error');
$select_enemies_number = mysql_num_rows($select_enemies_request);

if(isset($_GET['display']))
{
	$select_enemies_number--;
}

for($m = 0; $m < ($max_enemies - $select_enemies_number); $m++)
{
$total_zones = count($zones_split) - 1;
$pick_zone = rand(0,$total_zones);
$rawzone = $zones_split[$pick_zone];
$divide_zones = explode('_',$rawzone);
$walkzone = $divide_zones[0];
$followzone = $divide_zones[1];

$walkzone_split = explode(',',$walkzone);
//$pos_left = round( ($walkzone_split[2] + $walkzone_split[0]) / 2);
// $pos_top = round(($walkzone_split[3] + $walkzone_split[1]) / 2);

$pos_left = rand($walkzone_split[0],$walkzone_split[2]);
$pos_top = rand($walkzone_split[1],$walkzone_split[3]);

$myFile = "do.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, "Add number 1:\nPos left: $pos_left\nPos top: $pos_top\n\n");
fclose($fh);

$levelsplit = explode('-',$levels);
$level = rand($levelsplit[0],$levelsplit[1]);

$evs_split = explode('-',$evs);
$ev = rand($evs_split[0],$evs_split[1]);

$attack = $level * 10 + $ev;
$defense = $level * 10 + $ev;
$health = 47 + $level * 3 + round($ev/3);

$total_names = count($names_split) - 1;
$pickname = rand(0,$total_names);
$name = $names_split[$pickname];
$name = $name . " ($level)";

$agressivity_min = (int)$agressivities_split[0];
$agressivity_max = (int)$agressivities_split[1];

$agressive = rand($agressivity_min,$agressivity_max);
if(!is_numeric($agressive))
{
	$agressive = rand(0,10);
}
$total_items = count($items_split) - 1;
$pickitem = rand(0,$total_items);
$picked_itemraw = $items_split[$pickitem];
$itemraw_split = explode("-",$picked_itemraw);
$picked_item = $itemraw_split[1];
$picked_item_chance = $itemraw_split[0];

$chancenum = rand(0,100);
if($chancenum < $picked_item_chance)
{
$drop_item = $picked_item;
if($drop_item == 3)
{
$quantity = $itemraw_split[2];
}
else
{
$quantity = 1;
}
}
else
{
$drop_item = 0;
$quantity = 1;
}

$myFile = "do.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, "agressivity: $agressive");
fclose($fh);

// $agressive = rand(0,100);


$insert_enemy_request = mysql_query("INSERT into villains (room,pos_top,pos_left,sprite,player_name,health,walkzone,followzone,walkcoord,agressive,level,attack,defense,max_hp,drop_item,quantity) VALUES ('$room','$pos_top','$pos_left','sprite_down','$name','$health','$walkzone','$followzone','$pos_left,$pos_top','$agressive','$level','$attack','$defense','$health','$drop_item','$quantity')") or die('error: ' . mysql_error());
if(isset($_GET['display']))
{
	mysql_close();
	echo "INSERT into villains (room,pos_top,pos_left,sprite,player_name,health,walkzone,followzone,walkcoord,agressive,level,attack,defense,max_hp,drop_item,quantity) VALUES ('$room','$pos_top','$pos_left','sprite_down','$name','$health','$walkzone','$followzone','$pos_left,$pos_top','$agressive','$level','$attack','$defense','$health','$drop_item','$quantity')";
}

}


?>
