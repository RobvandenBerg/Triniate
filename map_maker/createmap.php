<?php
$id = round($_GET['id']);
$mapid = $id;
function imagecreatefromfile( $filename ) {
    if (!file_exists($filename)) {
        throw new InvalidArgumentException('File "'.$filename.'" not found.');
    }
    switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
        case 'jpeg':
        case 'jpg':
            return imagecreatefromjpeg($filename);
        break;

        case 'png':
            return imagecreatefrompng($filename);
        break;

        case 'gif':
            return imagecreatefromgif($filename);
        break;

        default:
            throw new InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
        break;
    }
}
/*foreach($_POST as $post_name => $post_value)
{
echo "<b>$post_name</b><br>$post_value<br><br>";
}*/

if(isset($_GET['columns']) && is_numeric($_GET['columns']))
{
$columns = $_GET['columns'];
}
else
{
$columns = 20;
}
if(isset($_GET['rows']) && is_numeric($_GET['rows']))
{
$rows = $_GET['rows'];
}
else
{
$rows = 20;
}

$tile_size = 20;

$im_width = $columns * $tile_size;
$im_height = $rows * $tile_size;



//$im_width = 191; 
//$im_height = 191;

// $photo = imagecreatefromjpeg('../../apps/canvas/standard.jpeg');


function get_file_extension($file_name) {
  return substr(strrchr($file_name,'.'),1);
}

$photo = imagecreatetruecolor($im_width,$im_height);
$white = imagecolorallocate($photo, 255, 255, 255);
imagefilledrectangle($photo, 0, 0, $im_width, $im_height, $white);

if($_POST['bgimg'])
{
	$bgimg = $_POST['bgimg'];
	$ctent = @file_get_contents($bgimg);
	if($ctent)
	{
		$fext = get_file_extension($bgimg);
		$tempimg = 'temp/'.rand(0,100) . time() .'.'.$fext;
		$tempimg2 = 'temp/'.rand(0,100) . time() .'.jpg';
		file_put_contents($tempimg,$ctent);
		$php = imagecreatefromfile($tempimg);
		$pw = ceil(imagesx($php) / 2);
		$ph = ceil(imagesy($php)/2);
		echo 'Dimensions: '.$pw . 'x'.$ph.'<br>';
		imagesetbrush($photo, $php);
		imageline($photo, $pw, $ph, $pw, $ph, IMG_COLOR_BRUSHED);
		imagedestroy($php);
		unlink($tempimg);
	}
	// file_put_contents($img, file_get_contents($url));
}



$sendstring = $_POST['sendstring'];
// echo $sendstring;
$s_explode = explode(";",$sendstring);

$tile_array_1 = array();
$tile_array_2 = array();
for($m = 0; $m < count($s_explode); $m++)
{
	$ce = $s_explode[$m];
	$ce_explode = explode(":",$ce);
	$coords = $ce_explode[0];
	$tile = $ce_explode[1];
	$coords_explode = explode(',',$coords);
	$layer = $coords_explode[2];
	if($layer == 1)
	{
		$tile_array_1[] = array($coords_explode[0],$coords_explode[1],$tile);
	}
	if($layer == 2)
	{
		$tile_array_2[] = array($coords_explode[0],$coords_explode[1],$tile);
	}
}

$total_tiles = count($tile_array_1) + count($tile_array_2);
$array_1_length = count($tile_array_1);
for($a = 0; $a < $total_tiles; $a++)
{
	if($a >= $array_1_length)
	{
		$tile_array[$a] = $tile_array_2[$a - $array_1_length];
	}
	else
	{
		$tile_array[$a] = $tile_array_1[$a];
	}
}
for($m = 0; $m < count($tile_array); $m++)
{
	$current_tile_array = $tile_array[$m];
	$pos_x = $current_tile_array[0];
	$pos_y = $current_tile_array[1];
	$tile = $current_tile_array[2];
	$left = $pos_x *$tile_size - $tile_size/2;
	$top = $pos_y * $tile_size - $tile_size/2;
	// echo "left: $left. Top: $top. Tile: $tile.<br>";
	

$extarray = explode(".",$tile);
$ext = strtolower(end($extarray));
if($ext == 'png')
{
	$php = imagecreatefrompng($tile);
}
elseif($ext == 'gif')
{
$php = imagecreatefromgif($tile);
}

	imagesetbrush($photo, $php);

	// Draw a couple of brushes, each overlaying each
	imageline($photo, $left, $top, $left, $top, IMG_COLOR_BRUSHED);

	imagedestroy($php);
}






// header('Content-Type: image/png');


/*
for($a = 2000; $a > 0; $a--)
{
	if(!file_exists("../rooms/$a.png"))
	{
		$savepng = "../rooms/$a.png";
		$mapid = $a;
	}
}*/
$savepng = "../rooms/$mapid/background.png";


if(!isset($savepng))
{
die('No more map space');
}
imagepng($photo,$savepng);

$myFile = "../rooms/$mapid.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = "$im_width,$im_height,rooms/$mapid/background.png;;;;";
fwrite($fh, $stringData);
fclose($fh);

// imagejpeg($photo,$savejpeg);
imagedestroy($photo);


echo "Are you happy with this map?<br><img src='$savepng'><br>";

?>

<input type='button' value='Yes' oNclick="window.location='zones.php?id=<?php echo $mapid;?>';"> <input type='button' value='No' oNclick="window.location='cancel.php?id=<?php echo $mapid;?>';">