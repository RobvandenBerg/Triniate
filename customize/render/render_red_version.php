<?php


$total_frames = 2;
$framespeed = 20;
$ammount_of_loops = 1;







if(!isset($functions_included))
{
include("renderfunctions.php");
include_once("GIFEncoder.class.php");
$functions_included = 'yes';
}

// $framespeed = 30;



$frames = array();
$framed = array();


$random_dir = rand(0,10000);
$temp_dir = "temp/$random_dir";
mkdir($temp_dir);

$t_sprite = "../saved/$member_id/$use_sprite.gif";
$photo = create_red_version($t_sprite);
$temp = "$temp_dir/1.gif";
imagegif($photo,$temp);
$frames[] = $temp;
$framed[] = $framespeed;
imagedestroy($photo);


$photo = imagecreatefromgif($t_sprite);
$temp = "$temp_dir/2.gif";
imagegif($photo,$temp);
$frames[] = $temp;
$framed[] = $framespeed;
imagedestroy($photo);




$gif = new GIFEncoder	(
							$frames,
							$framed,
							$ammount_of_loops,
							2,
							1, 1, 1,
							"url"
		);


$giftext = $gif->GetAnimation ( );

delete_directory($temp_dir);

$myFile = "../saved/$member_id/$sprite.gif";
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = $giftext;
fwrite($fh, $stringData);
fclose($fh);


?>