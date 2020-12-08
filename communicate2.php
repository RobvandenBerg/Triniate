<?php

include("include_this.php");

light_login();


mysql_pconnect($dbhost,$dbuser,$dbpass) or die('error');
mysql_select_db($db) or die('error');



if(isset($_POST['position']) && isset($_POST['sprite']))
{
	
	$time = time();
	$position = htmlentities($_POST['position']);
	$sprite = htmlentities($_POST['sprite']);
	if(is_numeric($_POST['stamina']) && $_POST['stamina'] >= 0 && $_POST['stamina'] <= 100)
	{
		$stamina = $_POST['stamina'];
	}
	else
	{
		$stamina = 0;
	}


	$posi_split = explode(",",$position);
	$cpos_left = $posi_split[0];
	$cpos_top = $posi_split[1];

	$min_left = $cpos_left - 200;
	$min_top = $cpos_top - 180;
	$max_left = $cpos_left + 200;
	$max_top = $cpos_top + 180;

}
else
{
	die('no post values found');
}
$select_time = time() - 60;
$room = get_room();


$extra_array = array();
if(isset($_POST['extra_0']) && !empty($_POST['extra_0']))
{
	$extra_array[count($extra_array)] = $_POST['extra_0'];
}
if(isset($_POST['extra_1']) && !empty($_POST['extra_1']))
{
	$extra_array[count($extra_array)] = $_POST['extra_1'];
}
if(isset($_POST['extra_2']) && !empty($_POST['extra_2']))
{
	$extra_array[count($extra_array)] = $_POST['extra_2'];
}
if(isset($_POST['extra_3']) && !empty($_POST['extra_3']))
{
	$extra_array[count($extra_array)] = $_POST['extra_3'];
}
if(isset($_POST['extra_4']) && !empty($_POST['extra_4']))
{
	$extra_array[count($extra_array)] = $_POST['extra_4'];
}


// ---------------------
$select_my_stats = mysql_query("SELECT flagtime, flag_left, flag_top,  attack,defense,health,magic,max_hp,max_magic,exp,exp_woodcutting,exp_mining,item_equipped,stamina,lastmove from position where id='$player_id'") or die('error: ' . mysql_error());

$sms = mysql_fetch_array($select_my_stats);
$flag_left = $sms['flag_left'];
$flag_top = $sms['flag_top'];
$flagtime = $sms['flagtime'];
$my_attack = $sms['attack'];
$my_defense = $sms['defense'];
$my_health = $sms['health'];
$my_magic = $sms['magic'];
$my_max_hp = $sms['max_hp'];
$my_max_magic = $sms['max_magic'];
$my_exp = $sms['exp'];
$level_handle = get_level($my_exp);
$my_level = $level_handle[0];
$item_equipped = $sms['item_equipped'];
$exp_woodcutting = $sms['exp_woodcutting'];
$exp_mining = $sms['exp_mining'];
$old_lastmove = $sms['lastmove'];

$time = time();
$flagdiff = $time - $flagtime;
$setflag = false;
$reset_left = 0;
$reset_top = 0;
$force_reset = false;
if($flagdiff >= 5)
{
	$setflag = true;
	$distance_travelled = abs($cpos_left - $flag_left) + abs($cpos_top - $flag_top) - 5;
	$max_distance_travelled = 50;

	if(($distance_travelled/$flagdiff) > $max_distance_travelled)
	{
		$cpos_left = $flag_left;
		$cpos_top = $flag_top;
		$force_reset = true;
		$reset_left = $flag_left;
		$reset_top = $flag_top;
	}
}

// $stamina = $sms['stamina'];

$explode_set_sprite = explode('_',$sprite);
if($explode_set_sprite[3] && $explode_set_sprite[3] == 'mana')
{
	foreach($extra_array as $extra_string)
	{
		// $extra_explode = explode("_",$_POST['extra_0']);
		$extra_explode = explode("_",$extra_string);
		if($extra_explode[0] == 'mana')
		{
			if($my_magic < 20)
			{
				$sprite = $explode_set_sprite[0] . '_' . $explode_set_sprite[2];
			}
		}
	}
}

