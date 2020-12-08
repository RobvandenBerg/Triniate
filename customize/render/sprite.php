<?php


$walk_up = array(array('weapon','head','body','legs'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),4,true,30);

$walk_down = array(array('weapon','head','body','legs'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),4,true,30);

$walk_left = array(array('weapon','head','body','legs'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),4,true,30);

$walk_right = array(array('head','body','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),4,true,30);

$attack_right = array(array('head','body','legs','weapon'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),2,false,20);

$attack_left = array(array('weapon','head','body','legs'),array('head'=>'head_','body'=>'body_','legs'=>'legs_','weapon'=>''),2,false,20);

$settings_array = array('walk_up'=>$walk_up,'walk_down'=>$walk_down,'walk_left'=>$walk_left,'walk_right'=>$walk_right,'attack_right'=>$attack_right,'attack_left'=>$attack_left);
// ^ parse order, prefix, ammount of loops, loop, framespeed



$sprite = $_GET['sprite'];
if(!$settings_array[$sprite])
{
die("Invalid sprite.");
}


$standard_part = "standard_parts/$sprite";
$standard_weapon = "standard_weapon/$sprite";

$weapon = $_GET['weapon'];
$head = $_GET['head'];
$body = $_GET['body'];
$legs = $_GET['legs'];

if($weapon == 'auto'){$weapon = $standard_weapon;}
if($head == 'auto'){$head = $standard_part;}
if($body == 'auto'){$body = $standard_part;}
if($legs == 'auto'){$legs = $standard_part;}

$total_frames = $_GET['frames'];


$get_settings = $settings_array[$_GET['sprite']];
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


// $framespeed = 30;


include("GIFEncoder.class.php");



$frames = array();
$framed = array();

for($m = 1; $m <= $total_frames; $m++)
{

	$tile_size = 20;

	$im_width = 20;
	$im_height = 30;
	$photo = load_gif("trans.gif");
	$trans_colour = imagecolorallocate($photo, 1, 1, 1);
	imagefill($photo, 0, 0, $trans_colour);



	$bodyparts = array();
//$head = 1;
//$body = 1;
//$legs = 1;

	/* $bodyparts[] = "../bodyparts/$head/walk_up/head_$m.gif";
	$bodyparts[] = "../bodyparts/$head/walk_up/body_$m.gif";
	$bodyparts[] = "../bodyparts/$head/walk_up/legs_$m.gif";*/

// echo "$head/head_$m.gif";


	foreach($layer_order as $current_layer)
	{
		$do_eval = "if(!empty(\$$current_layer)){\$bodyparts[] = \"\$$current_layer/".$prefixes[$current_layer]."$m.gif\";}";
eval($do_eval);

		// echo "$do_eval -" . $bodyparts[(count($bodyparts) - 1)] . "- ";
	}
	//$bodyparts[] = "$head/head_$m.gif";
	//$bodyparts[] = "$body/body_$m.gif";
	//$bodyparts[] = "$legs/legs_$m.gif";
	

	foreach($bodyparts as $part)
	{
		// $php = imagecreatefromgif($part);
$php = load_gif($part);


		imagesetbrush($photo, $php);

		// Draw a couple of brushes, each overlaying each
		imageline($photo, 10, 15, 10, 15, IMG_COLOR_BRUSHED);

		imagedestroy($php);

	}
	
	$temp = "temp/$m.gif";
	imagegif($photo,$temp);
	$frames[] = $temp;
	$framed[] = $framespeed;
	// imagedestroy($photo);
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
Header ( 'Content-type:image/gif' );
echo $gif->GetAnimation ( );


?>