<?php
include("../include_this.php");
light_login();

if(!isset($_POST['sendvalue']) or empty($_POST['sendvalue']))
{
	die("No sendvalue");
}

$sendvaluesplit = explode(";",$_POST['sendvalue']);
if(count($sendvaluesplit) != 3)
{
	die("Incorrect sendvalue");
}

foreach($sendvaluesplit as $value)
{
	if(!is_numeric($value) or empty($value))
	{
		die("Incorrect value");
	}
}

$head = $sendvaluesplit[0];
$body = $sendvaluesplit[1];
$legs = $sendvaluesplit[2];




$pid = $player_id;


mysql_pconnect($dbhost, $dbuser, $dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());

$update_parts_request = mysql_query("UPDATE position set head='$head', body='$body', legs='$legs' where id='$pid'") or die(mysql_error());

mysql_close();


function load_gif($imgname)
{
    /* Attempt to open */
    $im = @imagecreatefromgif($imgname);

    /* See if it failed */
    if(!$im)
    {
        /* Create a blank image */
        $im = imagecreatetruecolor (1000, 30);
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc = imagecolorallocate ($im, 50, 50, 50);

        imagefilledrectangle ($im, 0, 0, 1000, 30, $bgc);

        /* Output an error message */
        imagestring ($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}

$total_frames = 4;
$framespeed = 30;


include("GIFEncoder.class.php");

//$frames[0] = "frames/images01.gif";

//$framed[0] = 100;



$frames = array();
$framed = array();

for($m = 1; $m <= $total_frames; $m++)
{

	$tile_size = 20;

	$im_width = 20;
	$im_height = 30;


	/*$photo = imagecreatetruecolor($im_width,$im_height);
	imagesavealpha($photo, true);

	$trans_colour = imagecolorallocatealpha($photo, 0, 0, 0, 127);
	imagefill($photo, 0, 0, $trans_colour);*/


	$photo = load_gif("trans.gif");
	//imagesavealpha($photo, true);

	//$trans_colour = imagecolorallocatealpha($photo, 0, 0, 0, 127);
	//imagefill($photo, 0, 0, $trans_colour);
$trans_colour = imagecolorallocate($photo, 30, 30, 30);
	imagefill($photo, 0, 0, $trans_colour);



	$bodyparts = array();

	$bodyparts[] = "../bodyparts/$head/walk_up/head_$m.gif";
	$bodyparts[] = "../bodyparts/$body/walk_up/body_$m.gif";
	$bodyparts[] = "../bodyparts/$legs/walk_up/legs_$m.gif";
	

	foreach($bodyparts as $part)
	{
		//$php = imagecreatefromgif($part);
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
							0,
							2,
							30, 30, 30,
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
$gifcontent = $gif->GetAnimation ( );

if(!is_dir("saved/$pid"))
{
mkdir("saved/$pid");
}

$myFile = "saved/$pid/walk_back.gif";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $gifcontent);
fclose($fh);


echo "Sprite created. <a href='../redirect.php'>Play Triniate</a>";

?>