if($setflag)
{
	// file_put_contents('do.txt','UPDATE position set pos=\''.$position.'\', pos_left=\''.$cpos_left.'\', pos_top=\''.$cpos_top.'\', sprite=\''.$sprite.'\', lastmove=\''.$time.'\',stamina=\''.$stamina.'\', flagtime=\''.$time.'\', flag_left=\''.$cpos_left.'\', flag_top=\''.$cpos_top.'\' where id=\''.$player_id.'\'');
	$update_position_request = mysql_query('UPDATE position set pos=\''.$position.'\', pos_left=\''.$cpos_left.'\', pos_top=\''.$cpos_top.'\', sprite=\''.$sprite.'\', lastmove=\''.$time.'\',stamina=\''.$stamina.'\', flagtime=\''.$time.'\', flag_left=\''.$cpos_left.'\', flag_top=\''.$cpos_top.'\' where id=\''.$player_id.'\'') or die(mysql_error());
}
else
{
	$update_position_request = mysql_query('UPDATE position set pos=\''.$position.'\', pos_left=\''.$cpos_left.'\', pos_top=\''.$cpos_top.'\', sprite=\''.$sprite.'\', lastmove=\''.$time.'\',stamina=\''.$stamina.'\' where id=\''.$player_id.'\'') or die(mysql_error());
}

$item_equipped_id = 0;
$durability_percentage = 0;
if($item_equipped != 0)
{
	$select_item_request = mysql_query("SELECT inv.item_id, it.durability, inv.durability from inventories as inv, items as it where inv.id='$item_equipped' and inv.belongs_to='$player_id' and it.id=inv.item_id") or die('error: '.mysql_error());
	$select_item_rows = mysql_num_rows($select_item_request);
	if($select_item_rows == 1)
	{
		$select_item_array = mysql_fetch_array($select_item_request);
		$item_equipped_id = $select_item_array['item_id'];
		$it_durability = $select_item_array[1];
		$inv_durability = $select_item_array[2];
		if($it_durability != 0 && $inv_durability != 0)
		{
			$durability_percentage = 100 - floor($inv_durability/$it_durability * 100);
		}
	}
	else
	{
		// Het "equipped" item bestaat niet meer, misschien is de axe ofzo gebroken. Unset het equipped item in ieder geval
		$item_equipped = 0;
		$update_request = mysql_query("UPDATE position set item_equipped='0' where id='$player_id'") or die('error: '.mysql_error());
	}
}

/*$myFile = "do.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, "o.o\n");
fclose($fh);*/


// -- START HANDLE EXTRA'S CODE --
$lower_health = 0;


