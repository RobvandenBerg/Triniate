<?php
if($_SESSION['client'] == 'wiiu')
{
	header('location: wiiuclient.php?redirected=true&r='.rand());
	exit();
}
header('location: client.php?r='.rand());
?>