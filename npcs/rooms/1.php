<?php

$npc_name = 'Alina';
$object = array("npc","4",$npc_name,"5","320,570","20,30","

$npc_name|l|1/normal.png#purple#/queststatus 3

@$npc_name|l|3/normal.png#pink#{
@$npc_name|l|3/normal.png#pink#I am so happy!
@*you*|r|1/normal_inverted.png#purple#Uhhh... Hi?
@$npc_name|l|3/normal.png#pink#Haha I just blurted that out, didn't I?
@*you*|r|1/normal_inverted.png#purple#You.... sure did
@$npc_name|l|3/normal.png#pink#Allow me to explain... ~:multipilechoice:Please:multipilechoice:Sorry, I've got better things to do
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|3/normal.png#pink#So I have a boyfriend, but I've been feeling a bit unsure about my feelings lately
@$npc_name|l|3/normal.png#pink#But he took me on a date to the volcano, and out of nowhere, he proposed to me!
@$npc_name|l|3/normal.png#pink#I didn't know what to do, I was in such a pickle.
@$npc_name|l|3/normal.png#pink#But then, a monster snatched the ring right out of his hand!
@$npc_name|l|3/normal.png#pink#What a relief! Now I don't have to worry about what my answer will be!
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#{
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#}


@$npc_name|l|3/normal.png#pink#{
@$npc_name|l|3/normal.png#pink#I am so happy!
@*you*|r|1/normal_inverted.png#purple#Uhhh... Hi?
@$npc_name|l|3/normal.png#pink#Haha I just blurted that out, didn't I?
@*you*|r|1/normal_inverted.png#purple#You.... sure did
@$npc_name|l|3/normal.png#pink#Allow me to explain... ~:multipilechoice:Please:multipilechoice:Sorry, I've gotta return a ring to some guy
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|3/normal.png#pink#So I have a boyfriend, but I've been feeling a bit unsure about my feelings lately
@$npc_name|l|3/normal.png#pink#But he took me on a date to the volcano, and out of nowhere, he proposed to me!
@$npc_name|l|3/normal.png#pink#I didn't know what to do, I was in such a pickle.
@$npc_name|l|3/normal.png#pink#But then, a monster snatched the ring right out of his hand!
@$npc_name|l|3/normal.png#pink#What a relief! Now I don't have to worry about what my answer will be!
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|3/normal.png#pink#Wait, WHAT? That's probably my boyfriend's ring that he used to propose to me!
@$npc_name|l|3/normal.png#pink#Don't do it! First of all, it's dangerous... Secondly, I'm better off as is!
@*you*|r|1/normal_inverted.png#purple#Sorry, but I am just simply doing what must be done.
@$npc_name|l|3/normal.png#pink#Noooooooo!
@NPC 2|l|2/normal.png#purple#}
@NPC 2|l|2/normal.png#purple#}

@NPC 2|l|2/normal.png#purple#{
@$npc_name|l|3/normal.png#pink#Uhh... Did I just see you kill that monster and take that ring?
@$npc_name|l|3/normal.png#pink#That's... The ring my boyfriend tried to propose to me with.
@$npc_name|l|3/normal.png#pink#I sure hope you're not planning on giving it back to him, are you?
@*you*|r|1/normal_inverted.png#purple#Look, I'm a simple man. People have quests for me, and I do them.
@$npc_name|l|3/normal.png#pink#Wow, that's so bold! Unlike my boyfriend. Are you si-
@$npc_name|l|3/normal.png#pink#Wait, I shouldn't be asking such things. I have a partner!
@NPC 2|l|2/normal.png#purple#}

@NPC 2|r|2/normal.png#purple#{
@$npc_name|l|3/normal.png#pink#So you gave the ring back to my boyfriend... What am I gonna do?!
@$npc_name|l|3/normal.png#pink#I better start thinking quickly about whether I am ready to get married.
@NPC 2|r|2/normal.png#purple#}
");


$object[6] = str_replace("\n",'',$object[6]);
$object[6] = str_replace("\r",'',$object[6]);

$objects[count($objects)] = $object;

?>