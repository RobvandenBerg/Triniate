<?php



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

echo "works<hr>";

if(isset($_POST['do']))
{
$level = $_POST['level'];
$attack = $_POST['attack'];
$defense = $_POST['defense'];
$train = $_POST['train'];
$train = 'train';

if($train == 'power')
{
$returned = train_power($attack,$level,5);
echo "You trained power. Returned value: $returned.<br>";

}
elseif($train == 'chance')
{
$returned = train_chance($attack,$level);
echo "You trained chance. Returned value: $returned%.<br>";
}
elseif($train == 'train')
{
$result = train($attack,$defense,'both',$level,5000000000,-20);
$attack = $result[0];
$defense = $result[1];
echo "New attack: $attack<br>New defense: $defense<br>";
}
}
?>

<form action='<?php echo $_SERVER['PHP_SELF'];?>' method='POST'>
Level: <input type='text' name='level' value='<?php echo $_POST['level'];?>'><br>
Attack: <input type='text' name='attack' value='<?php echo $_POST['attack'];?>'><br>
Defense: <input type='text' name='defense' value='<?php echo $_POST['defense'];?>'><br>
<input type='submit' name='do' value='Do'>
</form>