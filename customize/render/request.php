<?php
include("../../include_this.php");
full_login();
if(!isset($logged_in))
{
	die("Please log in.");
}

include("../functions/index.php");

$sprite_id = $_GET['id'];

if(empty($sprite_id) or !check_sprite_dir($user_id,$sprite_id))
{
	die("No valid sprite with id $id.");
}

$stage = get_recommended_action($user_id,$sprite_id);

$sprite = $_GET['sprite'];

$before_render_walk_up = array('walk_up/head_1.png','walk_up/head_2.png','walk_up/head_3.png','walk_up/head_4.png','walk_up/body_1.png','walk_up/body_2.png','walk_up/body_3.png','walk_up/body_4.png','walk_up/legs_1.png','walk_up/legs_2.png','walk_up/legs_3.png','walk_up/legs_4.png');

$before_render_walk_down = array('walk_down/head_1.png','walk_down/head_2.png','walk_down/head_3.png','walk_down/head_4.png','walk_down/body_1.png','walk_down/body_2.png','walk_down/body_3.png','walk_down/body_4.png','walk_down/legs_1.png','walk_down/legs_2.png','walk_down/legs_3.png','walk_down/legs_4.png');

$before_render_walk_left = array('walk_left/head_1.png','walk_left/head_2.png','walk_left/head_3.png','walk_left/head_4.png','walk_left/body_1.png','walk_left/body_2.png','walk_left/body_3.png','walk_left/body_4.png','walk_left/legs_1.png','walk_left/legs_2.png','walk_left/legs_3.png','walk_left/legs_4.png');

$before_render_walk_right = array('walk_right/head_1.png','walk_right/head_2.png','walk_right/head_3.png','walk_right/head_4.png','walk_right/body_1.png','walk_right/body_2.png','walk_right/body_3.png','walk_right/body_4.png','walk_right/legs_1.png','walk_right/legs_2.png','walk_right/legs_3.png','walk_right/legs_4.png');

$before_render_attack_up = array('attack_up/head_1.png','attack_up/head_2.png','attack_up/body_1.png','attack_up/body_2.png','attack_up/legs_1.png','attack_up/legs_2.png');

$before_render_attack_down = array('attack_down/head_1.png','attack_down/head_2.png','attack_down/body_1.png','attack_down/body_2.png','attack_down/legs_1.png','attack_down/legs_2.png');

$before_render_attack_left = array('attack_left/head_1.png','attack_left/head_2.png','attack_left/body_1.png','attack_left/body_2.png','attack_left/legs_1.png','attack_left/legs_2.png');

$before_render_attack_right = array('attack_right/head_1.png','attack_right/head_2.png','attack_right/body_1.png','attack_right/body_2.png','attack_right/legs_1.png','attack_right/legs_2.png');

$before_render_stand_up = array('walk_up/head_2.png','walk_up/body_2.png','walk_up/legs_2.png');

$before_render_stand_down = array('walk_down/head_2.png','walk_down/body_2.png','walk_down/legs_2.png');

$before_render_stand_left = array('walk_left/head_2.png','walk_left/body_2.png','walk_left/legs_2.png');

$before_render_stand_right = array('walk_right/head_2.png','walk_right/body_2.png','walk_right/legs_2.png');

$before_render_hurt_up = array('stand_up.gif');

$before_render_hurt_down = array('stand_down.gif');

$before_render_hurt_left = array('stand_left.gif');

$before_render_hurt_right = array('stand_right.gif');

$needed_array = array('walk_up'=>$before_render_walk_up,'walk_down'=>$before_render_walk_down,'walk_left'=>$before_render_walk_left,'walk_right'=>$before_render_walk_right,'attack_up'=>$before_render_attack_up,'attack_down'=>$before_render_attack_down,'attack_left'=>$before_render_attack_left,'attack_right'=>$before_render_attack_right,'stand_up'=>$before_render_stand_up,'stand_down'=>$before_render_stand_down,'stand_left'=>$before_render_stand_left,'stand_right'=>$before_render_stand_right,'hurt_up'=>$before_render_hurt_up,'hurt_down'=>$before_render_hurt_down,'hurt_left'=>$before_render_hurt_left,'hurt_right'=>$before_render_hurt_right);


if(!$needed_array[$sprite])
{
die("Invalid Sprite");
}

$get_needed_array = $needed_array[$sprite];

$have_all = true;

foreach($get_needed_array as $file)
{
	if(!file_exists(get_main_dir($user_id) . "/$sprite_id/$file"))
	{
		$have_all = false;
	}
}

if($have_all == false)
{
	die("<script>alert('You do not have all the needed sprites to render this sprite'); window.location='../sprite.php?id=$sprite_id';</script>");
}

$splitsprite = explode("_",$sprite);
if($splitsprite[0] == 'hurt')
{
	$use_sprite = "walk_" . $splitsprite[1];
	include("render_red_version.php");
}
else
{
	include("render.php");
}
echo "<script>alert('Sprite succesfully rendered!'); window.location='../sprite.php?id=$sprite_id';</script>";
exit();

?>