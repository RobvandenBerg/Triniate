<?php
include_once('functions/index.php');

$mapid = round($_POST['mapid']);
$columns = round($_POST['columns']);
$rows = round($_POST['rows']);
if($mapid < 1 or $columns < 1 or $rows < 1)
{
	die('Invalid values entered. Please try again. <a href="./">Back</a>');
}
$validation_needed = false;
$validated = false;
if(file_exists('../rooms/'.$mapid))
{
	$validation_needed = true;
}
if(isset($_POST['validation']))
{
	$validated = true;
}
if(!$validation_needed or ($validation_needed and $validated))
{
	
	/*	STRUCTURE:
	500,500,rooms/3.png
	
	'200,220,300,260','300,356,400,396','490,0,500,100,teleport,1,1','274,250,278,264,teleport,40,0','490,0,500,100,teleport,1,1','0,475,82,498,teleport,22,0','380,396,390,400,teleport,34,0'
	
	468,61.sprite_left-33,437.sprite_up-366,377.sprite_down-267,245.sprite_down
	
	200,200,100,60.images/house.png:300,336,100,60.images/house.png

	1/79,82,206,123_11,36,290,163/1-7/0-20/Goblin_Caveman_Madeye/50-1_50-2_100-3-5/0,100
	*/
	
	/*
	- BACKGROUND INFO
	- WALLS
	- INCOMING
	- OBJECTS
	- ENEMIES
	- INFO.TXT
	*/
	$mapdir = '../rooms/'.$mapid;
	mkdir($mapdir);
	file_put_contents($mapdir.'/background.txt',($columns * 20) .','.($rows * 20).',rooms/'.$mapid.'/background.png');
	file_put_contents($mapdir.'/walls.txt','');
	file_put_contents($mapdir.'/incoming.txt','');
	file_put_contents($mapdir.'/objects.txt','');
	file_put_contents($mapdir.'/enemies.txt','');
	file_put_contents($mapdir.'/info.txt','');
	update_map_info_file($mapdir);
	
	file_put_contents('../rooms/'.$mapid.'.txt', ($columns * 20) .','.($rows * 20).',rooms/'.$mapid.'/background.png;;;;'); // for the sake of backwards compatibility
	header('location: construct.php?id='.$mapid);
	
}
else
{
	?>
	A map with id <?php echo $mapid;?> already exists. Are you sure you want to OVERWRITE map <?php echo $mapid;?>?<br>
	<img src='../rooms/<?php echo $mapid;?>/background.png' alt='Map <?php echo $mapid;?>'><br>
	<form action='<?php $_SERVER['PHP_SELF'];?>' method='POST'>
	<input type='hidden' name='columns' value='<?php echo $columns;?>'>
	<input type='hidden' name='rows' value='<?php echo $rows;?>'>
	<input type='hidden' name='mapid' value='<?php echo $mapid;?>'>
	<input type='hidden' name='validation' value='1'>
	<input type='button' value='Cancel' onClick='window.location="./";'><input type='submit' value='Confirm'>
	</form>
	<?php
}
?>