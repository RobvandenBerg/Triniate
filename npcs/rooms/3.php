<?php
$objects = array();


$npc_name = 'Rob';
$object = array("npc","1",$npc_name,"4","10,15","20,30","
$npc_name|l|2/normal.png#blue#/queststatus 2
@NPC 1|l|2/normal.png#blue#{
@$npc_name|l|2/normal.png#blue#I lost stoney!
@*you*|r|1/normal_inverted.png#blue#What is that?
@$npc_name|l|2/normal.png#blue#WHO is that, you mean. Stoney is the name of the stone I lost. I was very attached to it...
@*you*|r|1/normal_inverted.png#blue#*mumbles*Forever alone...
@$npc_name|l|2/normal.png#blue#Hey! I heard that!
@*you*|r|1/normal_inverted.png#blue#Do you have any clue where it might be?
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
@*you*|r|1/normal_inverted.png#blue#You're still forever alone...
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
@*you*|r|1/normal_inverted.png#purple#What's wrong?
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
@*you*|r|1/normal_inverted.png#purple#No problem, thanks for the bone!
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#Awww...
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#}

@NPC 2|r|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#I feel so much better after those two apples! Thanks again! Now I can finally continue programming SomeLuigi's mansion!
@NPC 2|r|2/normal.png#purple#}
");


$object[6] = str_replace("\n",'',$object[6]);
$object[6] = str_replace("\r",'',$object[6]);

$objects[count($objects)] = $object;




$npc_name = 'Eduard';
$object = array("npc","3",$npc_name,"2","320,270","20,30","

$npc_name|l|2/normal.png#purple#/queststatus 3

@$npc_name|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#The worst thing ever happened to me... Hear me out
@$npc_name|l|2/normal.png#purple#I was proposing to my beautiful girlfriend near the volcano...
@$npc_name|l|2/normal.png#purple#But then a strong-looking monster stole the ring!
@*you*|r|1/normal_inverted.png#purple#What happened then?
@$npc_name|l|2/normal.png#purple#I ran for my life! Now I am safe, but I want the ring back!
@$npc_name|l|2/normal.png#purple#I am too afraid to do it myself, though... ~:multipilechoice:I will do it!:multipilechoice:That is not very manly
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#/quest 3;accept_quest
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#Hey! Let go of your toxic masculinity!
@$npc_name|l|2/normal.png#purple#Men can be scared, too.
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#}


@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#Sooo... How's getting back my wedding ring going?
@*you*|r|1/normal_inverted.png#purple#Erm... Where can I find it again?
@$npc_name|l|2/normal.png#purple#The monster should still be near the volcano.
@$npc_name|l|2/normal.png#purple#If you head north east from here, you should be able to find it no problem.
@NPC 2|l|2/normal.png#purple#}

@NPC 2|l|2/normal.png#purple#{
@*you*|r|1/normal_inverted.png#purple#I found your ring!
@$npc_name|l|2/normal.png#purple#Really?! Woah!~:multipilechoice:Here you go!:multipilechoice:I'm gonna use it to propose to your girlfriend
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#/quest 3;accomplish_quest
@$npc_name|l|2/normal.png#purple#Here, have this iron axe and iron pickaxe in return! They're good tools, but I won't be needing them anymore.
@*you*|r|1/normal_inverted.png#purple#Sweet!
@*you*|r|1/normal_inverted.png#purple#Next time don't lose the ring again.
@$npc_name|l|2/normal.png#purple#Haha yeah, never again! 
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#Wow. You think you're cool now, huh? Is that it?
@$npc_name|l|2/normal.png#purple#Go for it. She won't even consider it, ugly face.
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#}

@NPC 2|r|2/normal.png#purple#{
@$npc_name|l|2/normal.png#purple#I'm thinking of a new original place to propose.
@$npc_name|l|2/normal.png#purple#Somewhere safe, unlike that volcano...
@NPC 2|r|2/normal.png#purple#}
");


$object[6] = str_replace("\n",'',$object[6]);
$object[6] = str_replace("\r",'',$object[6]);

$objects[count($objects)] = $object;
?>