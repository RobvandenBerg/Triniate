<?php

/**
 * Flip (mirror) an image left to right.
 *
 * @param image  resource
 * @param x      int
 * @param y      int
 * @param width  int
 * @param height int
 * @return bool
 * @require PHP 3.0.7 (function_exists), GD1
 */
function imageflip(&$image, $x = 0, $y = 0, $width = null, $height = null)
{
    if ($width  < 1) $width  = imagesx($image);
    if ($height < 1) $height = imagesy($image);
    // Truecolor provides better results, if possible.
    if (function_exists('imageistruecolor') && imageistruecolor($image))
    {
        $tmp = imagecreatetruecolor(1, $height);
    }
    else
    {
        $tmp = imagecreate(1, $height);
    }
    $x2 = $x + $width - 1;
    for ($i = (int) floor(($width - 1) / 2); $i >= 0; $i--)
    {
        // Backup right stripe.
        imagecopy($tmp,   $image, 0,        0,  $x2 - $i, $y, 1, $height);
        // Copy left stripe to the right.
        imagecopy($image, $image, $x2 - $i, $y, $x + $i,  $y, 1, $height);
        // Copy backuped right stripe to the left.
        imagecopy($image, $tmp,   $x + $i,  $y, 0,        0,  1, $height);
    }
    imagedestroy($tmp);
    return true;
}

function load_gif($imgname)
{
    /* Attempt to open */
    $im = @imagecreatefromgif($imgname);

    /* See if it failed */
    if(!$im)
    {
        /* Create a blank image */
        $im = imagecreatetruecolor (800, 500);
        $bgc = imagecolorallocate ($im, 255, 255, 0);
        $tc = imagecolorallocate ($im, 100, 100, 100);

        // imagefilledrectangle ($im, 0, 0, 80, 500, $bgc);

        /* Output an error message */
        imagestring ($im, 1, 650, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}



// images/3DSPlaza_header2.png
$image = imagecreatefrompng("../../../images/3DSPlaza_header2.png");
// $image = $php;

// $image = imagecreate(190, 60);
// $background = imagecolorallocate($image, 100, 0,   0);
// $color      = imagecolorallocate($image, 200, 100, 0);
imageflip($image);
header("Content-Type: image/jpeg");
imagejpeg($image);

?>