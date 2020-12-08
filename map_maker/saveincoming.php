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
	$output .= $currzone;
}

// echo "The output would be:<br><b>$output</b>";

$myFile = "../rooms/$id.txt";
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);

$data_explode = explode(";",$theData);


$newdata = "$data_explode[0];$data_explode[1];$output;$data_explode[3];$data_explode[4]";

$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = $newdata;
fwrite($fh, $stringData);
fclose($fh);

// echo "Output would be:<br>$newdata";


header("location: done.php?id=$id");
?>