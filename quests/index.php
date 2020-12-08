<?php
include("../include_this.php");
light_login();

include('accomplish.php');
include('status.php');
include('accept.php');

mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());


// ---------------------

$select_my_settings_request = mysql_query("SELECT exp from position where id='$player_id'") or die(mysql_error());
if(mysql_num_rows($select_my_settings_request) != 1)
{
	mysql_close();
	die("error");
}
$select_my_settings_row = mysql_fetch_row($select_my_settings_request);
$real_exp = $select_my_settings_row[0];
$level_handle = get_level($real_exp);
$my_level = $level_handle[0];

if(isset($_GET['accept_quest']) && is_numeric($_GET['accept_quest']))
{
	$quest_id = $_GET['accept_quest'];
	if(file_exists('info/'.$quest_id.'.php'))
	{
		include('info/'.$quest_id.'.php');
		echo accept_quest($needed_level, $level_reject_message,$accept_quest_message,$quest_id);
	}
}
elseif(isset($_GET['quest_status']) && is_numeric($_GET['quest_status']))
{
//echo "1";
// 1 = not started
// 2 = started, not yet completed
// 3 = now completed
// 4 = completed already
	$quest_id = $_GET['quest_status'];
	if(file_exists('info/'.$quest_id.'.php'))
	{
		include('info/'.$quest_id.'.php');
		echo quest_status($needed_level,$items_needed,$quest_id);
	}
}
elseif(isset($_GET['accomplish_quest']) && is_numeric($_GET['accomplish_quest']))
{
	$quest_id = $_GET['accomplish_quest'];
	if(file_exists('info/'.$quest_id.'.php'))
	{
		include('info/'.$quest_id.'.php');
		echo accomplish_quest($needed_level,$items_needed,$items_get,$accomplish_message,$quest_id);
	}
}
else
{
echo "\$_GET['accept_quest'] = ". $_GET['accept_quest'] . "!";
}




mysql_close();

include("../functions/db_info.php");
/*
?>
<form action='<?php echo $_SERVER['PHP_SELF'];?>' method='post'>
<input type='text' name='accept_quest'>
<input type='submit' value='Submit!'>
</form>
<?php
*/
?>