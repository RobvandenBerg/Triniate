<?php

include_once(__DIR__ . '/mysql_functions.php');

if(!isset($mysql_credentials_file_location))
{
	include_once(__DIR__ . '/../github_settings.php');
} else {
	include(__DIR__ . "/../db_info.php");
}
?>