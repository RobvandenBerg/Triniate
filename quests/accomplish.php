<?php
// NEEDED variables: $needed_level, $items_needed,$items_get,$quest_id,$accomplish_message,$player_id
/*$needed_level = 5;
$quest_status = check_quest_status($player_id,$quest_id);
$items_needed = array(1=>2);
$items_get = array(2=>1);*/

function accomplish_quest($needed_level,$items_needed,$items_get,$accomplish_message,$quest_id)
{

	global $player_id,$my_level;

	$quest_status = check_quest_status($player_id,$quest_id);

	$accomplish_message = 'accomplish;'.$accomplish_message;
	$accomplish = 'yes';
	$accomplish_queries = array();
	
	$query2 = 'INSERT into inventories (item_id,belongs_to) VALUES';
	$firstinsert = true;
	$addmoney = 0;
	foreach($items_get as $item_id => $amount)
	{
		if($item_id == 3)
		{
			// money
			$addmoney = $addmoney + $amount;
		}
		else
		{
			for($a = 0; $a < $amount; $a++)
			{
				if($firstinsert == false)
				{
					$query2 .= ',';
				}
				$query2 .= ' (\''.$item_id.'\',\''.$player_id.'\')';
				$firstinsert = false;
			}
		}
	}
	
	if(count($items_get) != 0)
	{
		$accomplish_queries[] = $query2;
		if($addmoney != 0)
		{
			$query3 = "UPDATE position set money=money+$addmoney where id='$player_id'";
			$accomplish_queries[] = $query3;
		}
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
					$reject_message = "reject;Message from Triniate.com: Something just went wrong (code 1).";
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
		$reject_message = "reject;Message from Triniate.com: Something just went wrong (code 2).";
	}


	// -------------------------------

	$output = '';

	if(isset($accomplish))
	{
		//echo "accept;" . $accept_message;
		$accept_quest_request = mysql_query("UPDATE quests set status='finished' where quest_id='$quest_id' and player='$player_id'") or die(mysql_error());
		if(count($accomplish_queries) != 0)
		{
			foreach($accomplish_queries as $accomplish_query)
			{
				$do_query = mysql_query($accomplish_query) or die(mysql_error());
			}
		}
		$output = $accomplish_message;
	}
	else
	{
		//echo "reject;" . $reject_message;
		$output = $reject_message;
	}
	return($output);
}
?>