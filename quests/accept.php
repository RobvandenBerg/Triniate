<?php
// NEEDED variables: $needed_level, $level_reject_message,$accept_quest_message,$quest_id,$player_id

function accept_quest($needed_level, $level_reject_message,$accept_quest_message,$quest_id)
{

	global $player_id,$my_level,$accept_quest_queries;
	if($my_level >= $needed_level)
	{
		$accept = 'yes';
		$accept_message = 'accept;'.$accept_quest_message;
	}
	else
	{
		$reject_message = 'reject;'.$level_reject_message;
	}

	$check_status_request = mysql_query("SELECT id from quests where quest_id='$quest_id' and player='$player_id'") or die(mysql_error());
	if(mysql_num_rows($check_status_request) != 0)
	{
		unset($accept);
		$reject_message = 'reject;You can\'t accept this quest, because you already accepted it... ';
	}


	// -----------------------

	$output = '';
	if(isset($accept))
	{
		//echo "accept;" . $accept_message;
		$accept_quest_request = mysql_query("INSERT into quests (quest_id,player,status) VALUES ('$quest_id','$player_id','started')") or die(mysql_error());
		if(isset($accept_quest_queries))
		{
			if(count($accept_quest_queries) > 0)
			{
				foreach($accept_quest_queries as $accept_quest_query)
				{
					$do_query = mysql_query($accept_quest_query) or die(mysql_error());
				}
			}
		}
		$output = $accept_message;
	}
	else
	{
		//echo "reject;" . $reject_message;
		$output = $reject_message;
	}
		
	return($output);

}
?>