<?php
$objects = array();

$npc_name = 'Kevin';

$object = array("npc","2",$npc_name,"1","10,15","20,30","
$npc_name|l|2/normal.png#blue#Hello there! How can I help you?~:multipilechoice:I want to buy:multipilechoice:Nothing. Have a good day:multipilechoice:You suck, go home.
@NPC 1|l|2/normal.png#blue#{
@*you*|r|2/normal_inverted.png#blue#/shop
@NPC 1|l|2/normal.png#blue#}
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Bye!
@NPC 1|l|2/normal.png#blue#}
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Excuse me?! I do not want people with this kind of behaviour in my shop, leave now!
@*you*|r|1/normal_inverted.png#blue#Okay, sorry, I will...
@$npc_name|l|2/normal.png#blue#/npcaction 1
@NPC 1|l|2/normal.png#blue#}
");
$object = str_replace("\r","",$object);
$objects[count($objects)] = str_replace("\n","",$object);

$npc_name = 'Mark';

$object = array("npc","1",$npc_name,"1","100,15","20,30","
$npc_name|l|2/normal.png#blue#Hello there! How can I help you?~:multipilechoice:I want to sell:multipilechoice:Nothing. Have a good day:multipilechoice:You suck, go home.
@$npc_name|l|2/normal.png#blue#{
@*you*|r|2/normal_inverted.png#blue#/sell
@$npc_name|l|2/normal.png#blue#}
@$npc_name|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Bye!
@$npc_name|l|2/normal.png#blue#}
@$npc_name|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Okay, bye.
@$npc_name|l|2/normal.png#blue#/npcaction 0
@$npc_name|l|2/normal.png#blue#}
");
$object = str_replace("\r","",$object);
$objects[count($objects)] = str_replace("\n","",$object);

?>