<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function check_has_item($player,$item,$mysql_active = true)
{
	global $dbhost,$dbuser,$dbpass,$db;

	if($mysql_active == false)
	{
		mysql_pconnect($dbhost,$dbuser,$dbpass);
		mysql_select_db($db);
	}
	
	$idlist = array();

	$select_request = mysql_query("SELECT id from inventories where item_id='$item' and belongs_to='$player'") or die(mysql_error());
	$number = mysql_num_rows($select_request);
	for($m = 0; $m < $number; $m++)
	{
		$array = mysql_fetch_array($select_request);
		array_push($idlist, $array['id']);
	}
	if($mysql_active == false)
	{
		mysql_close();
	}

	return(array($number,$idlist));
}


function check_quest_status($player,$quest,$mysql_active = true)
{
	global $dbhost,$dbuser,$dbpass,$db;

	if($mysql_active == false)
	{
		mysql_pconnect($dbhost,$dbuser,$dbpass);
		mysql_select_db($db);
	}

	$select_request = mysql_query("SELECT status from quests where player='$player' and quest_id='$quest'") or die(mysql_error());

	$num_rows = mysql_num_rows($select_request);
	if($num_rows == 0)
	{
		$status = 'not_started';
	}
	else
	{
		$array = mysql_fetch_array($select_request);
		$status = $array['status'];
	}

	if($mysql_active == false)
	{
		mysql_close();
	}

	return($status);
}


?>