<?php
function obtain_mg($player_id)
{
	$convert_player_id = md5($player_id);
	// $splode = explode('',$convert_player_id);
	$STR_LEN = strlen($convert_player_id);
	$mg = $convert_player_id[6] .$convert_player_id[21] . $convert_player_id[5] . $convert_player_id[12] . $convert_player_id[29] . $convert_player_id[2] . $convert_player_id[13];
	// echo "<script>alert('$convert_player_id - $mg');</script>";
	return $mg;
}
?>