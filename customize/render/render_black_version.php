<?php




if(!isset($functions_included))
{
include("renderfunctions.php");
include_once("GIFEncoder.class.php");
$functions_included = 'yes';
}



$t_sprite = '../saved/'.$member_id.'/'.$use_sprite.'.gif';
$photo = create_black_version($t_sprite);
$saveto = '../saved/'.$member_id.'/'.$sprite.'.gif';
imagegif($photo,$saveto);
imagedestroy($photo);



?>