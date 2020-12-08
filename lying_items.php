<?php


if(isset($_GET['display']))
{
	include("include_this.php");
	light_login();
	mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
	mysql_select_db($db);
}

$select_items_request = mysql_query("SELECT l.item_id,l.position,l.id from lying_items as l where l.room='$room' and (l.visible='0' or l.visible='$player_id') and (l.dropped_time>".(time() - 120)." or l.visible='$player_id')") or die(mysql_error());

$total_lying_items = mysql_num_rows($select_items_request);
for($a = 0; $a < $total_lying_items; $a++)
{
	$lying_items_row = mysql_fetch_row($select_items_request);
	$item_id = $lying_items_row[0];
	$item_position = $lying_items_row[1];
	$pos_split = explode(",",$item_position);
	$lying_item_id = $lying_items_row[2];
	echo ";$item_id,$pos_split[1],$pos_split[0],$lying_item_id,,,,item";
}



/// SPECIAL OBJECTS START HERE

$select_objects_request = mysql_query("SELECT p.object_id,p.pos_left,p.pos_top,p.id,p.stage,s.width,s.height,s.minetime,s.block,s.zindex,s.tool,s.tool_level from placed_special_objects as p, special_objects as s where p.room='$room' and s.id=p.object_id and (p.visible='0' or p.visible='$player_id') and pos_left>$min_left and pos_left<$max_left and pos_top>$min_top and pos_top<$max_top") or die(mysql_error());
// echo "SELECT p.object_id,p.pos_left,p.pos_top,p.id,p.stage,s.width,s.height,s.minetime,s.tool,t.tool_level from placed_special_objects as p, special_objects as s where p.room='$room' and s.id=p.object_id and (p.visible='0' or p.visible='$player_id')";
$total_lying_items = mysql_num_rows($select_objects_request);
// $total_lying_items = 0;
for($a = 0; $a < $total_lying_items; $a++)
{
	$lying_items_array = mysql_fetch_array($select_objects_request);
	$object_id = $lying_items_array['object_id'];
	$pos_left = $lying_items_array['pos_left'];
	$pos_top = $lying_items_array['pos_top'];
	$stage = $lying_items_array['stage'];
	$width = $lying_items_array['width'];
	$height = $lying_items_array['height'];
	$minetime = $lying_items_array['minetime'];
	$lying_item_id = $lying_items_array['id'];
	$block = $lying_items_array['block'];
	$zindex = $lying_items_array['zindex'];
	$tool = $lying_items_array['tool'];
	$tool_level = $lying_items_array['tool_level'];
	$exp_block = explode(',',$block);
	if(count($exp_block) == 4)
	{
		$bsplit = explode(',',$block);
		$bl = $bsplit[0] + 2;
		$bt = $bsplit[1];
		$br = $bsplit[2] + 2;
		$bb = $bsplit[3];
		$block = ($pos_left + $bl) . ',' . ($pos_top + $bt) . ',' . ($pos_left + $br) . ',' . ($pos_top + $bb);
	}
	echo ";$lying_item_id,$pos_left,$pos_top,$object_id,$width,$height,$stage,special_object,$tool,$tool_level,$minetime,$zindex,$block";
}

if(isset($_GET['display']))
{
	mysql_close();
}
?>