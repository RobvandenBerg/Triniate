<?php
function update_map_info_file($mapdir)
{
	$new_content = file_get_contents($mapdir.'/background.txt') . ';' . file_get_contents($mapdir.'/walls.txt') . ';' . file_get_contents($mapdir.'/incoming.txt') . ';' . file_get_contents($mapdir.'/objects.txt') . ';' . file_get_contents($mapdir.'/enemies.txt') . ';' . file_get_contents($mapdir.'/info.txt');
	
	file_put_contents($mapdir . '/info.txt',$new_content);
}
?>