//if(isset($_POST['extra_0']) && !empty($_POST['extra_0']))
foreach($extra_array as $extra_string)
{
	// $extra_explode = explode("_",$_POST['extra_0']);
	$extra_explode = explode("_",$extra_string);
	if($extra_explode[0] == 'use')
	{
		$inventory_id = htmlentities($extra_explode[1]);
$myFile = "do.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, "gotta use item $inventory_id\n");
fclose($fh);
		$select_item_request = mysql_query("SELECT item_id from inventories where id='$inventory_id'") or die(mysql_error());
		if(mysql_num_rows($select_item_request) == 1)
		{
			$select_item_row = mysql_fetch_row($select_item_request);
			$item_id = $select_item_row[0];
			if($item_id == 0)
			{
				$somevar = false;
				if(($my_health + 20) > $my_max_hp)
				{
					$my_health = $my_max_hp;
					$somevar = true;
				}
				elseif($somevar == false)
				{
					$my_health = $my_health + 20;
				}
				$get_hp_request = mysql_query("UPDATE position set health='$my_health' where id='$player_id'");
			}
			elseif($item_id == 5 or $item_id == 6)
			{
				if($item_id == 5)
				{
					$restore_percentage = 30;
				}
				elseif($item_id == 6)
				{
					$restore_percentage = 100;
				}
				$restore_absolute = round($my_max_hp / 100 * $restore_percentage);
				$my_health = $my_health + $restore_absolute;
				if($my_health > $my_max_hp)
				{
					$my_health = $my_max_hp;
				}
				$get_hp_request = mysql_query("UPDATE position set health='$my_health' where id='$player_id'");
			}
			elseif($item_id == 7 or $item_id == 8)
			{
				if($item_id == 7)
				{
					$restore_percentage = 30;
				}
				elseif($item_id == 8)
				{
					$restore_percentage = 100;
				}
				$restore_absolute = round($my_max_magic / 100 * $restore_percentage);
				$my_magic = $my_magic + $restore_absolute;
				if($my_magic > $my_max_magic)
				{
					$my_magic = $my_max_magic;
				}
				$get_magic_request = mysql_query("UPDATE position set magic='$my_magic' where id='$player_id'");
			}
			elseif($item_id == 9 or $item_id == 10)
			{
				if($item_id == 9)
				{
					$restore_percentage = 30;
				}
				elseif($item_id == 10)
				{
					$restore_percentage = 100;
				}
				$restore_absolute = $restore_percentage;
				$stamina = $stamina + $restore_absolute;
				if($stamina > 100)
				{
					$stamina = 100;
				}
				$display_stamina = true;
				$get_stamina_request = mysql_query("UPDATE position set stamina='$stamina' where id='$player_id'");
			}
			$delete_item_request = mysql_query("DELETE from inventories where id='$inventory_id'");
		}
	}
	elseif($extra_explode[0] == 'pick')
	{
		$lying_item_id = htmlentities($extra_explode[2]);

		$select_item_request = mysql_query("SELECT l.item_id,i.name,l.quantity from lying_items as l, items as i where l.id='$lying_item_id' and i.id=l.item_id") or die('error: ' . mysql_error());
		$sir = mysql_fetch_row($select_item_request);
		$item_id = $sir[0];
		$item_name = addslashes($sir[1]);
		$item_quantity = $sir[2];

		if(mysql_num_rows($select_item_request) == 1)
		{
			$select_inventory_request = mysql_query("SELECT count(*) from inventories where belongs_to='$player_id'") or die('error: ' . mysql_error());
			$select_inventory_row = mysql_fetch_row($select_inventory_request);
			$items_in_inventory = $select_inventory_row[0];
			if($items_in_inventory >= 30 && $item_id != 3)
			{
				$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','You cannot pick up this item: Your inventory is full','$room')") or die(mysql_error());
			}
			else
			{

				//$opponent_attack = $svr[1];
				//$opponent_defense = $svr[2];


				$delete_item_request = mysql_query("DELETE from lying_items where id='$lying_item_id'") or die('error');
				if($item_id == 3)
				{
					$get_money_request = mysql_query("UPDATE position SET money = ('$item_quantity' + money) WHERE id = '$player_id' ") or die(mysql_error());

					$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','You picked up $quantity coins','$room')") or die(mysql_error());
				}
				else
				{
					$inventory_request = mysql_query("INSERT into inventories (item_id,belongs_to) VALUES ('$item_id','$player_id')") or die('error');

					$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','You picked up item: $item_name','$room')") or die(mysql_error());
				}
			}

		}
	}
	elseif($extra_explode[0] == 'mine' && is_numeric($extra_explode[2]))
	{
		$special_object_id = htmlentities($extra_explode[1]);
		$tool_inv_id = htmlentities($extra_explode[2]);
				
		
		$select_item_id = mysql_query("SELECT inv.item_id, inv.durability, it.durability from inventories as inv, items as it where inv.id='$tool_inv_id' and it.id=inv.item_id") or die(mysql_error());
		if(mysql_num_rows($select_item_id) == 1)
		{		
			$select_item_array = mysql_fetch_array($select_item_id);
			$tool_item_id = $select_item_array['item_id'];
			if($tool_item_id == 17)
			{
				$tool_level = 1;
				$exp_type = 'woodcutting';
			}
			if($tool_item_id == 18)
			{
				$tool_level = 2;
				$exp_type = 'woodcutting';
			}
			if($tool_item_id == 19)
			{
				$tool_level = 3;
				$exp_type = 'woodcutting';
			}
			if($tool_item_id == 20)
			{
				$tool_level = 4;
				$exp_type = 'woodcutting';
			}
			if($tool_item_id == 21)
			{
				$tool_level = 5;
				$exp_type = 'woodcutting';
			}
			
			if($tool_item_id == 22)
			{
				$tool_level = 1;
				$exp_type = 'mining';
			}
			if($tool_item_id == 23)
			{
				$tool_level = 2;
				$exp_type = 'mining';
			}
			if($tool_item_id == 24)
			{
				$tool_level = 3;
				$exp_type = 'mining';
			}
			if($tool_item_id == 25)
			{
				$tool_level = 4;
				$exp_type = 'mining';
			}
			if($tool_item_id == 26)
			{
				$tool_level = 5;
				$exp_type = 'mining';
			}
			
			$durability = $select_item_array[1] + 1;
			$max_durability = $select_item_array[2];
			if($durability >= $max_durability)
			{
				// axe broke
				$break_tool = mysql_query("DELETE from inventories where id='$tool_inv_id'") or die(mysql_error());
				$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','Your tool broke','$room')") or die(mysql_error());
						
			}
			else
			{
				$update_request = mysql_query("UPDATE inventories set durability='$durability' where id='$tool_inv_id'") or die(mysql_error());
			}
			
			$select_stage_request = mysql_query("SELECT p.stage, p.pos_left, p.pos_top, s.stages, s.drops, s.height, s.width, s.tool_level, s.give_exp from placed_special_objects as p, special_objects as s where p.id='$special_object_id' and s.id=p.object_id") or die('error: ' . mysql_error());
			$sia = mysql_fetch_array($select_stage_request);
			$stage = $sia['stage'] + 1;
			$pos_left = round($sia['pos_left'] + $sia['width'] / 2 - 7.5);
			$pos_top = $sia['pos_top'] + $sia['height'] - 15;
			$item_pos = $pos_left . ',' . $pos_top;
			$stages = $sia['stages'];
			$drops = $sia['drops'];
			$needed_tool_level = $sia['tool_level'];
			$give_exp = $sia['give_exp'];
			if($tool_level >= $needed_tool_level)
			{
				$dropsplit = explode('_',$drops);
				$dropraw = $dropsplit[rand(0,(count($dropsplit) - 1))];
				$dropsplit = explode('-',$dropraw);
				$chance = $dropsplit[0];
				if(rand(1,100) <= $chance)
				{
					$drops = $dropsplit[1];
				}
				else
				{
					$drops = 0;
				}
				if($stage > $stages)
				{
					eval('$exp_this = $exp_'.$exp_type.';');
					/*$next_exp_woodcutting = get_exp($exp_woodcutting,$give_exp);
					$hnd = get_level($exp_woodcutting);
					$old_woodcutting_level = $hnd[0];
					$hnd = get_level($next_exp_woodcutting);
					$new_woodcutting_level = $hnd[0];
					if($new_woodcutting_level > $old_woodcutting_level)
					{
						// Woodcutting level up!
						$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','Congratulations! Your woodcutting is now level $new_woodcutting_level','$room')") or die(mysql_error());
						$exp_woodcutting = $next_exp_woodcutting;
						$update_exp_request = mysql_query("UPDATE position set exp_woodcutting='$exp_woodcutting' where id='$player_id'") or die(mysql_error());
					}*/
					$next_exp_this = get_exp($exp_this,$give_exp);
					$hnd = get_level($exp_this);
					$old_this_level = $hnd[0];
					$hnd = get_level($next_exp_this);
					$new_this_level = $hnd[0];
					if($new_this_level > $old_this_level)
					{
						// Woodcutting level up!
						$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','Congratulations! Your $exp_type is now level $new_this_level','$room')") or die(mysql_error());
					}
					eval('$exp_'.$exp_type.' = $next_exp_this;');
					$update_exp_request = mysql_query("UPDATE position set exp_".$exp_type."='$next_exp_this' where id='$player_id'") or die(mysql_error());
					$myFile = "do.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, "UPDATE position set exp_".$exp_type."='$next_exp_this' where id='$player_id'");
fclose($fh);
					$delete_object_request = mysql_query("DELETE from placed_special_objects where id='$special_object_id'") or die('error: '.mysql_error());
					if($drops != 0)
					{
						$drop_item_request = mysql_query("INSERT into lying_items (room,item_id,position,dropped_time) VALUES ('$room','$drops','$item_pos','".time()."')") or die('error: '.mysql_error());
					}
				}
				else
				{
					$update_stage_request = mysql_query("UPDATE placed_special_objects set stage='$stage' where id='$special_object_id'") or die('error: '.mysql_error());
				}
			}
			else
			{
				// Axe too weak
				$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','Your tool is too weak to mine this.','$room')") or die(mysql_error());
			}
		}
	}
	elseif($extra_explode[0] == 'hotkey' && is_numeric($extra_explode[1]))
	{
		$hotkey_id = htmlentities($extra_explode[1]);
			
		$select_item_id = mysql_query("SELECT item_id from inventories where id='$hotkey_id' and belongs_to='$player_id'") or die(mysql_error());
		if(mysql_num_rows($select_item_id) == 1)
		{		
			$update_request = mysql_query("UPDATE position set item_equipped='$hotkey_id' where id='$player_id'") or die('error: '.mysql_error());
		}
	}
	elseif($extra_explode[0] == 'hit')
	{
		// $lower_health = -5;
		$opponent_id = parseInt($extra_explode[1]);
		if($extra_explode[3] == 'villain')
		{
			
			$select_villains_request = mysql_query("SELECT level,attack,defense from villains where room='$room' and id='$opponent_id'") or die('error: ' . mysql_error());
		}
		else
		{
			$select_villains_request = mysql_query("SELECT level,attack,defense from position where room='$room' and id='$opponent_id'") or die('error: ' . mysql_error());
		}
		if(mysql_num_rows($select_villains_request) == 1)
		{
			$svr = mysql_fetch_row($select_villains_request);
			$opponent_level = $svr[0];
			$opponent_attack = $svr[1];
			$opponent_defense = $svr[2];


			$damage = att($opponent_attack,$my_defense,10);
			if($damage > $my_health)
			{
				$my_health = 0;
				$update_kills_request = mysql_query("UPDATE position set kills=kills+1 where id='$opponent_id'") or die(mysql_error());
			}
			else
			{
				$my_health = $my_health - $damage;
			}

			$update_my_health = mysql_query("UPDATE position set health='$my_health' where id='$player_id'") or die('error');
		}
	}
	if($extra_explode[0] == 'attack')
	{
		$can_attack = true;
		if($extra_explode[3] == 'mana')
		{
			if($my_magic < 20)
			{
				$can_attack = false;
			}
		}
		
		if($can_attack)
		{
			if(isset($extra_explode[1]) && !empty($extra_explode[1]) && $extra_explode[1] == 'villain')
			{
				$villain_id = parseInt($extra_explode[2]);
			
			
				$select_villains_request = mysql_query("SELECT id,pos_top,pos_left,sprite,message,health,player_name,player_type,last_time_update,following,level,attack,defense,max_hp from villains where room='$room' and id='$villain_id'") or die('error: ' . mysql_error());
				$select_villains_number = mysql_num_rows($select_villains_request);
				
				$theData = '';
				for($m = 0; $m < $select_villains_number; $m++)
				{

/*$myFile = "do.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, "Test 2!\n");
fclose($fh);*/

					$svr = mysql_fetch_row($select_villains_request);
					$id = $svr[0];
					$pos_top = $svr[1];
					$pos_left = $svr[2];
					$sprite = $svr[3];
					$message = $svr[4];
					$health = $svr[5];
					$name = $svr[6];
					$player_type = $svr[7];
					$last_time_update = $svr[8];
					$following = $svr[9];
					$level = $svr[10];
					$attack = $svr[11];
					$defense = $svr[12];
					$max_hp = $svr[13];
				

					$explsprite = explode('_',$sprite);
				
					if((count($explsprite) == 3 && $explsprite[1] == 'hurt') or ($sprite == 'sprite_dead'))
					{
					}
					else
					{
						// -- Hit villain script start --
						$hit_enemy = 'yes';
						
						if(isset($extra_explode[3]) && !empty($extra_explode[3]) && $extra_explode[3] == 'up')
						{
							$svr[1] = $svr[1] - 2;
							$svr[3] = 'sprite_hurt_up';
						}
						elseif(isset($extra_explode[3]) && !empty($extra_explode[3]) && $extra_explode[3] == 'down')
						{
							$svr[1] = $svr[1] + 2;
							$svr[3] = 'sprite_hurt_down';
						}
						if(isset($extra_explode[3]) && !empty($extra_explode[3]) && $extra_explode[3] == 'left')
						{
							$svr[2] = $svr[2] - 2;
							$svr[3] = 'sprite_hurt_left';
						}
						elseif(isset($extra_explode[3]) && !empty($extra_explode[3]) && $extra_explode[3] == 'right')
						{
							$svr[2] = $svr[2] + 2;
							$svr[3] = 'sprite_hurt_right';
						}
					
						$following = $player_id;
						// -- Hit villain script end --
					
				
				
						$damage = att($my_attack,$defense,10);
						$new_health = $svr[5];
						$killed_villain = false;
					
						if($new_health >= $damage)
						{
							$new_health = $new_health - $damage; $svr[5] = $new_health;

						}
						else
						{
							$new_health = 0; $svr[5] = $new_health;
							$killed_villain = true;
							$my_old_level = $my_level;
							$lvlarray = gain_exp($attack,$defense,$max_hp,$my_exp,$my_level,$my_attack,$my_defense,$my_max_hp,$my_max_magic);
							// error D:
							$my_exp = $lvlarray[0];
							$my_level = $lvlarray[1];
							$my_attack = $lvlarray[2];
							$my_defense = $lvlarray[3];
							$my_max_hp = $lvlarray[4];
							$my_max_magic = $lvlarray[5];
						}

						$d = $svr;
						$d[8]=micro_time() + 0.5;

						if($killed_villain == false)
						{
							$req = "set id='$d[0]', pos_top='$d[1]', pos_left='$d[2]', sprite='$d[3]', health='$d[5]', message='$d[4]', player_name='$d[6]', player_type='$d[7]', last_time_update='$d[8]', following='$following'";
							$upreq = mysql_query("UPDATE villains $req where id='$villain_id' and room='$room'") or fwrite($fh, "Failed 2: ". mysql_error() . "\n");
						}
						else
						{
							// Goblin killed
							$req = "set id='$d[0]', pos_top='$d[1]', pos_left='$d[2]', sprite='sprite_dead', health='$d[5]', message='$d[4]', player_name='$d[6]', player_type='$d[7]', last_time_update='".($d[8] + 4.5)."', following='$following'";
							$upreq = mysql_query("UPDATE villains $req where id='$villain_id' and room='$room'") or fwrite($fh, "Failed 2: ". mysql_error() . "\n");
							$gain_exp_req = mysql_query("UPDATE position set exp='$my_exp',attack='$my_attack',defense='$my_defense',max_hp='$my_max_hp',max_magic='$my_max_magic' where id='$player_id'") or die('error');
							$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','You killed $d[6]','$room')") or die(mysql_error());
							if($my_old_level < $my_level)
							{
								$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('notice','$player_id','0','Congratulations! You are now level $my_level','$room')") or die(mysql_error());
							}
						}

						/*$myFile = "do.txt";
						$fh = fopen($myFile, 'a') or die("can't open file");
						fwrite($fh, "Done hit\n");
						fclose($fh);*/
					
					}
				}
			
			}
		}
	}
	elseif($extra_explode[0] == 'mana')
	{
		$my_magic = $my_magic - 20;
		if($my_magic < 0)
		{
			$my_magic = 0;
		}
		$insert_request = mysql_query("UPDATE position set magic='$my_magic' where id='$player_id'") or die(mysql_error());
	}
}

