<?php
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


function png2gif($path,$output, $background = array(1, 1, 1), $dest = 'gif'){
// Why is this an array if it's only one argument?
$pngs = array($path);
    // by WebReflection
    foreach($pngs as $png){
        $size = getimagesize($png);
        $img = imagecreatefrompng($png);
        debug_backtrace();
        $image = imagecreatetruecolor($width = $size[0], $height = $size[1]);
        imagefill($image, 0, 0, $bgcolor = imagecolorallocate($image, $background[0], $background[1], $background[2]));
        imagecopyresampled($image, $img, 0, 0, 0, 0, $width, $height, $width, $height);
        imagecolortransparent($image, $bgcolor);

        // Removed quality

        imagegif($image, $output);
        imagedestroy($image);
    }
}

include('dirs.php');
include('makecolor.php');
?>