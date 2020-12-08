<?php
$needed_level = 5;
$quest_status = check_quest_status($player_id,$quest_id);
$items_needed = array(1=>2);
$items_get = array(2=>1);

$accomplish_message = 'accomplish;Thank you so much for the two apples!';
$accomplish = 'yes';
$accomplish_queries = array();

$query2 = 'INSERT into inventories (item_id,belongs_to) VALUES';
$firstinsert = true;
foreach($items_get as $item_id => $amount)
{
	if($firstinsert == false)
	{
		$query2 .= ',';
	}
	$query2 .= ' (\''.$item_id.'\',\''.$player_id.'\')';
	$firstinsert = false;
}

if(count($items_get) != 0)
{
	$accomplish_queries[] = $query2;
}

$delete_ids = array();
if($quest_status == 'started')
{
	$query1 = 'DELETE from inventories where ';
		foreach($items_needed as $needed_item => $amount)
		{
			$chi = check_has_item($player_id,$needed_item);
			$chi1 = $chi[1];
			$countchi = count($chi[1]);
			if($chi[0] >= $amount)
			{
				for($m = 0; $m < $amount; $m++)
				{
					$inventories_item_id = $chi[1][$m];
					$delete_ids[] = $inventories_item_id;
				}
			}
			else
			{
				unset($accomplish);
				$reject_message = "reject;Message from Triniate.com: Something just went wrong. (code 3)";
			}
		}
	$firstdel = true;
	foreach($delete_ids as $del_id)
	{
		if($firstdel == false)
		{
			$query1 .= 'or ';
		}
		$query1 .= 'id=\''.$del_id.'\' ';
		$firstdel = false;
	}
	if(count($delete_ids) != 0)
	{
		$accomplish_queries[] = $query1;
	}
		
}
else
{
	unset($accomplish);
	$reject_message = "reject;Message from Triniate.com: Something just went wrong. (code 4)";
}

?>