// -- END HANDLE EXTRA'S CODE --


// ----------------------








$players = array();

$trades_select_time = time() - 30;

$select_trades_request = mysql_query("SELECT t.id,t.inviter,p.name from trades as t, position as p where t.requestedtime>'$trades_select_time' and t.accepter='$player_id' and t.trade_status='requesting' and p.id=t.inviter") or die('error');
for($a = 0; $a < mysql_num_rows($select_trades_request); $a++)
{
	$select_trades_row = mysql_fetch_row($select_trades_request);
	$trade_id = $select_trades_row[0];
	$inviter = $select_trades_row[1];
	$inviter_name = $select_trades_row[2];
	echo "$trade_id,$inviter,$inviter_name,,,,,traderequest;";
}


$select_position_request = mysql_query("SELECT id,pos_left,pos_top,sprite,message,health,name,last_health_update,last_magic_update,max_hp,max_magic,magic from position where (lastmove>'$select_time' && room='$room' && pos_left>$min_left && pos_left<$max_left && pos_top>$min_top && pos_top<$max_top) or id=$player_id") or die('error');
$number = mysql_num_rows($select_position_request);
for($m = 0; $m < $number; $m++)
{
	if($m != 0)
	{
		echo ";";
	}
	$select_position_array = mysql_fetch_array($select_position_request);
	/*$select_position_number = mysql_num_rows($select_position_request);
	echo "numb: $select_position_number<br>";*/
	$id = $select_position_array['id'];
	$pos_left = $select_position_array['pos_left'];
	$pos_top = $select_position_array['pos_top'];
	$sprite = $select_position_array['sprite'];
	$message = $select_position_array['messsage'];
	$health = $select_position_array['health'];
	$player_name = $select_position_array['name'];
	$last_health_update = $select_position_array['last_health_update'];
	$last_magic_update = $select_position_array['last_magic_update'];
	$max_hp = $select_position_array['max_hp'];
	$max_magic = $select_position_array['max_magic'];
	$magic = $select_position_array['magic'];
	if($id == $player_id)
	{
		if($health != 100 && $health != 0)
		{
			if(time() > ($last_health_update + $regenerate_time))
			{
				$new_last_update = time();
				if($health >= ($max_hp - 5))
				{
					$new_health = $max_hp;
				}
				else
				{
					$new_health = $health + 5;
				}
				$health_update = mysql_query("UPDATE position set health='$new_health', last_health_update='$new_last_update' where id='$player_id'");
			}
		}
		if($magic != 100)
		{
			if(time() > ($last_magic_update + $regenerate_magic_time))
			{
				$new_last_update = time();
				if($magic >= ($max_magic - 5))
				{
					$new_magic = $max_magic;
				}
				else
				{
					$new_magic = $magic + 5;
				}
				$magic_update = mysql_query("UPDATE position set magic='$new_magic', last_magic_update='$new_last_update' where id='$player_id'");
			}
		}
	}

	
	if(!empty($message))
	{
		$mesplode = explode('ao9q82o',$message);
		$timelimit = time() - 20;
		$sendtime = $mesplode[0];
		if($sendtime > $timelimit)
		{
			$message = str_replace(',','',$mesplode[1]);
			$message = str_replace(';','',$mesplode[1]);
		}
		else
		{
			$message = 'nomsg';
		}
	}
	else
	{
		$message = 'nomsg';
	}

	$health_percentage = $health/$max_hp * 100;
	$magic_percentage = $magic/$max_magic * 100;
	if($health <= 0)
	{
		$sprite = "sprite_dead";
	}
	
	echo $id . ',' . $pos_left . ',' . $pos_top . ',' . $sprite . ',' . $message . ',' . $player_name . ',' . round($health_percentage) . ',player,';
	if($id == $player_id)
	{
		echo $item_equipped . ',' . $item_equipped_id . ',' . $durability_percentage . ',' . round($magic_percentage) . ',' . $magic . ',' . $reset_left . ',' . $reset_top;
		if($force_reset)
		{
			file_put_contents('do2.txt','reset left: '.$reset_left.'. reset top: '.$reset_top);
		}
		if($display_stamina)
		{
			echo ',' . $stamina;
		}
	}
	
	$rv = explode(',',$position);
	$pt = $rv[1];
	$pl = $rv[0];
	$py = array($id,$pl,$pt);
	$players[$m] = $py;
}

/*
$myFile = "villains.txt";
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);
//echo ';' . $theData;
*/


if(rand(0,200) == 5)
{
	include('sampletreespawn.php');
}

// if(!isset($hit_enemy))
$random = rand(1,5);
// if($random == 2)
$random = 2;
if($random == 2)
{
	include('villains_2.php');
}

include('lying_items.php');

include('spawn.php');

if($_POST['fetch_messages'])
{
	echo ';split;';
	include('messages.php');
}

mysql_close();
include('functions/db_info.php');
?>