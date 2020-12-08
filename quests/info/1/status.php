<?php
$needed_level = 5;
$items_needed = array(1=>2);

$quest_status = check_quest_status($player_id,$quest_id);

if($quest_status == 'not_started')
{
	$code = 1;
}
elseif($quest_status == 'started')
{
	$code = 2;
	$accomplished = true;
	if($my_level > $needed_level)
	{
		foreach($items_needed as $needed_item => $ammount)
		{
			if(check_has_item($player_id,$needed_item) < $ammount)
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
?>