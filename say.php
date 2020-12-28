<?php
include("include_this.php");

light_login();

mysql_connect($dbhost,$dbuser,$dbpass) or die(mysql_error());
$mysqli = mysql_select_db($db) or die(mysql_error());
if(isset($_POST['text']))
{
	$originaltext= $_POST['text'];
	$chars_raw = Array('♪','↑','↓','→','←','♫');
	$chars_encoded = Array('&#9834;','&#8593;','&#8595;','&#8594;','&#8592;','&#9835;');
	$originaltext = str_replace($chars_raw,$chars_encoded,$originaltext);
	$originaltext = htmlentities($originaltext);
	$time = time();
	$text = $time . 'ao9q82o' . $originaltext;
		// $sprite = htmlentities($_GET['sprite']);
	$update_message_request = mysql_query("UPDATE position set message='".$mysqli->real_escape_string($text)."' where id='$player_id'") or die(mysql_error());
	$insert_request = mysql_query("INSERT into chat (type,receiver,sender,message,room) VALUES ('chat','everybody','$player_id','".$mysqli->real_escape_string($originaltext)."','$room')") or die(mysql_error());
}
mysql_close();
?>