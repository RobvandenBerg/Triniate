<?php
// INSERT into villains (room,pos_top,pos_left,sprite,player_name,health,walkzone,followzone,walkcoord,agressive,level,attack,defense,max_hp,drop_item,quantity) VALUES ('3','123','181','sprite_down','Goblin (5)','66','79,82,206,123','11,36,290,163','181,123','','5','61','61','66','1','1')


$needed_level = 5;
$items_needed = array(4=>1);
$items_get = array(5=>3,3=>100);
$level_reject_message = 'Sorry, but you need to be at least level 5 to accept this quest';
$accomplish_message = 'Yeeess! My stone! I\'m so thankful! Here, have these three small potions and 100 coins!';
$accept_quest_message = 'I hope you\'ll find my stone...';
$accept_quest_queries = array("INSERT into villains (room,pos_top,pos_left,sprite,player_name,health,walkzone,followzone,walkcoord,agressive,level,attack,defense,max_hp,drop_item,quantity,visible) VALUES ('3','123','206','sprite_down','Goblin (5)','66','79,82,206,123','11,36,290,163','181,123','0','5','61','61','66','4','1','$player_id')");
?>