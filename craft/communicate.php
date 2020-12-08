<?php
include('../include_this.php');
light_login();
if(empty($_POST['craftrow_0']) or empty($_POST['craftrow_1']) or empty($_POST['craftrow_2']))
{
	die('NO ITEMS NO CRAFTING.');
}

$craftbox = array();
for($i = 0; $i < 3; $i++)
{
	// eval('$craftrow_'.$i.' = $_POST[\'craftrow_'.$i.'\'];');
	eval('$crow = $_POST[\'craftrow_'.$i.'\'];');
	$exploder = explode(';',$crow);
	$the_array = array();
	foreach($exploder as $exploded)
	{
		$cut = explode(',',$exploded);
		$the_array[] = $cut;
	}
	$craftbox[$i] = $the_array;
}


$check_query = '';
$total_items = 0;
foreach($craftbox as $craft_rows)
{
	foreach($craft_rows as $craft_item)
	{
		if($craft_item[0] != 0 || $craft_item[1] != 0)
		{
			$total_items++;
			if(!empty($check_query))
			{
				$check_query .= ' or ';
			}
			$check_query .= 'id=\''.$craft_item[0].'\' and item_id=\''.$craft_item[1].'\'';
		}
	}
}

if(empty($check_query))
{
	echo 0;
	if(!empty($_POST['get_output']))
	{
		echo ',0';
	}
	die();
}

mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db);

$check_request = mysql_query("SELECT count(*) from inventories where $check_query and belongs_to='$player_id'") or die(mysql_error());
mysql_close();

$check_array = mysql_fetch_array($check_request);

if($check_array[0] != $total_items)
{
	die("$check_array[0] != $total_items");
}


$oak_planks = array(array(array(30,0)),33,2);
$spruce_planks = array(array(array(31,0)),34,2);
$birch_planks = array(array(array(32,0)),35,2);
$oak_sticks = array(array(array(33)),27,4);
$spruce_sticks = array(array(array(34)),27,4);
$birch_sticks = array(array(array(35)),27,4);
$wooden_axe_oak = array(array(array(33,33),array(27,33),array(27,0)),17,1);
$wooden_axe_spruce = array(array(array(34,34),array(27,34),array(27,0)),17,1);
$wooden_axe_birch = array(array(array(35,35),array(27,35),array(27,0)),17,1);
$stone_axe = array(array(array(36,36),array(27,36),array(27,0)),18,1);
$iron_axe = array(array(array(37,37),array(27,37),array(27,0)),19,1);
$golden_axe = array(array(array(38,38),array(27,38),array(27,0)),20,1);

$wooden_pickaxe_oak = array(array(array(33,33,33),array(0,27,0),array(0,27,0)),22,1);
$wooden_pickaxe_spruce = array(array(array(34,34,34),array(0,27,0),array(0,27,0)),22,1);
$wooden_pickaxe_birch = array(array(array(35,35,35),array(0,27,0),array(0,27,0)),22,1);
$stone_pickaxe = array(array(array(36,36,36),array(0,27,0),array(0,27,0)),23,1);
$iron_pickaxe = array(array(array(37,37,37),array(0,27,0),array(0,27,0)),24,1);
$golden_pickaxe = array(array(array(38,38,38),array(0,27,0),array(0,27,0)),25,1);


