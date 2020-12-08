<?php

/*
$directory = "tiles";

$tiles=array();
$fileCount=0;
$filePath=$PATH.'/var/www/public_html/'; # Specify the path you want to look in. 
// $filePath = "moeras";
$filePath = $directory;
$dir = opendir($filePath); # Open the path
while ($file = readdir($dir)) { 
  if (eregi("\.png",$file)) { # Look at only files with a .php extension
    $tiles[count($tiles)] = array($file,$directory);
    $fileCount++;
  }
}
if ($fileCount > 0) {
  //echo sprintf("<strong>List of Files in %s</strong><br />%s<strong>Total Files: %s</strong>",$filePath,$string,$fileCount);
}

foreach($tiles as $tile)
{
echo "<a href='$tile'><img src='$tile'> $tile[0]</img></a><br>\n";
}


echo "<hr>";
*/

$directory = "tiles";

$iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory), 
            RecursiveIteratorIterator::SELF_FIRST);

foreach($iterator as $file) {
    if($file->isDir()) {
        echo $file, PHP_EOL;
	$directory = $file;
echo $directory;

$tiles = array();

$filePath = $directory;
$dir = opendir($filePath); # Open the path
while ($file = readdir($dir)) { 
  if (eregi("\.png",$file)) { # Look at only files with a .php extension
    $tiles[count($tiles)] = "$directory/$file";
    $fileCount++;
  }
}
if ($fileCount > 0) {
  //echo sprintf("<strong>List of Files in %s</strong><br />%s<strong>Total Files: %s</strong>",$filePath,$string,$fileCount);
}

foreach($tiles as $tile)
{
echo "<a href='$tile'><img src='$tile'> $tile</img></a><br>\n";
}












echo "<br>";



    }
}


?>