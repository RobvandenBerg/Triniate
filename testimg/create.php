<?php

if(empty($_POST['path']))
{
	die('error.');
}

$path = $_POST['path'];
$extsplit = explode('.',$path);
if($extsplit[count($extsplit)-1] != 'png')
{
	die('File is not png');
}

if(!file_exists($path))
{
	die('File does not exist.');
}

// echo 'Good.';

include('includer.php');

function make_masked_version(&$image, $opacity_array)
{
	$width = imagesx($image);
	$height = imagesy($image);
	for($x = 0; $x < $width; $x++)
	{
		for($y = 0; $y < $height; $y++)
		{
			$rgb = imagecolorat($image, $x, $y);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			$alpha = ($rgb & 0x7F000000) >> 24;
			
			$alpha = $opacity_array[$y][$x];

			imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $r, $g, $b, $alpha));
		}
	}
}

function render_tile_directory($path_to_png,$output_directory,$opacity_arrays)
{
	if(file_exists($output_directory))
	{
		die('Output directory already exists!');
	}
	mkdir($output_directory);
	$tname = 0;
	$image = imagecreatefrompng($path_to_png);
	imagealphablending($image, false);
	imagesavealpha($image, true);
	imagepng($image,$output_directory . '/' . $tname . '.png');
	foreach($opacity_arrays as $opacity_array)
	{
		$tname++;
		make_masked_version($image,$opacity_array);
		imagepng($image,$output_directory . '/' . $tname . '.png');
	}
}
$directory_path = $_POST['directory_path'];
render_tile_directory($path,$directory_path,$opacity_arrays);
echo 'Succesfully rendered tile directory in <a href="'.$directory_path.'">'.$directory_path.'</a>';
?>