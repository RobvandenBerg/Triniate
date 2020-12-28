<?php
include_once("functions/mysql_functions.php");


include_once('include_this.php');

full_login();

if($logged_in)
{
	$mysqli = mysqli_connect($dbhost,$dbuser,$dbpass, $db) or die($mysqli -> error);
	$select_open_request = $mysqli -> query("SELECT id, betatester from position where account_id='$user_id'") or die($mysqli -> error);
	$select_open_num = $select_open_request -> num_rows;
	if($select_open_num > 0)
	{
		$select_open_array = $select_open_request -> fetch_assoc();
		$player_id = $select_open_array['id'];
		$betatester = $select_open_array['betatester'];
	}
	else
	{
		$insert_request = $mysqli -> query("INSERT into position (account_id,name) VALUES ('$user_id','$username')") or die(mysql_error());

		$select_open_request = $mysqli -> query("SELECT id, betatester from position where account_id='$user_id'") or die('error');
		$select_open_num = $select_open_request -> num_rows;
		if($select_open_num > 0)
		{
			$select_open_array = $select_open_request -> fetch_assoc();
			$player_id = $select_open_array['id'];
			$betatester = $select_open_array['betatester'];
		}
		else
		{
			$mysqli -> close();
			die("An error occurred...");
		}

	}

	if(empty($player_id))
	{
		die("Error: Player id is empty!");
	}
	$mg = obtain_mg($player_id);

	session_start();
	$_SESSION['playerid'] = $player_id;
	$_SESSION['mg'] = $mg;
	$_SESSION['betatester'] = $betatester;
	if(!empty($username))
	{
		$mysql_update_req = $mysqli -> query("UPDATE position set name='$username' where id='$player_id'") or die($mysqli -> error);
	}
	else
	{
		$mysql_update_req = $mysqli -> query("UPDATE position set name='Player $player_id' where id='$player_id'") or die($mysqli -> error);
	}
	$mysqli -> close();
	
	if($username != 'Robdeprop' and $username != 'Supershell52')
	{
		if(file_exists('/var/www/html/pushme/index.php'))
		{
			include_once('/var/www/html/pushme/index.php');
			send_notification_to_robdeprop($username . ' just logged in to Triniate!');
		}
	}
	
	include('redirect.php');
	exit();
}
?>
Log in first.<br>
<a href='./'>Back</a>