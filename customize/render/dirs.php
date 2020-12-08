<?php
/*function create_main_dir($user_id)
{
	mkdir("$_SERVER[DOCUMENT_ROOT]/apps/triniate_spriter/saved/$user_id");
}

function get_main_dir($user_id)
{
	if(!is_dir("$_SERVER[DOCUMENT_ROOT]/apps/triniate_spriter/saved/$user_id"))
	{
		create_main_dir($user_id);
	}
	return("$_SERVER[DOCUMENT_ROOT]/apps/triniate_spriter/saved/$user_id");
}

function create_sprite_dir($user_id)
{
	$main_dir = get_main_dir($user_id);
$b = 2;
	for($m = 1; $m < $b; $m++)
	{
		if(!is_dir("$main_dir/$m"))
		{
			mkdir("$main_dir/$m");
			mkdir("$main_dir/$m/walk_up");
			mkdir("$main_dir/$m/walk_down");
			mkdir("$main_dir/$m/walk_left");
			mkdir("$main_dir/$m/walk_right");
			mkdir("$main_dir/$m/attack_up");
			mkdir("$main_dir/$m/attack_down");
			mkdir("$main_dir/$m/attack_left");
			mkdir("$main_dir/$m/attack_right");
			$the_dir = "$m";
		}
		elseif(is_dir("$main_dir/$m"))
		{
			$b++;
		}
	}
	return($the_dir);
}

function check_sprite_dir($user_id,$sprite_id)
{
	$main_dir = get_main_dir($user_id);
	$r = is_dir("$main_dir/$sprite_id");
	return($r);
}










*/






function delete_directory($dirPath)
{
	if(is_dir($dirPath))
	{
		$it = new RecursiveDirectoryIterator($dirPath);
		$files = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file)
		{
			$splitfile = explode('/', $file);
			$filename = array_pop($splitfile);
			if($filename == '..' or $filename == '.') { continue;}
    			if ($file->isDir())
			{
				rmdir($file->getRealPath());
			}
			else
			{
				unlink($file->getRealPath());
			}
		}
		rmdir($dirPath);
	}
}
?>