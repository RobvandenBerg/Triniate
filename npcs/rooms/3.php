<?php
$objects = array();


$npc_name = 'Rob';
$object = array("npc","1",$npc_name,"4","10,15","20,30","
$npc_name|l|2/normal.png#blue#/queststatus 2
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#I lost stoney!
@*you*|r|2/normal_inverted.png#blue#What is that?
@$npc_name|l|2/normal.png#blue#WHO is that, you mean. Stoney is the name of the stone I lost. I was very attached to it...
@*you*|r|2/normal_inverted.png#blue#*mumbles*Forever alone...
@$npc_name|l|2/normal.png#blue#Hey! I heard that!
@*you*|r|2/normal_inverted.png#blue#Do you have any clue where it might be?
@$npc_name|l|2/normal.png#blue#Yes, I saw a green monster take it. He must be somewhere around here.
@$npc_name|l|2/normal.png#blue#Would you mind helping me? Please? I'll give you a small potion and 100 coins!~:multipilechoice:Okay:multipilechoice:No, I'm sorry:multipilechoice:LOL, no
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#/quest 2;accept_quest
@NPC 1|l|2/normal.png#blue#}
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Awww...
@NPC 1|l|2/normal.png#blue#}
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Why are you so rude?!
@NPC 1|l|2/normal.png#blue#}
@NPC 1|l|2/normal.png#blue#}

@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Please find my stone. A green monster took it. It must be somewhere close.
@NPC 1|l|2/normal.png#blue#}

@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#OHMYGOSH! You found my stone!~:multipilechoice:Here, have it:multipilechoice:I'm keeping it
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#/quest 2;accomplish_quest
@NPC 1|l|2/normal.png#blue#}
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Whaaaaaaaat? MEANIE!
@NPC 1|l|2/normal.png#blue#}
@NPC 1|l|2/normal.png#blue#}

@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#Thank you so much for bringing back stoney!
@*you*|r|2/normal_inverted.png#blue#You're still forever alone...
@$npc_name|l|2/normal.png#blue#Stop being so mean...
@NPC 1|l|2/normal.png#blue#}
");
$object = str_replace("\r","",$object);
$objects[count($objects)] = str_replace("\n","",$object);



$npc_name = 'Oliver';
$object = array("npc","2",$npc_name,"3","90,20","20,30","

$npc_name|l|2/normal.png#purple#/queststatus 1

@$npc_name|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#Help me...
@*you*|r|2/normal_inverted.png#purple#What's wrong?
@$npc_name|l|2/normal.png#purple#I haven't had anything to eat for a whole day...
@$npc_name|l|2/normal.png#purple#I'd give you this bone I found for two apples. Is that a deal?~:multipilechoice:Sure thing:multipilechoice:Get your own food
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#/quest 1;accept_quest
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#Urgh... Too bad...
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#}


@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#So hungry... Please bring me two appples... I'll give you a bone...
@NPC 2|l|2/normal.png#purple#}

@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#So... Hungry...
@$npc_name|l|2/normal.png#purple#I see you have the two apples I asked for... Can I have them?~:multipilechoice:Yes:multipilechoice:No
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#/quest 1;accomplish_quest
@*you*|r|2/normal_inverted.png#purple#No problem, thanks for the bone!
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#Awww...
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#}

@NPC 2|r|2/normal.png#purple#{
@$npc_name|r|2/normal.png#purple#I feel so much better after those two apples! Thanks again! Now I can finally continue programming SomeLuigi's mansion!
@NPC 2|r|2/normal.png#purple#}
");


$object[6] = str_replace("\n",'',$object[6]);
$object[6] = str_replace("\r",'',$object[6]);

$objects[count($objects)] = $object;
?>