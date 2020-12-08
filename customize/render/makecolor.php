<?php
function makered(&$image, $add = 100)
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

			if($r != 1 && $g != 1 && $b != 1)
			{
				$r = $r + $add;
				if($r > 255)
				{
					$r = 255;
				}
				$g = $g - $add;
				if($g < 0)
				{
					$g = 0;
				}
				$b = $b - $add;
				if($b < 0)
				{
					$b = 0;
				}
				imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $r, $g, $b, $alpha));
			}
		}
	}
}

function create_red_version($path_to_gif)
{
	$img = imagecreatefromgif($path_to_gif);
	$width = imagesx($img);
	$height = imagesy($img);

	$image = imagecreatetruecolor($width, $height);
	imagefill($image, 0, 0, $bgcolor = imagecolorallocate($image, 1, 1, 1));
	imagecopyresampled($image, $img, 0, 0, 0, 0, $width, $height, $width, $height);
	imagecolortransparent($image, $bgcolor);
	makered($image);
	return($image);
}

/*
$my_img = create_red_version("../saved/1/2/stand_down.gif");

header('Content-type: image/gif');
imagegif($my_img);
*/







function create_black_version($path_to_gif)
{
	$img = imagecreatefromgif($path_to_gif);
	$width = imagesx($img);
	$height = imagesy($img);

	$image = imagecreatetruecolor($width, $height);
	imagefill($image, 0, 0, $bgcolor = imagecolorallocate($image, 1, 1, 1));
	imagecopyresampled($image, $img, 0, 0, 0, 0, $width, $height, $width, $height);
	imagecolortransparent($image, $bgcolor);
	makeblack($image);
	return($image);
}



function makeblack(&$image)
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

			if($r != 1 && $g != 1 && $b != 1)
			{
				$r = 0;
				$g = 0;
				$b = 0;
				imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $r, $g, $b, $alpha));
			}
		}
	}
	return($image);
}
?>