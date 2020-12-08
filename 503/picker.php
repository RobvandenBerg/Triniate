<?php
if ($handle = opendir('optimized'))
{
    echo 'Directory handle: '.$handle.'<br>';
    echo 'Entries:<br>';

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle)))
	{
		$checker = str_replace('.','',$entry);
		if(!empty($checker))
		{
			$game_pages[] = 'optimized/'.$entry;
		}
    }

    closedir($handle);
}

$pickfile = $game_pages[rand(0,(count($game_pages) - 1))];
echo '<h1>'.$pickfile.'</h1>';
$gcontent = file_get_contents($pickfile);
file_put_contents('page.html',$gcontent);
?>