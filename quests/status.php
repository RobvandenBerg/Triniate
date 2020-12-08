<?php
// NEEDED variables: $needed_level,$items_needed,$quest_id,$player_id
/*
$needed_level = 5;
$items_needed = array(1=>2);
*/

function quest_status($needed_level,$items_needed,$quest_id)
{
	global $player_id,$my_level;

	$quest_status = check_quest_status($player_id,$quest_id);

	if($quest_status == 'not_started')
	{
		$code = 1;
	}
	elseif($quest_status == 'started')
	{
		$code = 2;
		$accomplished = true;
		if($my_level >= $needed_level)
		{
			foreach($items_needed as $needed_item => $amount)
			{
				$chi = check_has_item($player_id,$needed_item);
				if($chi[0] < $amount)
				{
					$accomplished = false;
				}
			}
		}
		if($accomplished == true)
		{
			$code = 3;
		}
	}
	elseif($quest_status == 'finished')
	{
		$code = 4;
	}

	// -----------------
	
	return($code);
	
}
?>