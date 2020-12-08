<?php
$objects = array();

$npc_name = 'Jason';

$object = array("npc","1",$npc_name,"1","100,15","20,30","
$npc_name|l|2/normal.png#blue#Hello there! How can I help you?~:multipilechoice:I want to access my bank account:multipilechoice:Nothing. Have a good day
@$npc_name|l|2/normal.png#blue#{
@*you*|r|2/normal_inverted.png#blue#/bank
@$npc_name|l|2/normal.png#blue#}
@$npc_name|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Bye!
@$npc_name|l|2/normal.png#blue#}
");
$object = str_replace("\r","",$object);
$objects[count($objects)] = str_replace("\n","",$object);

?>