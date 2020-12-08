<?php
if(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$id = $_GET['id'];
	if(file_exists("maps/$id.png"))
	{
		unlink("maps/$id.png");
		if(file_exists("maps/$id.txt"))
		{
			unlink("maps/$id.txt");
		}
		header("location: index.php");
		exit();
	}
	if(file_exists("maps/$id.txt"))
	{
		unlink("maps/$id.txt");
	}
}
?>
Could not undo creating the map. <a href='index.php'>Back to index</a>