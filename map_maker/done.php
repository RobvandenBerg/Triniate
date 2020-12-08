<?php
if(!isset($_GET['id']) or !is_numeric($_GET['id']))
{
die("Could not find map. <a href='index.php'>Back</a>");
}
$id = $_GET['id'];
if(!file_exists("../rooms/$id/background.png"))
{
die("Could not find map. <a href='index.php'>Back</a>");
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
.textfile
{
border: 1px solid black;
background-color: gray;
}
</style>
<script language="JavaScript">
var width = <?php echo $width;?>;
var height = <?php echo $height;?>;


</script>
</head>
<body>

<img src="../rooms/<?php echo $id;?>/background.png" style="border: 1px solid black;">
<br><br>
Text file says:<br><div class='textfile'><?php
$myFile = "../rooms/$id.txt";
$fh = fopen($myFile, 'r');
$theData = fgets($fh);
fclose($fh);
echo $theData;
?>

<form action='publish.php?id=<?php echo $id;?>' method='POST'>
<input type='submit' value='Publish map and test'>
</form>
</div>
</body>
</html>