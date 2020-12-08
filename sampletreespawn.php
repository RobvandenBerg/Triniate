<?php
if(isset($_GET['display']))
{
	include('include_this.php');
	light_login();
	mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
	mysql_select_db($db);
}

$room_3 = array(3,1,array(array(20,50,150,350,1),array(20,50,150,350,1),array(20,50,150,350,2)));
$room_38 = array(38,3,array(array(10,40,250,350,3),array(10,40,250,350,3),array(10,40,250,350,3),array(10,40,250,350,3),array(10,40,250,350,3),array(10,40,250,350,3),array(10,40,250,350,3),array(10,40,250,350,3),array(10,40,250,350,4),array(10,40,250,350,4),array(10,40,250,350,5)));

$randomizer = rand(0,5);
if($randomizer == 3)
{
	$room_38[2][0] = array(10,40,250,350,6);
}

$spawnrooms = array($room_3,$room_38);

// $spawnrooms = array(array(3,1,array(array(20,50,150,350,1),array(20,50,150,350,1),array(20,50,150,350,2))),array(38,3,array(array(10,40,250,350,4),array(10,40,250,350,4),array(10,40,250,350,5))));
$count_spawnrooms = count($spawnrooms);
for($a = 0; $a < $count_spawnrooms; $a++)
{
	$current_array = $spawnrooms[$a];
	$rooms_id = $current_array[0];
	$max_objects = $current_array[1];
	$placable_objects = $current_array[2];
	$select_objects_request = mysql_query("SELECT count(*) from placed_special_objects where room='$rooms_id'") or die(mysql_error());
	$select_objects_array = mysql_fetch_array($select_objects_request);
	$total_objects = $select_objects_array[0];

	if($total_objects < $max_objects)
	{
		$place_object = $placable_objects[rand(0,(count($placable_objects) - 1))];
		$min_x_coord = $place_object[0];
		$max_x_coord = $place_object[2];
		$min_y_coord = $place_object[1];
		$max_y_coord = $place_object[3];
		$place_object_id = $place_object[4];
		$left = rand($min_x_coord,$max_x_coord);
		$top = rand($min_y_coord,$max_y_coord);
		
		$insert_tree_request = mysql_query("INSERT into placed_special_objects (object_id,pos_left,pos_top,room) VALUES ('$place_object_id','$left','$top','$rooms_id')") or die(mysql_error());
		if(isset($_GET['display']))
		{
			echo "INSERT into placed_special_objects (object_id,pos_left,pos_top,room) VALUES ('$place_object_id','$left','$top','$rooms_id')";
		}
	}
}

if(isset($_GET['display']))
{
	mysql_close();
}
?>