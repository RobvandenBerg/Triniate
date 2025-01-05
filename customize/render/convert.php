<?php

// Why is this different than the one in renderfunctions.php?

function png2gif($directory, $background = array(1, 1, 1), $dest = 'gif'){
$pngs = glob("$directory/*.png");
    // by WebReflection
    foreach($pngs as $png){
        $size = getimagesize($png);
        $img = imagecreatefrompng($png);
        $image = imagecreatetruecolor($width = $size[0], $height = $size[1]);
        imagefill($image, 0, 0, $bgcolor = imagecolorallocate($image, $background[0], $background[1], $background[2]));
        imagecopyresampled($image, $img, 0, 0, 0, 0, $width, $height, $width, $height);
        imagecolortransparent($image, $bgcolor);
        // Remove quality
        imagegif($image, $npath);
        imagedestroy($image);
    }
}

function single_png2gif($path,$output, $background = array(1, 1, 1), $dest = 'gif'){
$pngs = array($path);
    // by WebReflection
    foreach($pngs as $png){
        $size = getimagesize($png);
        $img = imagecreatefrompng($png);
        $image = imagecreatetruecolor($width = $size[0], $height = $size[1]);
        imagefill($image, 0, 0, $bgcolor = imagecolorallocate($image, $background[0], $background[1], $background[2]));
        imagecopyresampled($image, $img, 0, 0, 0, 0, $width, $height, $width, $height);
        imagecolortransparent($image, $bgcolor);
        // Remove quality
        imagegif($image, $output);
        imagedestroy($image);
    }
}

// example
// png2gif("../saved/1/1/walk_down");

single_png2gif("../saved/1/1/walk_down/head_1.png","../saved/1/1/walk_down/hand_1.gif");

?>