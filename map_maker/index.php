<html>
<head>
<title>
Triniate - Map maker
</title>
<style>
td
{
width: 20px;
height: 20px;
}

.tile_normal
{
border: 1px solid black;
}

.tile_selected
{
border: 2px solid red;
}
</style>
<script>
function construct()
{
var dcolumns = document.getElementById('columns').value;
var drows = document.getElementById('rows').value;
window.location = 'construct.php?rows='+drows+'&columns='+dcolumns;
}
</script>
</head>
<body>
<div id='screen'>
<?php
$directory = '../rooms';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::SELF_FIRST);
foreach($iterator as $file)
{
    if($file->isDir())
	{
		$directory = $file;
		$filePath = $directory;
		$dir = opendir($filePath);
		$expfile = explode('/', $filePath);
		$filename = array_pop($expfile);
		if($filename == '.' or $filename == '..') { continue;}
		while ($file = readdir($dir))
		{
			if($file == 'info.txt')
			{
				$expdir = explode('/',$directory);
				$mapnumber = $expdir[count($expdir)-1];
				echo '<a href="zones.php?id='.$mapnumber.'">Map '.$mapnumber.'</a><br>';
			}
		}
	}
}
?>
<form action='newmap.php' method='POST'>
<b>Create new map:</b><br>
Map ID: <input type='text' name='mapid' value='<?php echo ($mapnumber + 1);?>'><br>
Columns: <input type='text' value='20' name='columns'><br>
Rows: <input type='text' value='20' name='rows'><br>
<input type='submit' value='Create map'>
</form>
</div>
</body>
</html>