// $craftable = array(array(array(array(1,0),array(0,16)),2,3));
$craftable = array($oak_planks,$spruce_planks,$birch_planks,$oak_sticks,$spruce_sticks,$birch_sticks,$wooden_axe_oak,$wooden_axe_spruce,$wooden_axe_birch,$stone_axe,$iron_axe,$golden_axe,$wooden_pickaxe_oak,$wooden_pickaxe_spruce,$wooden_pickaxe_birch,$stone_pickaxe,$iron_pickaxe,$golden_pickaxe);
function get_output_item($craftbox,$craftable)
{
for($a = 0; $a < 3; $a++)
{
	for($b = 0; $b < 3; $b++)
	{
		$citem = $craftbox[$a][$b][1];
		foreach($craftable as $check_craftable_raw)
		{
			$return_id = $check_craftable_raw[1];
			$return_amount = $check_craftable_raw[2];
			$check_craftable = $check_craftable_raw[0];
			if($citem == $check_craftable[0][0])
			{
				$check_craftable_rows = count($check_craftable);
				$check_craftable_columns = count($check_craftable[0]);
				$valid = true;
				for($c = 0; $c < $check_craftable_rows; $c++)
				{
					for($d = 0; $d < $check_craftable_columns; $d++)
					{
						$ncitem = $craftbox[($c + $a)][($d + $b)][1];
						$check_craftable_item = $check_craftable[$c][$d];
						if($ncitem != $check_craftable_item)
						{
							//return("$ncitem is not $check_craftable_item");
							$valid = false;
						}
					}
				}
				if($valid == true)
				{
					if($a + $check_craftable_rows < 2)
					{
						if($craftbox[1][0][1] != 0 or $craftbox[1][1][1] != 0 or $craftbox[1][2][1] != 0)
						{
							$valid = false;
						}
					}
					if($valid && $a + $check_craftable_rows < 3)
					{
						if($craftbox[2][0][1] != 0 or $craftbox[2][1][1] != 0 or $craftbox[2][2][1] != 0)
						{
							$valid = false;
						}
					}
					if($valid && $b + $check_craftable_columns < 2)
					{
						if($craftbox[0][1][1] != 0 or $craftbox[1][1][1] != 0 or $craftbox[2][1][1] != 0)
						{
							$valid = false;
						}
					}
					if($valid && $b + $check_craftable_columns < 3)
					{
						if($craftbox[0][2][1] != 0 or $craftbox[1][2][1] != 0 or $craftbox[2][2][1] != 0)
						{
							$valid = false;
						}
					}
					if($valid && $a > 0)
					{
						if($craftbox[0][0][1] != 0 or $craftbox[0][1][1] != 0 or $craftbox[0][2][1] != 0)
						{
							$valid = false;
						}
					}
					if($valid && $a > 1)
					{
						if($craftbox[1][0][1] != 0 or $craftbox[1][1][1] != 0 or $craftbox[1][2][1] != 0)
						{
							$valid = false;
						}
					}
					if($valid && $b > 0)
					{
						if($craftbox[0][0][1] != 0 or $craftbox[1][0][1] != 0 or $craftbox[2][0][1] != 0)
						{
							$valid = false;
						}
					}
					if($valid && $b > 1)
					{
						if($craftbox[0][1][1] != 0 or $craftbox[1][1][1] != 0 or $craftbox[2][1][1] != 0)
						{
							$valid = false;
						}
					}
					if($valid)
					{
						return(array($return_id,$return_amount));
					}
				}
			}
		}
	}
}
return(0);
}
if(!empty($_POST['get_output']))
{
	$returned = get_output_item($craftbox,$craftable);
	if($returned != 0)
	{
		$item_id = $returned[0];
		$amount = $returned[1];
		$insert_request = '';
		for($i = 0; $i < $amount; $i++)
		{
			if(empty($insert_request))
			{
				$insert_request = 'VALUES';
			}
			elseif(!empty($insert_request))
			{
				$insert_request .= ', ';
			}
			$insert_request .= '(\''.$item_id.'\',\''.$player_id.'\')';
		}
		mysql_pconnect($dbhost,$dbuser,$dbpass, $db) or die(mysql_error());
		$select_total_items = mysql_query("SELECT count(*) from inventories where belongs_to='$player_id'") or die(mysql_error());
		$select_total_items_array = mysql_fetch_array($select_total_items);
		$have_total_items = $select_total_items_array[0];
		if((30 - $have_total_items) >= $amount)
		{
			$insert_request = mysql_query("INSERT into inventories (item_id,belongs_to) $insert_request") or die(mysql_error());
			$delete_request = mysql_query("DELETE from inventories where $check_query and belongs_to='$player_id'") or die(mysql_error());
			$select_ids_request = mysql_query("SELECT id from inventories where item_id='$item_id' and belongs_to='$player_id' order by id DESC LIMIT 0,$amount") or die(mysql_error());
			if(mysql_num_rows($select_ids_request) == $amount)
			{
				for($e = 0; $e < $amount; $e++)
				{
					if($e != 0)
					{
						echo ';';
					}
					$select_ids_array = mysql_fetch_array($select_ids_request);
					$inv_id = $select_ids_array['id'];
					echo $inv_id . ',' . $item_id;
				}
			}
			else
			{
				echo 0;
			}
			
		}
		else
		{
			echo 0;
		}
		mysql_close();
	}
	else
	{
		echo '0';
	}
}
else
{
	$returned = get_output_item($craftbox,$craftable);
	if($returned == 0)
	{
		echo 0;
	}
	else
	{
		echo $returned[0] . ',' . $returned[1];
	}
}
?>