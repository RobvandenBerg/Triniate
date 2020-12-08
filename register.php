<?php
include('include_this.php');

full_login();
if($logged_in)
{
	exit('You cannot register while being logged in.<br><a href="./">Back</a>');
}

if($_POST['register'])
{
	$register_username = $_POST['register_username'];
	$register_password = $_POST['register_password'];
	$register_password_check = $_POST['register_password_check'];
	
	if(!$register_username)
	{
		$error = 'Enter a username!';
	}
	
	if(strlen($register_username) > 20)
	{
		$error = 'Username is too long!';
	}
	
	if(!$register_password)
	{
		$error = 'Enter a password!';
	}
	
	if($register_password != $register_password_check)
	{
		$error = 'The passwords don\'t match!';
	}
	
	if(!$error)
	{
		$mysqli = mysqli_connect($dbhost, $dbuser, $dbpass, $db);
		$register_password = md5($register_password);
		$password_hash = password_hash($register_password, PASSWORD_DEFAULT);
		$check_result = $mysqli -> query('SELECT id from users where username="'.$mysqli ->real_escape_string($register_username).'"') or die($mysqli -> error);
		if($check_result -> num_rows > 0)
		{
			$error = 'That username already exists!';
		}
		else
		{
			$result = $mysqli -> query('INSERT into users (username, password_hash) VALUES ("'.$mysqli->real_escape_string($register_username).'","'.$password_hash.'")') or die($mysqli -> error);
			
			session_start();
			setcookie('username', $register_username, time()+60*60*24*365);
			setcookie('password', $register_password, time()+60*60*24*365);
			
			echo 'Successfully registered!<br><a href="./">Back</a>';
			exit();
		}
	}
}



?>
<h3>Register</h3>
<?php

if($error)
{
	echo '<span style="color: red; font-weight: bold;">'.htmlentities($error).'</span><br><br>';
}

?>

<form method='POST'>
Username: <input type='text' name='register_username'><br>
Password: <input type='password' name='register_password'><br>
Check password: <input type='password' name='register_password_check'>
<input type='submit' name='register' value='Register!'>
</form>

<br><br>
<a href='./'>Back</a>