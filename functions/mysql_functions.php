<?php

if(!function_exists('mysql_connect'))
{
	function mysql_connect($dbhost, $dbuser, $dbpass, $db = false)
	{
		global $mysqli;
		if($db)
		{
			$mysqli = mysqli_connect($dbhost, $dbuser, $dbpass, $db);
			return $mysqli;
		}
		$mysqli = mysqli_connect($dbhost, $dbuser, $dbpass);
		return $mysqli;
	}

	function mysql_select_db($db)
	{
		global $mysqli;
		$mysqli -> select_db($db);
		return $mysqli;
	}

	function mysql_query($q)
	{
		global $mysqli;
		return $mysqli -> query($q);
	}

	function mysql_num_rows($r)
	{
		global $mysqli;
		return $r -> num_rows;
	}

	function mysql_fetch_row($r)
	{
		return $r -> fetch_row();
	}

	function mysql_close()
	{
		global $mysqli;
		return $mysqli -> close();
	}

	function mysql_fetch_array($r)
	{
		return $r -> fetch_array();
	}

	function mysql_error()
	{
		global $mysqli;
		return $mysqli -> error;
	}

	function mysql_pconnect($dbhost, $dbuser, $dbpass, $db = false)
	{
		return mysql_connect($dbhost, $dbuser, $dbpass, $db);
	}
}
?>