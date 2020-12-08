<?php
include("include_this.php");


light_login();

if(!is_numeric($_GET['inoption']))
{
	$redirect_to = htmlentities($_GET['inoption']) . ".php";
	if(file_exists($redirect_to))
	{
		header("location: $redirect_to");
		exit();
	}
}

if(isset($_GET['newroom']) && is_numeric($_GET['newroom']) && isset($_GET['inoption']) && is_numeric($_GET['inoption']))
{
	$newroom = $_GET['newroom'];
	$inoption = $_GET['inoption'];
	
	// -- GET ROOM SETTINGS CODE --
	$myFile = 'rooms/' . $newroom . '.txt';
	$fh = fopen($myFile, 'r');
	$theData = fread($fh, filesize($myFile));
	fclose($fh);
	$settingsexplode = explode(';',$theData);
	$pos_array = explode('-',$settingsexplode[2]);
	$possplit = explode('.',$pos_array[$inoption]);
	$newpos = $possplit[0];
	$newpossplit = explode(',',$newpos);
	$npos_left = $newpossplit[0];
	$npos_top = $newpossplit[1];
	$newsprite = $possplit[1];
	//echo "<script>alert('$theData - $background');</script>";
	// -- END GET ROOM SETTINGS CODE --
	
	file_put_contents('do2.txt',"UPDATE position set room='$newroom',pos_left='$npos_left',pos_top='$npos_top',sprite='$newsprite',flag_left='$npos_left',flag_top='$npos_top',flagtime='".time()."' where id='$player_id'");
	
	
	mysql_pconnect($dbhost,$dbuser,$dbpass);
	mysql_select_db($db);
	$upreq = mysql_query("UPDATE position set room='$newroom',pos_left='$npos_left',pos_top='$npos_top',sprite='$newsprite',flag_left='$npos_left',flag_top='$npos_top',flagtime='".time()."' where id='$player_id'") or die(mysql_error());
	mysql_close();
	

$_SESSION['room'] = $newroom;

	// echo "Je gaat naar spot $inoption.<br>De hele string: $settingsexplode[2]<br>Jouw keuze: $pos_array[$inoption].<hr>";
	include("redirect.php");
}
?>