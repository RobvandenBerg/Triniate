<?php

function load_transparent_png($source)
{
$photo = imagecreatefromgif("trans.gif");
$trans_colour = imagecolorallocate($photo, 1, 1, 1);
imagefill($photo, 0, 0, $trans_colour);

$im = imagecreatefrompng($source);

imagesetbrush($photo, $im);

// Draw a couple of brushes, each overlaying each
imageline($photo, 10, 15, 10, 15, IMG_COLOR_BRUSHED);

imagedestroy($im);

return($photo);
}

$head = "../saved/$member_id/$sprite_id/$sprite";
$s = "../saved/1/1/walk_down/head_1.png";
$image = load_transparent_png($s);
Header ( 'Content-type:image/png' );
imagepng($image);
?>