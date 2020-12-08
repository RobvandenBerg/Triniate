<?php

include("functions/db_info.php");

mysql_connect($dbhost,$dbuser,$dbpass) or die(mysql_error());

mysql_select_db($db) or die(mysql_error());


$select_villains_request = mysql_query("SELECT id,pos_left,pos_top,sprite,message,health,player_name,player_type,last_health_update,last_time_update,walkzone,followzone,following,walkcoord, agressive, level, attack, defense, max_hp, drop_item, quantity from villains where room='$room'") or die('error: ' . mysql_error());


$select_villains_number = mysql_num_rows($select_villains_request);

for($m = 0; $m < $select_villains_number; $m++)
{
	$svr = mysql_fetch_row($select_villains_request);
	$id = $svr[0];
	$pos_left = $svr[1];
	$pos_top = $svr[2];
	$sprite = $svr[3];
	$message = $svr[4];
	$health = $svr[5];
	$name = $svr[6];
	$player_type = $svr[7];
	$last_health_update = $svr[8];
	$last_time_update = $svr[9];
	$walkzone = $svr[10];
	$followzone = $svr[11];
	$following = $svr[12];
	$walkcoord = $svr[13];
	$agressive = $svr[14];
	$level = $svr[15];
	$attack = $svr[16];
	$defense = $svr[17];
	$max_hp = $svr[18];
	$drop_item = $svr[19];
	$quantity = $svr[20];

$addtime = 0;
$attacked = false;
if($sprite == 'sprite_attack_up' or $sprite == 'sprite_attack_down' or $sprite == 'sprite_attack_left' or $sprite == 'sprite_attack_right')
{
$attacked = true;
}

$dead = false;
$do_die = false;

if($last_time_update < (micro_time() - 0.50))
{
$do_die = true;
}
if($sprite == 'sprite_dead')
{
       $dead = true;
}
if($last_time_update < (micro_time() - 0.50) && $dead == false)
{
if($sprite == 'sprite_hurt_left')
{
       $sprite = 'sprite_attack_right';
}
elseif($sprite == 'sprite_hurt_right')
{
       $sprite = 'sprite_attack_left';
}
elseif($sprite == 'sprite_hurt_up')
{
       $sprite = 'sprite_attack_down';
}
elseif($sprite == 'sprite_hurt_down')
{
       $sprite = 'sprite_attack_up';
}
/*
elseif($sprite == 'sprite_move_left')
{
   $pos_left = $pos_left - 2;
   if($pos_left < 10)
   {
       $pos_left = $pos_left + 4;
       $sprite = 'sprite_move_right';
   }
}
else
{
   $sprite = 'sprite_move_right';
   $pos_left = $pos_left + 2;
   if($pos_left > 200)
   {
       $pos_left = $pos_left - 4;
       $sprite = 'sprite_move_left';
   }
}*/

if($last_health_update < (time() - 20) && $health <= $max_hp)
{
   $health = $health + 5;
   if($health > $max_hp)
   {
      $health = $max_hp;
   }
   $last_health_update = time();
}



// ------ ZONE SCRIPT
$newcoords = get_position($walkcoord,$pos_left . ',' . $pos_top,3);
$following_somebody = false;
if($following != 0)
{
	foreach($players as $player)
	{
		if($player[0] == $following)
		{
			$following_somebody = true;
			$followcoords = get_position($player[1] . ',' . $player[2],$pos_left . ',' . $pos_top,5,20);
			$folleft = $player[1];			
			$foltop = $player[2];
		}
	}
}



$followzonesplit = explode(',',$followzone);
$fzone = $followzonesplit;

$walkzonesplit = explode(',',$walkzone);
$wzone = $walkzonesplit;

if($following_somebody == false)
{
	$followable = array();
	$closest_distance = 1000000;
	$closest_player = array(0,1,1);
	foreach($players as $player)
	{
		$x1 = $player[1];
		$y1 = $player[2];
		$diagonal_distance = get_diagonal($x1,$y1,$pos_left,$pos_top);
		if($diagonal_distance < $closest_distance)
		{
			$closest_distance = $diagonal_distance;
			$closest_player = $player;
		}
	}

	if($closest_player[0] != 0)
	{
		$folleft = $closest_player[1];
		$foltop = $closest_player[2];
		
		if($folleft > $wzone[0] && $folleft < $wzone[2] && $foltop > $wzone[1] && $foltop < $wzone[3])
		{
			// --- De speler bevindt zich in de walkzone van de vijand
			$gofollow = rand(0,1000);
		}
		if($folleft > $fzone[0] && $folleft < $fzone[2] && $foltop > $fzone[1] && $foltop < $fzone[3])
		{
			// --- De speler bevindt zich in de followzone van de vijand
			$gofollow = rand(0,5000);
		}
	}

	if($gofollow < $agressive)
	{
		$following = $closest_player[0];
		$foltop = $closest_player[2];
		$folleft = $closest_player[1];
		$followcoords = get_position($folleft . ',' . $foltop,$pos_left . ',' . $pos_top,5,20);

		$following_somebody = true;
	}
	
}

if($following_somebody == true && $folleft > $fzone[0] && $folleft < $fzone[2] && $foltop > $fzone[1] && $foltop < $fzone[3])
{
	// Er wordt iemand gevolgd én de gevolgde speler is in de follow zone
	$newcoords = $followcoords;
}
else
{
	// Er word niemand gevolgd of er de gevolgde speler is niet in de follow zone
	// In ieder geval moet de vijand dus niet volgen
	
	$following = 0;
	$following_somebody = false;
	
	if($pos_left > $wzone[0] && $pos_left < $wzone[2] && $pos_top > $wzone[1] && $pos_top < $wzone[3])
	{
		//$sprite = 'sprite_down';
		// in the walk zone
	}
	else
	{
		// out of the walk zone
		$walkcoord = rand(($wzone[0]+1),($wzone[2]-1)) . ',' . rand(($wzone[1] + 1),($wzone[3] - 1));
		// $sprite = 'sprite_up';
	}
}






if(count($newcoords) > 2)
{
	// Bestemming nog niet bereikt
	$pos_left = $newcoords[0];
	$pos_top = $newcoords[1];
	$sprite = $newcoords[2];
}
else
{
	// Bestemming bereikt
	$walkcoord = rand(($wzone[0]+1),($wzone[2]-1)) . ',' . rand(($wzone[1] + 1),($wzone[3] - 1));
	if($following_somebody)
	{
		$sprite = $newcoords[0];
		if($attacked == false)
		{
			$sprite_split = explode('_',$sprite);
			$sprite = $sprite_split[0] . '_attack_' . $sprite_split[1];
			$addtime = 1;
		}
	}
}


// ------ END OF ZONE SCRIPT












$update_villains_request = mysql_query("UPDATE villains set health='$health',last_health_update='$last_health_update', pos_left='$pos_left',pos_top='$pos_top',sprite='$sprite', walkcoord='$walkcoord', following='$following', last_time_update='".(micro_time() + $addtime)."' where id='$id'") or die('error: ' . mysql_error());
}
	$health_percentage = $health/$max_hp * 100;
	echo ";$id,$pos_left,$pos_top,$sprite,$message,$name,$health_percentage,$player_type,$last_health_update";
       
       if($dead == true && $do_die == true)
       {
		/*$myFile = "do.txt";
		$fh = fopen($myFile, 'a') or die("can't open file");
		fwrite($fh, "Villain died. $last_time_update is smaller than ". (micro_time() - 0.50) ."\n");
	        fclose($fh);*/
                $die_request = mysql_query("DELETE from villains where id='$id'") or die('error: ' . mysql_error());
		
		if($drop_item != 0)
		{
			$die_request = mysql_query("INSERT into lying_items (item_id,room,position,dropped_time,quantity) VALUES ('$drop_item','$room','" . ($pos_left + 2) . ",". ($pos_top + 15) . "','". (time() + 20) . "','$quantity')") or die('error: ' . mysql_error());
		}
       }
}

// 0 = id, 1 = top pos, 2 = left pos, 3 = sprite, 4 = message, 5 = player name, 6 = health, 7 = player type, 8 = last time update
?>