<?php
include("../../include_this.php");
full_login();
if(!isset($logged_in))
{
	die("Please log in.");
}

// echo "member id: $user_id.";

include("../functions/index.php");

$sprite_id = $_GET['id'];

if(empty($sprite_id) or !check_sprite_dir($user_id,$sprite_id))
{
	die("No valid sprite with id $id.");
}

$stage = get_recommended_action($user_id,$sprite_id);



$sprite = "walk_down";
include("render.php");
?>