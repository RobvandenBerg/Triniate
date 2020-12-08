<?php

include_once(__DIR__ . '/mysql_functions.php');

if(!$mysql_credentials_file_location)
{
	include_once(__DIR__ . '/../github_settings.php');
}

include($mysql_credentials_file_location);

?>