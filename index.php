<?php

include('include_this.php');

include_once('functions/urlsettings.php');

full_login();
//var_dump($_SERVER);

if(!isset($logged_in))
{
?>
<html>
<head>
<meta name='viewport' content='width=device-width'>
<title>Project Triniate</title>
<style>
body{padding: 0px; margin: 0px;}
#topscreen,#bottomscreen{width: 310px; background-color: black; color: white; padding: 5px;}
#bottomscreen{
font-size: 10px;
height: 202px;
background-image: url(<?php echo $triniate_homepage;?>/bottomscreen.png);
}
#topscreen{
height: 208px;
font-size: 16px;
text-align: center;
}
.titleimg{
position: relative;
top: 80px;
text-align: center;
}

@font-face {
font-family: trinigan;
src: url('<?php echo $triniate_homepage;?>/Trinigan.ttf');
}

.paper{
position: relative;
top: 0px;
left: 45px;
color: black;
width: 220px;
height: 160px;
font-size: 12px;
}

.title{
text-align: center;
font-size: 14px;
font-weight: bold;
}

.welcome_user
{
position: absolute;
top: 200px;
left: 0px;
width: 300px;
text-align: right;
padding-right: 5px;
font-size: 12px;
}
div.back
{
position: relative;
float: left;
top: 0px;
width: 79px;
background-color: gray;
border-right: 1px solid blue;
border-bottom: 1px solid blue;
left: -5px;
top: -5px;
text-align: center;
font-size: 14px;
height: 18px;
opacity: 0.8;
}
a.backlink
{
color: white;
text-decoration: none;
}
a.backlink:visited
{
color: white;
text-decoration: none;
}
</style>
<script>
/*
if(window.top.location != '<?php echo $triniate_playpage;?>/')
{
	window.top.location = '<?php echo $triniate_playpage;?>/';
}*/
window.onload = function(){document.body.scrollTop = 218;}
</script>
<?php
if(file_exists('../analytics/index.php'))
{
	include('../analytics/index.php');
}
?>
</head>
<body>
<div id='topscreen'><img class='titleimg' src='<?php echo $triniate_homepage;?>/project_triniate_title.png'></div>
<div id='bottomscreen'>
<div class='back'><a href='<?php echo $triniate_homepage;?>' class='backlink'>Back</a></div><br><br>
<div class='paper'>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
<?php
if(isset($_POST['log_in']))
{
	echo '<span style="color: red;">Invalid username/password</span><br>';
}
?>
Username: <input type="text" name="username" maxlength=15 width="100"><br>
Password: <input type="password" name="password" width="100"><br>
Remember me: <input type="checkbox" name="remember_me" value="yes"><br>
<input type="submit" name="log_in" value="log in"><br>
<a href='register.php'>I don't have an account.</a>
</div>
</div>
</body>
</html>
<?php
exit();
}


?>
<html>
<head>
<meta name='viewport' content='width=device-width'>
<title>Project Triniate</title>
<style>
body{padding: 0px; margin: 0px;}
#topscreen,#bottomscreen{width: 310px; background-color: black; color: white; padding: 5px;}
#bottomscreen{
font-size: 12px;
min-height: 202px;
}
#topscreen{
height: 208px;
font-size: 16px;
text-align: center;
}
.titleimg{
position: relative;
top: 80px;
text-align: center;
}

@font-face {
font-family: trinigan;
src: url('<?php echo $triniate_homepage;?>/Trinigan.ttf');
}

.title{
text-align: center;
font-size: 14px;
font-weight: bold;
}

.welcome_user
{
position: absolute;
top: 200px;
left: 0px;
width: 300px;
text-align: right;
padding-right: 5px;
font-size: 12px;
}
.logout
{
position: relative;
float: right;
top: 0px;
width: 79px;
background-color: gray;
border-left: 1px solid blue;
border-bottom: 1px solid blue;
left: 5px;
top: -5px;
text-align: center;
font-size: 14px;
height: 18px;
opacity: 0.8;
color: white;
font-size: 13px;
font-family: Trinigan;
cursor: pointer;
}
a.logoutlink
{
color: white;
text-decoration: none;
}
a.logoutlink:visited
{
color: white;
text-decoration: none;
}
div.back
{
position: relative;
float: left;
top: 0px;
width: 79px;
background-color: gray;
border-right: 1px solid blue;
border-bottom: 1px solid blue;
left: -5px;
top: -5px;
text-align: center;
font-size: 14px;
height: 18px;
opacity: 0.8;
}
a.backlink
{
color: white;
text-decoration: none;
}
a.backlink:visited
{
color: white;
text-decoration: none;
}
</style>
<script>
window.onload = function(){document.body.scrollTop = 218;}
</script>
</head>
<body>
<div id='topscreen'><img class='titleimg' src='<?php echo $triniate_homepage;?>/project_triniate_title.png'><div class='welcome_user'>Welcome, <?php echo htmlentities($username);?></div></div>
<div id='bottomscreen'>
<div class='back'><a href='<?php echo $triniate_homepage;?>' class='backlink'>Back</a></div><form method='post'><input type='submit' name='logout' class='logout' value='Log out'></form><br><br>
<?php



?>
<p>Welcome to Triniate, <?php echo $username;?></p>

<p>Hint: If you are stuck in the game, hit "Chat", and type "/stuck"</p>
<p><input type='button' value='Start playing Triniate' oNclick="window.location='login.php';"></p>

</div>
</body>
</html>
