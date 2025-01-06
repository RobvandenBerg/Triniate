<?php


$stand_up = array(array('body','head','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),1,false,0);

$stand_down = array(array('head','body','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),1,false,0);

$stand_left = array(array('weapon','head','body','legs'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),1,false,0);

$stand_right = array(array('head','body','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),1,false,0);



$walk_up = array(array('body','head','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),4,true,30);

$walk_down = array(array('head','body','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),4,true,30);

$walk_left = array(array('weapon','head','body','legs'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),4,true,30);

$walk_right = array(array('head','body','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),4,true,30);

$attack_up = array(array('weapon','body','head','legs'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),2,false,20);

$attack_down = array(array('head','body','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),2,false,20);

$attack_right = array(array('head','body','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),2,false,20);

$attack_left = array(array('weapon','head','body','legs'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),2,false,20);

$settings_array = array('stand_up'=>$stand_up,'stand_down'=>$stand_down,'stand_left'=>$stand_left,'stand_right'=>$stand_right,'walk_up'=>$walk_up,'walk_down'=>$walk_down,'walk_left'=>$walk_left,'walk_right'=>$walk_right,'attack_up'=>$attack_up,'attack_down'=>$attack_down,'attack_right'=>$attack_right,'attack_left'=>$attack_left);
// ^ parse order, prefix, ammount of loops, loop, framespeed


if(!$settings_array[$sprite])
{
die('Invalid sprite.');
}

$use_sprite = $sprite;
$doplus = 0;

if($sprite == 'stand_down' or $sprite == 'stand_up' or $sprite == 'stand_left' or $sprite == 'stand_right')
{
	$ssplit = explode('_',$sprite);
	$use_sprite = "walk_$ssplit[1]";
	$doplus = 1;
}


$standard_part = "standard_parts/$use_sprite";
$standard_weapon = "standard_weapon/$use_sprite";

$weapon = $_GET['weapon'];
$head = $_GET['head'];
$body = $_GET['body'];
$legs = $_GET['legs'];

$head = "../../bodyparts/$uhead/$use_sprite";
$body = "../../bodyparts/$ubody/$use_sprite";
$legs = "../../bodyparts/$ulegs/$use_sprite";
if($uweapon != 0 && file_exists("../../weapons/$uweapon/$use_sprite"))
{
	$weapon = "../../weapons/$uweapon/$use_sprite";
}


if($weapon == 'auto'){$weapon = $standard_weapon;}
if($head == 'auto'){$head = $standard_part;}
if($body == 'auto'){$body = $standard_part;}
if($legs == 'auto'){$legs = $standard_part;}


$get_settings = $settings_array[$sprite];
$layer_order = $get_settings[0];
$prefixes = $get_settings[1];
$total_frames = $get_settings[2];
$do_loop = $get_settings[3];
$framespeed = $get_settings[4];
if($do_loop)
{
$ammount_of_loops = 0;
}
else
{
$ammount_of_loops = 1;
}







if(!isset($functions_included))
{
include("renderfunctions.php");
include("GIFEncoder.class.php");
$functions_included = 'yes';
}

// $framespeed = 30;



$frames = array();
$framed = array();

if(!is_dir("temp"))
{
	mkdir("temp");
}

$random_dir = rand(0,10000);
$temp_dir = "temp/$random_dir";
mkdir($temp_dir);

for($m = 1; $m <= $total_frames; $m++)
{
	$n = $m + $doplus;
	$tile_size = 20;

	$im_width = 20;
	$im_height = 30;
	$photo = load_gif('trans.gif');
	$trans_colour = imagecolorallocate($photo, 1, 1, 1);
	imagefill($photo, 0, 0, $trans_colour);



	$bodyparts = array();

	foreach($layer_order as $current_layer)
	{
		$do_eval = "if(!empty(\$$current_layer)){\$bodyparts[] = \"\$$current_layer/".$prefixes[$current_layer]."$n\";}";
eval($do_eval);

		if($sprite == 'walk_down')
		{
			//echo "walk_down: $do_eval -" . $bodyparts[(count($bodyparts) - 1)] . "- <br>";
		}
	}
	

	foreach($bodyparts as $part)
	{

png2gif("$part.png","$part.gif");

$php = load_gif("$part.gif");



		imagesetbrush($photo, $php);

		// Draw a couple of brushes, each overlaying each
		imageline($photo, 10, 15, 10, 15, IMG_COLOR_BRUSHED);

		imagedestroy($php);

	}
	
	$temp = "$temp_dir/$m.gif";
	imagegif($photo,$temp);
	$frames[] = $temp;
	$framed[] = $framespeed;
	imagedestroy($photo);
}





/*
		GIFEncoder constructor:
        =======================

		image_stream = new GIFEncoder	(
							URL or Binary data	'Sources'
							int					'Delay times'
							int					'Animation loops'
							int					'Disposal'
							int					'Transparent red, green, blue colors'
							int					'Source type'
						);
*/
$gif = new GIFEncoder	(
							$frames,
							$framed,
							$ammount_of_loops,
							2,
							1, 1, 1,
							"url"
		);
/*
		Possibles outputs:
		==================

        Output as GIF for browsers :
        	- Header ( 'Content-type:image/gif' );
        Output as GIF for browsers with filename:
        	- Header ( 'Content-disposition:Attachment;filename=myanimation.gif');
        Output as file to store into a specified file:
        	- FWrite ( FOpen ( "myanimation.gif", "wb" ), $gif->GetAnimation ( ) );
*/

$giftext = $gif->GetAnimation ( );

/*
Header ( 'Content-type:image/gif' );
echo $giftext;
*/

delete_directory($temp_dir);

if(!is_dir("../saved"))
{
	mkdir("../saved");
}

if(!is_dir("../saved/$member_id"))
{
	mkdir("../saved/$member_id");
}
$myFile = "../saved/$member_id/$sprite.gif";
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = $giftext;
fwrite($fh, $stringData);
fclose($fh);


?>