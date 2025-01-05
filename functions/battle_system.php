<?php
// Remove error checking
function hp_upgrade($hp,$level,$strongness)
{
if(($hp - $level * 3 - 47) < 53)
{
// HP kan erbij
$trainedhp = $hp - $level * 3 - 47;
$newhp = 53 - $trainedhp;
if($newhp < $strongness)
{
$outputhp = 100 + $level * 3;
}
else
{
$outputhp = $hp + $strongness;
}
return($outputhp);
}
else
{
// HP kan er niet bij
return($hp);
}
}

function att($att,$def,$strength=10)
{
if($att >= $def)
{
$hp = (($att-$def)/500 + 1) * ((5*$att+1940)/199);
$hp = $hp / 10 * $strength;
return(ceil($hp));
}
else
{
$hp = (1/(abs($att-$def)/500 + 1)) * ((5*$att+1940)/199);
$hp = $hp / 10 * $strength;
return(floor($hp));
}
}




// Level omhoog
function level_up($attack,$defense)
{
$attack = $attack + 10;
$defense = $defense + 10;
return array($attack,$defense);
}
// End of level script

// Item script
function use_item($attack,$defense,$item_strongness,$works_on)
{
if($works_on == 'att')
{
$attack = $attack + $item_strongness;
}
elseif($works_on == 'both')
{
$attack = $attack + $item_strongness;
$defense = $defense + $item_strongness;
}
else
{
$defense = $defense + $item_strongness;
}
return(array($attack,$defense));
}
// End of item script

// Training script
function train_power($stat,$level,$potential)
{
$output =  round( $potential * (((-1.5 * $stat + 15 * $level)/1000) + 2) );

if(($stat + $output) > ($level * 10 + 1000))
{
$output = $level * 10 + 1000 - $stat;
}
return($output);
}

function train_chance($stat,$level,$extrachance = 0)
{
$stat = $stat - 10 * $level;
$output1 =  ((-90 * $stat)/1000 + 100 )  + $extrachance;
$output2 = (($stat-1000)/1) * (1/(($stat-1000) + 0.00000000000000000000000000001));
$output = $output1 * $output2;
return($output);
}

function train($attack,$defense,$works_on,$level,$potential,$extrachance=0)
{
if($works_on == 'att' or $works_on == 'both')
{
$train_att = $attack + train_power($attack,$level,$potential);
$att_chance = train_chance($attack,$level,$extrachance);
$picknum = rand(0,100);
if($picknum < $att_chance)
{
$attack = $train_att;
}
}
if($works_on == 'def' or $works_on == 'both')
{
$train_def = $defense + train_power($defense,$level,$potential);
$def_chance = train_chance($defense,$level,$extrachance);
$picknum = rand(0,100);
if($picknum < $def_chance)
{
$defense = $train_def;
}
}

return(array($attack,$defense));
}
// End of training script







// EXP functions

function get_exp_small($attack,$defense,$hp)
{
$exp = round(($attack + $defense)/2 + $hp / 30);
return($exp);
}

function get_level($exp)
{
	$return_level = 100;
	$return_exp = $exp;
	for($m = 1; $m < 100; $m++)
	{
		$level = $m;
		$to_next_level = round(($level) * 50 * (1+($level*2)/10));
		if($return_exp < $to_next_level)
		{
			$returner = $m;
			$m = 100;
		}
		else
		{
			$return_exp = $return_exp - $to_next_level;
		}
	}
	return(array($returner,$return_exp));
}

function exp_to_level($exp)
{
	$return_level = 100;
	$return_exp = $exp;
	for($m = 1; $m < 100; $m++)
	{
		$level = $m;
		$to_next_level = round(($level) * 50 * (1+($level*2)/10));
		if($return_exp < $to_next_level)
		{
			$returner = $m;
			$m = 100;
		}
		else
		{
			$return_exp = $return_exp - $to_next_level;
		}
	}
	return($returner);
}

function level_to_exp($level)
{
	$dexp = 0;
	for($a = 1; $a < $level; $a++)
	{
		$dexp = $dexp + round(($a) * 50 * (1+($a*2)/10));
	}
	return($dexp);
}

function get_exp($exp,$level,$gainexp = false)
{
	if(!$gainexp)
	{
		$gainexp = $level;
		$level_handle = get_level($exp);
		$level = $level_handle[0];
	}
$to_next_level = round(($level) * 50 * (1+$level/10));
$newexp = $exp + $gainexp;
/*if($newexp > $to_next_level)
{
$level = $level + 1;
$newexp = $newexp - $to_next_level;
}*/

return($newexp);
}

function gain_exp($opponent_attack,$opponent_defense,$opponent_hp,$exp,$level,$attack,$defense,$max_hp,$max_magic = 0)
{
$gainexp = get_exp_small($opponent_attack,$opponent_defense,$opponent_hp);
$nexp = get_exp($exp,$level,$gainexp);
$level_handle = get_level($nexp);
$nlevel = $level_handle[0];
if($nlevel > $level)
{
$ret = level_up($attack,$defense);
$attack = $ret[0];
$defense = $ret[1];
$max_hp = $max_hp + 3;
$max_magic = $max_magic + 1;
}

if($max_magic != 0)
{
return(array($nexp,$nlevel,$attack,$defense,$max_hp,$max_magic));
}
else
{
return(array($nexp,$nlevel,$attack,$defense,$max_hp));
}
}

// End of EXP functions
?>
