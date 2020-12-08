<?php
if(!isset($_GET['id']) or !is_numeric($_GET['id']))
{
die("Could not find map. <a href='index.php'>Back</a>");
}
$id = $_GET['id'];
if(!file_exists("../rooms/$id/background.png") or !file_exists("../rooms/$id.txt"))
{
die("Could not find map. <a href='index.php'>Back</a>");
}


$myFile = "../rooms/$id.txt";
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);
// echo $theData;
$datasplit = explode(";",$theData);
$datazones = $datasplit[1];

// echo $theData;
$preset_zones = array();

$datazonesplit = explode("'",$datazones);
for($m = 0; $m < count($datazonesplit); $m++)
{
	$czone = $datazonesplit[$m];
	$checkzonesplit = explode(",",$czone);
	if(count($checkzonesplit) == 4)
	{
		$preset_zones[count($preset_zones)] = $czone;
	}
}

list($width, $height, $type, $attr) = getimagesize("../rooms/$id/background.png");

/*echo "Image width " .$width;
echo "<BR>";
echo "Image height " .$height;
echo "<BR>";
echo "Image type " .$type;
echo "<BR>";
echo "Attribute " .$attr . "<br>";
?>
<style>
.map
{
border: 1px solid black;
}
</style>
<script>
var width = <?php echo $width;?>;
var height = <?php echo $height;?>;
</script>
<?php
echo "<img src=\"../rooms/$id.png\" ".$attr." class=\"map\">";
*/
?>
<html>
<head>
<style>
.delete_zone
{
color: red;
}
#pointer_div
{
border: 2px solid black;
position: absolute;
top: 0px;
left: 0px;
}
#not_picture
{
border: 2px solid black;
position: absolute;
top: <?php echo ($height + 20);?>px;
left: 0px;
}
</style>
<script language="JavaScript">
var width = <?php echo $width;?>;
var height = <?php echo $height;?>;

var click = 0;
<?php
echo "var zones = new Array(";
$alzet = false;
foreach($preset_zones as $pzone)
{
	if($alzet == true)
	{
		echo ",";
	}
	echo "Array($pzone)";
	$alzet = true;
}
?>);
var curx = 0;
var cury = 0;
function point_it(event){
	pos_x = event.offsetX?(event.offsetX):event.pageX-document.getElementById("pointer_div").offsetLeft - 2;
	pos_y = event.offsetY?(event.offsetY):event.pageY-document.getElementById("pointer_div").offsetTop - 2;
	/*document.getElementById("cross").style.left = (pos_x-1) ;
	document.getElementById("cross").style.top = (pos_y-15) ;
	document.getElementById("cross").style.visibility = "visible" ;
	document.pointform.form_x.value = pos_x;
	document.pointform.form_y.value = pos_y;*/


	if(pos_x < 0 || pos_y < 0 || pos_x > width || pos_y > height)
	{
		alert('invalid click');
		return;
	}
	

	if(click == 0)
	{
		curx = parseInt(pos_x);
		cury = parseInt(pos_y);
	}
	if(click == 1)
	{
		var newx = parseInt(pos_x);
		var newy = parseInt(pos_y);
		if(newx <= curx || newy <= cury)
		{
			alert('First click the upper left point, THEN the bottom right point.');
			click = -1;
		}
		else
		{

			var zone_id = zones.length;

			var newdiv = document.createElement('div');
			var divIdName = 'zone_'+zone_id;
			newdiv.setAttribute('id',divIdName);
			newdiv.style.width = (newx-curx)+"px";
			newdiv.style.height = (newy-cury)+"px";
			newdiv.style.left = (curx - 1) + "px";
			newdiv.style.top = (cury - 1) + "px";
			newdiv.style.position = "absolute";
			newdiv.style.background = "yellow";
			newdiv.style.opacity = '0.5';
			newdiv.style.border = "1px solid black";
			newdiv.innerHTML = "<span class='delete_zone' oNclick='delete_zone("+zone_id+");'>X</span>";
			document.getElementById('pointer_div').appendChild(newdiv);
			click = -1;
			zones[zone_id] = new Array(curx,cury,newx,newy);
		}
	}
	click++;
}

function delete_zone(id)
{
//alert('impossibru: '+id);
click = - 1;
//alert(zones[id]);
zones.splice(id,1,'');

remove_element('pointer_div','zone_'+id);
}


function remove_element(parentDiv, childDiv){
     if (childDiv == parentDiv) {
          alert("The parent div cannot be removed.");
     }
     else if (document.getElementById(childDiv)) {     
          var child = document.getElementById(childDiv);
          var parent = document.getElementById(parentDiv);
          parent.removeChild(child);
     }
     else {
          alert("Child div has already been removed or does not exist.");
          return false;
     }
}


function done()
{
	document.getElementById('sendzones').value = '';
	for(var o in zones)
	{
		var currzone = zones[o];
		if(currzone != '')
		{
			var lc = currzone[0];
			var tc = currzone[1];
			var rc = currzone[2];
			var bc = currzone[3];
			if(document.getElementById('sendzones').value != '')
			{
				document.getElementById('sendzones').value += ';';
			}
			document.getElementById('sendzones').value += lc+','+tc+','+rc+','+bc;
		}
	}
	toggle_element('finish_button');
	toggle_element('submit_div');
}
function toggle_element(id)
{
if(document.getElementById(id).style.display=='none')
{
document.getElementById(id).style.display='inline';
}
else if(document.getElementById(id).style.display=='inline')
{
document.getElementById(id).style.display='none';
}
}
</script>
</head>
<body>
<form name="pointform" method="post">
<div id="pointer_div" onclick="point_it(event)" style = "background-image:url('../rooms/<?php echo $id;?>/background.png');width:<?php echo $width;?>px;height:<?php echo $height;?>px;"><?php


for($a = 0; $a < count($preset_zones); $a++)
{
	$pzone = $preset_zones[$a];
	$zsplit = explode(",",$pzone);
	$lc = $zsplit[0];
	$tc = $zsplit[1];
	$rc = $zsplit[2];
	$bc = $zsplit[3];
	echo "<div id='zone_$a' style='width: " . ($rc - $lc) . "px; height: ". ($bc - $tc) . "px; left: ".$lc."px; top: ".$tc."px; position: absolute; background-color: yellow; opacity: 0.5; border: 1px solid black;'><span class='delete_zone' oNclick='delete_zone($a);'>X</span></div>";
}
?>
<img src="point.gif" id="cross" style="position:relative;visibility:hidden;z-index:2;">
</div>
</form>
<div id='not_picture'>
Set the areas where you can not walk.<br>Press the upper left spot, then the bottom right spot to create a non-walkable area.<br>
<br>
Please do not set more than 5 of these spots, or the game will have major lag.<br>
<br>
<input type='button' value='Done' oNclick='done();' id='finish_button' style='display: inline;'>
<div id='submit_div' style='display: none;'>
<form action='savezones.php?id=<?php echo $id;?>' method='post'>
<input type='hidden' name='sendzones' id='sendzones'>
Are you sure you want to finish this step?<br>
<input type='button' oNclick="toggle_element('finish_button'); toggle_element('submit_div');" value='No'> <input type='submit' value='Yes, finish step'>
</form></div>
</div>
</body>
</html>