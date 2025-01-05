<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start includes
// echo __DIR__ . "/db_info.php";
include_once(__DIR__ . "/index.php");

function full_login()
{
	global $dbhost, $dbuser, $dbpass, $db, $logged_in, $user_id, $username, $login_error;
	session_start();
	if(isset($_POST['username']) and isset($_POST['password']))
	{
		$username = $_POST['username'];
		$password = md5($_POST['password']);
	}
	else if(isset($_SESSION['username']) and isset($_SESSION['password']))
	{
		$username = $_SESSION['username'];
		$password = $_SESSION['password'];
	}
	else if(isset($_COOKIE['username']) and isset($_COOKIE['password']))
	{
		$username = $_COOKIE['username'];
		$password = $_COOKIE['password'];
	}
	if(isset($username) and isset($password) and !isset($_POST['logout']))
	{
		// For some unknown reason, new mysqli() works but not mysqli_connect()
		// Update 1: It still doesn't work.
		$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $db);
		$result = $mysqli -> query('SELECT id, username, password_hash from users where username="'.$mysqli->real_escape_string($username).'"') or die($mysqli -> error);
		if($result -> num_rows < 1)
		{
			$login_error = 'Invalid username/password';
		}
		else
		{
			$data = $result -> fetch_assoc();
			if(!password_verify($password, $data['password_hash']))
			{
				$login_error = 'Invalid username/password';
			}
			else
			{
				$user_id = $data['id'];
				$username = $data['username'];
				$logged_in = true;
				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;
				if(isset($_POST['remember_me']))
				{
					setcookie('username', $username, time()+60*60*24*365);
					setcookie('password', $password, time()+60*60*24*365);
				}
			}
		}
	}
	else if(isset($_POST['logout']))
	{
		$_SESSION['username'] = '';
		$_SESSION['password'] = '';
		setcookie('username', '', time());
		setcookie('password', '', time());
	}
	
}

function light_login($include_when_dead = false)
{
	global $_SESSION,$player_id,$mg,$room,$time,$betatester;

	session_start();
	if(isset($_SESSION['playerid']) && is_numeric($_SESSION['playerid']) && isset($_SESSION['mg']))
	{
		$player_id = $_SESSION['playerid'];
		$mg = htmlentities($_SESSION['mg']);
		$room1 = $_SESSION["room"] ?? "0";
		if($room1 = "0"){
			$_SESSION["room"] = "0";		
		}
		$room = htmlentities($room1);
		$betatester = false;
		if($_SESSION['betatester'] == 1)
		{
			$betatester = true;
		}
		if($mg == obtain_mg($player_id))
		{
			$time = time();
		}
		else
		{
			if($include_when_dead !== false)
			{
				include($include_when_dead);
				die();
			}
			die('Please login!');
		}
	}
	else
	{
		if($include_when_dead !== false)
		{
			include($include_when_dead);
			die();
		}
		die('login');
	}
	$room = get_room();
}

function get_room()
{
	global $_SESSION;
	// Debug
	// return "0";
	// Production
	return $_SESSION["room"];
	
}
?>