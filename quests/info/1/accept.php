<?php
$needed_level = 5;
if($my_level >= $needed_level)
{
$accept = 'yes';
$accept_message = "accept;Ho ho! Thank you very much! Please bring me two apples!";
}
else
{
$reject_message = "reject;You should be at least level $needed_level to have a chance at such a task!";
}

$check_status_request = mysql_query("SELECT id from quests where quest_id='$quest_id' and player='$player_id'") or die(mysql_error());
if(mysql_num_rows($check_status_request) != 0)
{
	unset($accept);
	$reject_message = 'reject;Uhh... You already accepted this quest.';
}
?>