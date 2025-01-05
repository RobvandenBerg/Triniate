<?php
include("../include_this.php");
light_login();

mysql_pconnect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());

$my_settings_request = mysql_query("SELECT head,body,legs,weapon from position where id='$player_id'");
$my_settings_row = mysql_fetch_row($my_settings_request);
$head = $my_settings_row[0];
$body = $my_settings_row[1];
$legs = $my_settings_row[2];
$weapon = $my_settings_row[3];

if($head == 0 or !is_numeric($head))
{
	$head = 1;
}
if($body == 0 or !is_numeric($body))
{
	$body = 1;
}
if($legs == 0 or !is_numeric($legs))
{
	$legs = 1;
}

mysql_close();
?>
<html>
<head>
<meta name='viewport' content='width=320'>
<title>
Triniate - Create character
</title>
<style>
body
{
	padding: 0px;
	margin: 0px;
}
#topscreen
{
	width: 320px;
	height: 218px;
	padding: 0px;
	background-color: black;
}
#bottomscreen
{
	width: 320px;
	height: 212px;
	padding: 0px;
	background-color: black;
}

.character_part
{
position: absolute;
top: 250px;
left: 150px;
width: 60px;
height: 90px;
padding: 5px;
}
#character_head
{
border: 2px solid red;
top: 248px;
left: 149px;
background-color: white;
}
.settings
{
font-size: 12px;
position: absolute;
top: 228px;
left: 5px;
border: 1px solid blue;
background-color: aqua;
width: 100px;
height: 180px;
}
.finished
{
position: absolute;
top: 380px;
left: 115px;
width: 183px;
padding: 5px;
height: 30px;
font-size: 14px;
border: 1px solid orange;
background-color: yellow;
}

.page_header
{
position: relative;
top: 90px;
text-align: center;
font-size: 30px;
color: white;
font-weight: bold;
}
</style>
<script>

var head = new Array(0,[1,'../bodyparts/1/walk_down/head_2.gif'],[2,'../bodyparts/2/walk_down/head_2.gif'],[3,'../bodyparts/3/walk_down/head_2.gif'],[4,'../bodyparts/4/walk_down/head_4.gif']);
var body = new Array(0,[1,'../bodyparts/1/walk_down/body_2.gif'],[2,'../bodyparts/2/walk_down/body_2.gif'],[5,'../bodyparts/5/walk_down/body_2.gif'],[6,'../bodyparts/6/walk_down/body_4.gif'],[7,'../bodyparts/7/walk_down/body_4.gif']);
var legs = new Array(0,[1,'../bodyparts/1/walk_down/legs_2.gif'],[2,'../bodyparts/2/walk_down/legs_2.gif'],[5,'../bodyparts/5/walk_down/legs_2.gif'],[6,'../bodyparts/6/walk_down/legs_4.gif'],[8,'../bodyparts/8/walk_down/legs_4.gif']);
var npc = ["1","2","3","4","5"];
<?php
/*
var current_body = <?php $body;?>;
var current_head = <?php $head;?>;
var current_legs = <?php $legs;?>;
*/
?>

var current_head = 0;
var current_body = 0;
var current_legs = 0;
var current_npc = 1;

function prevChar(){
	max = (npc.length - 1);
	if((current_npc - 1) < 0){
		current_npc = max;
	} else {
		current_npc -= 1;
	}
	document.getElementById("char").src = "../npcs/sprites/"+npc[current_npc]+"/stand_down.gif";
	document.getElementById("sendvalue").value = npc[current_npc]+";1;1;1";
}
function nextChar(){
	max = npc.length
	if((npc.length - 1)< current_npc + 1){
		current_npc = 0;
	} else {
		current_npc += 1;
	}
	document.getElementById("char").src = "../npcs/sprites/"+npc[current_npc]+"/stand_down.gif";
	document.getElementById("sendvalue").value = npc[current_npc]+";1;1;1";
}
var weapon = new Array([0,0],[1,1],[2,2]);
var current_weapon = 0;
function finish()
{
// document.getElementById('sendvalue').value = head[current_head][0] + ';' + body[current_body][0] + ';' + legs[current_legs][0] + ';' + weapon[current_weapon][0];
setTimeout("document.forms[0].submit();",500);
}
</script>
</head>
<body>
<div id='topscreen'>
<div class='page_header'>Create character</div>
</div>
<div id='Bottomscreen'>
<?php
// echo "Player ID: $player_id.<br>\n";
// echo "Head: $head. Body: $body. Legs: $legs.";
?>
<br>
<img class='character_part' id='char' src='../npcs/sprites/1/stand_down.gif'>


<div class='settings'>
<input type='button' value='&lt;' oNclick="prevChar();"> NPC <input type='button' value='&gt;' oNclick="nextChar();"><br>
</div>
<div class='finished'>
<input type='button' value='Finished' oNclick='finish();'>
<form action='render/create_sprite.php' method='post'>
<input type='hidden' id='sendvalue' name='sendvalue'>
</form>
</div>

</div>
</body>
<script><?php // echo "switch_bodypart('head','next'); switch_bodypart('body','next'); switch_bodypart('legs','next');";?>setInterval("document.body.scrollTop=218;",1);</script>
</html>