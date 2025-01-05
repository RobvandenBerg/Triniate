<?php
error_reporting(0);
include('../../include_this.php');
light_login();


$member_id = $player_id;
// include("../functions/index.php");


$sendvaluesplit = explode(";",$_POST['sendvalue']);
if(count($sendvaluesplit) != 4)
{
	die("Incorrect sendvalue");
}

$cer = 1;
foreach($sendvaluesplit as $value)
{
	if((!is_numeric($value) or empty($value)) && $cer != 4)
	{
		die("Incorrect value: $value");
	}
	$cer++;
}

$uhead = $sendvaluesplit[0];
$ubody = $sendvaluesplit[1];
$ulegs = $sendvaluesplit[2];
$uweapon = $sendvaluesplit[3];


mysql_pconnect($dbhost, $dbuser, $dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());

$update_parts_request = mysql_query("UPDATE position set head='$uhead', body='$ubody', legs='$ulegs', weapon='$uweapon' where id='$player_id'") or die(mysql_error());

mysql_close();

// This line is not used in anywhere else.
// $sprite_id = $_POST['id'];
/*

	$sprite = 'stand_down';
	include('render.php');

	$sprite = 'stand_up';
	include('render.php');

	$sprite = 'stand_left';
	include('render.php');

	$sprite = 'stand_right';
	include('render.php');

	$sprite = 'walk_down';
	include('render.php');

	$sprite = 'walk_up';
	include('render.php');

	$sprite = 'walk_left';
	include('render.php');

	$sprite = 'walk_right';
	include('render.php');

	$sprite = 'attack_up';
	include('render.php');

	$sprite = 'attack_down';
	include('render.php');

	$sprite = 'attack_left';
	include('render.php');

	$sprite = 'attack_right';
	include('render.php');



	$sprite = 'hurt_up';
	$use_sprite = 'stand_up';
	include('render_red_version.php');

	$sprite = 'hurt_down';
	$use_sprite = 'stand_down';
	include('render_red_version.php');

	$sprite = 'hurt_left';
	$use_sprite = 'stand_left';
	include('render_red_version.php');

	$sprite = 'hurt_right';
	$use_sprite = 'stand_right';
	include('render_red_version.php');
	
	$sprite = 'dead';
	$use_sprite = 'stand_down';
	include('render_black_version.php');
*/

echo "Sprite created. <a href='../../redirect.php'>Play Triniate</a>";
	//echo "<script>alert('Sprite succesfully rendered!'); window.location='../../index.php';</script>";
	exit();
?>
