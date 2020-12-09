<?php

$needed_level = 10;
$items_needed = array(28=>1);
$items_get = array(24=>1,19=>1);
$level_reject_message = 'Sorry, but I don\'t want to ask anyone below level 10 to do this. This monster is dangerous!';
$accomplish_message = 'You actually did it! Thanks so much! My wife-to-be and I are eternally grateful!';
$accept_quest_message = 'Thank you! I hope you find it, my marriage depends on it!';
$accept_quest_queries = array("INSERT into villains (room,pos_top,pos_left,sprite,player_name,health,walkzone,followzone,walkcoord,agressive,level,attack,defense,max_hp,drop_item,quantity,visible) VALUES ('1','123','206','sprite_down','Goblin (15)','150','79,182,306,123','11,136,390,163','181,223','100','15','114','124','150','28','1','$player_id')");

?>