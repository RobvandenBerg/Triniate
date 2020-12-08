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
		$output .= "-";
	}
	$currzonesplit = explode(",",$currzone);
	$output .= $currzonesplit[0] . ',' . $currzonesplit[1] . ',' . $currzonesplit[2] . ',' . $currzonesplit[3] . '_' . $currzonesplit[4] . ',' . $currzonesplit[5] . ',' . $currzonesplit[6] . ',' . $currzonesplit[7];
}

// echo "The output would be:<br><b>$output</b>";

$myFile = "../rooms/$id.txt";
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);

$data_explode = explode(";",$theData);
$newdata = "$data_explode[0];$data_explode[1];$data_explode[2];$data_explode[3];1/$output/1-7/0-20/Goblin_Caveman/50-1_50-2_100-3-5";

$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = $newdata;
fwrite($fh, $stringData);
fclose($fh);


// 3/100,100,350,350_50,50,400,400-370,610,560,840_260,480,700,1000/1-7/0-20/Goblin_Caveman/50-1_50-2_100-3-5

header("location: teleports.php?id=$id");
?>