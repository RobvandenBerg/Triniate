<?php
if(!isset($_GET['id']) or !is_numeric($_GET['id']))
{
	die("Invalid id. <a href='index.php'>Back</a>");
}
$id = $_GET['id'];
if(!file_exists("../rooms/$id/background.png") or !file_exists("../rooms/$id.txt"))
{
	die("Invalid id. <a href='index.php'>Back</a>");
}

$output = '';

$zones = $_POST['sendzones'];
// echo "<b>$zones</b><hr>";
$zonesplit = explode(";",$zones);
for($m = 0; $m < count($zonesplit); $m++)
{
	$currzone = $zonesplit[$m];
	if($output != '')
	{
		$output .= ",";
	}
	$output .= "'" . $currzone . "'";
}

// echo "The output would be:<br><b>$output</b>";

$myFile = "../rooms/$id.txt";
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);

$data_explode = explode(";",$theData);











$inzones = $data_explode[1];
// echo $theData . "<hr>";
$inzone_explode = explode("'",$inzones);
for($b = 0; $b < count($inzone_explode); $b++)
{
	$czone = str_replace("'","",$inzone_explode[$b]);
	$czonesplit = explode(",",$czone);
	if(count($czonesplit) > 2)
	{
		if(isset($czonesplit[4]) && $czonesplit[4] == 'teleport')
		{
			if($already_in != '')
			{
				$already_in .= ",";
			}
			$already_in .= "'$czone'";
		}
	}
}

if(!empty($output) && !empty($already_in))
{
	$already_in .= ",";
}














$newdata = "$data_explode[0];$already_in$output;$data_explode[2];$data_explode[3];$data_explode[4]";

$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = $newdata;
fwrite($fh, $stringData);
fclose($fh);


header("location: enemy_zones.php?id=$id");
?>