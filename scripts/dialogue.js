var dialogue_feedback = false;
var dialogue_feedback_array = new Array(0,0);

function converttodialogue(input)
{

	var inputsplit = input.split('*you*');
	for(var a = 0; a < (inputsplit.length - 1); a++)
	{
		input = input.replace("*you*", player_name);
	}

	var spl1 = input.split('@');
	var gosayarray = new Array;
	var gochararray = new Array;
	for(var c in spl1)
	{
		var csp = spl1[c];
		var spl2 = csp.split('#');
		var sayname = spl2[0];
		var saycolor = spl2[1];
		var saymsg = spl2[2];
		gosayarray[c] = saymsg;
		gochararray[c] = new Array(sayname,saycolor);
	}
	start_dialogue(0,gosayarray,gochararray);
}

var talkspeed = 50;




var next_dialogue = 0;
var said = 0;
var sayable = true;
var dialogue_2 = new Array('Congratulations! You made it to the portal!','Thank you for playing the demo!');
var chararray_2 = new Array(1,1);
var dialogue_1 = new Array('Hello there!','This is a demo','Use the arrow keys and the A button to move and jump');
var chararray_1 = new Array(0,1,0);
var characterarrays = new Array(chararray_1,chararray_2);
var dialogues = new Array(dialogue_1,dialogue_2);
// var sayarray = dialogues[0];
var sayarray;
var characterarray;

var characters = new Array(new Array('Mr. Wtf','green'),new Array('Mr. Wtf','blue'));

var nextmsg = '<span class="nextmsg" oNclick="show_dialogue();">(A) Next</span>';
var newdialogue = '<span class="nextmsg" oNclick="start_dialogue(next_dialogue);">(A) Click</span>';
function start_dialogue(dialogue_num,v1,v2)
{
	if(in_dialogue == false)
	{
		moveable = false;
		in_dialogue = true;
		sayable = true;
		said = 0;
		
		characters = v2;
		sayarray = v1;
		document.getElementById('dialogue_box').style.display = '';
		loops = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		loops_passed = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		inloops_passed = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		inloop = 1;
	}
	show_dialogue();
}

var loops = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
var loops_passed = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
var inloops_passed = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
var inloop = 1;

var in_dialogue = false;

var in_trade = false;

var go_choose = false;
var do_choose = false;


var set_after_completing_dialogue = 0;
var save_for_after_dialogue = 0;


function convert_current_message(input)
{
	go_choose = false;
	var output = '';
	var inputsplit = input.split(':multipilechoice:');
var questcheck = input.split('/quest ');
var checkqueststatus = input.split('/queststatus ');
var npcactioncheck = input.split('/npcaction ');
	if(inputsplit.length > 1)
	{
		// alert('dialogue');
		go_choose = true;
		current_choice = 1;
		for(var s in inputsplit)
		{
			if(s == 0)
			{
				output += inputsplit[s] + '<br>';
			}
			if(s != 0)
			{
				if(s == 1)
				{
					output += "<span id='choice_" + s + "' class='dialogue_multipile_choice' style='color: blue;'><img src='images/icons/selection_arrow.png' class='selection_arrow' id='choice_icon_" + s + "' style='visibility: visible;'>" + inputsplit[s] + "</span>";
				}
				else
				{
					output += "<span id='choice_" + s + "' class='dialogue_multipile_choice' style='color: black;'><img src='images/icons/selection_arrow.png' class='selection_arrow' id='choice_icon_" + s + "' style='display: none;'>" + inputsplit[s] + "</span>";
				}
			}
		}
	}
	else if(checkqueststatus.length > 1)
	{
		if(checkskip() == false)
		{

			handlequestid = checkqueststatus[1];
			// alert('handlequestid: '+handlequestid);
			questrequest.open("GET","quests/index.php?quest_status="+handlequestid,false);
			questrequest.send();

			current_choice = questrequest.responseText;
//alert('response test: '+current_choice);

			loops[(get_loop() + 1)] = current_choice;
			enter_next = false;

			loops_passed[get_loop()] = 1;
			inloops_passed[get_loop()] = 0;

			output = ':skip:';
		}
	}
	else if(questcheck.length > 1)
	{

		if(checkskip() == false)
		{
			// var handlequest;
			var questsplit = questcheck[1].split(';');
			handlequestid = questsplit[0];
			handlequestaction = questsplit[1];
			output = '/quest';
			questrequest.open("GET","quests/index.php?"+handlequestaction+"="+handlequestid,false);
			questrequest.send();

			var qressplit = questrequest.responseText.split(';');
			if((qressplit[0] == 'accept' || qressplit[0] == 'accomplish') && qressplit[2])
			{
				set_after_completing_dialogue = qressplit[2];
			}
			// alert(questrequest.responseText + ' - ' + qressplit[1]);
			output = qressplit[1];
		}

// set_after_completing_dialogue
	}
	else if(npcactioncheck.length > 1)
	{
		if(checkskip() == false)
		{
			// var handlequest;
			var actionid = npcactioncheck[1];
			var doarray = npc_action_arrays[actionid];
			if(doarray)
			{
				eval_after_dialogue = "start_npc_action(npc_action_arrays["+actionid+"]);";
			}
			output = ':skip:';
		}

// set_after_completing_dialogue
	}
	else if(input == '/shop' && checkskip() == false)
	{
		eval_after_dialogue = "open_iframe('shop/','shopbox');";
		output = ':skip:';
	}
	else if(input == '/sell' && checkskip() == false)
	{
		eval_after_dialogue = "open_iframe('shop/sell.php','shopbox');";
		output = ':skip:';
	}
	else if(input == '/bank' && checkskip() == false)
	{
		eval_after_dialogue = "open_iframe('bank/','shopbox');";
		output = ':skip:';
	}
	else
	{
		output = input;
	}
output = output.replace("Wanna", 'Want to');
//alert('a');
//alert('a: '+output);
	return output;
}


var current_choice = 0;
var enter_loop = 0;
var enter_next = false;

function multipile_choice(pn)
{
	var old_choice = current_choice;
	if(pn == 'next')
	{
		var new_choice = old_choice + 1;
	}
	else if(pn == 'prev')
	{
		var new_choice = old_choice - 1;
	}
	else if(pn == 'enter')
	{

		loops[(get_loop() + 1)] = current_choice;
		enter_next = false;
		loops_passed[get_loop()] = 1;
		inloops_passed[get_loop()] = 0;

	}

	if(pn != 'enter')
	{
		// alert('choice_'+new_choice + ' is de nieuwe keuze en ' + 'choice_'+old_choice+' is de oude');

		if(check_existance('choice_'+new_choice) == true && check_existance('choice_'+old_choice) == true)
		{
			document.getElementById('choice_icon_'+old_choice).style.display = 'none';
			document.getElementById('choice_'+old_choice).style.color = 'black';
			document.getElementById('choice_'+new_choice).style.color = 'blue';
			document.getElementById('choice_icon_'+new_choice).style.display = '';
		}
		current_choice = new_choice;

		// check_existance(cplayer)
	}


}

function get_loop()
{
	for(onloop=10;onloop>=0;onloop=onloop-1)
	{
		if(loops[onloop] != 0)
		{
			return onloop;
		}
	}
	return 0;
}


function checkskip()
{
	if(get_loop() != (inloop - 1) || loops[get_loop()] != inloops_passed[(inloop - 1)])
	{
		// alert('realskip. getloop is ' + get_loop() + ' en (inloop - 1) is ' + (inloop - 1) + '. '+loops[get_loop()] + ' is niet ' + inloops_passed[(inloop - 1)]);
		return true;
	}
	return false;
}


var looping = false;
var skipnext = false;

var handlequestid = 0;
var handlequestaction;
var eval_after_dialogue = false;

function show_dialogue()
{
	if(sayable == true)
	{
		document.getElementById('dialogue_character_right').style.display = 'none';
		document.getElementById('dialogue_character_left').style.display = 'none';

		sayable = false;
		if(said < sayarray.length)
		{
			do_choose = false;
			document.getElementById('dialogue').innerHTML = '';
			var char = characters[said];
			var saystring = sayarray[said];
			saystring = convert_current_message(saystring);
			if(saystring == '/quest')
			{
				alert("Call to quest id: "+handlequestid+". Action: "+handlequestaction+".");
			}
			else if(saystring == '}')
			{
				inloop--;
				if(loops[inloop] == inloops_passed[inloop] && loops_passed[inloop] != 0 && get_loop() == inloop)
				{

					var got_loop = get_loop();
					loops[got_loop] = 0;
					loops_passed[got_loop] = 0;
				}
				else
				{
					
				}

				inloops_passed[(inloop+1)] = 0;
				
				said++;
				sayable = true;
				show_dialogue();
				return;
			}

			if(saystring == '{')
			{
				inloop++;
				
				var inloopmin1 = inloop - 1;
				inloops_passed[inloopmin1]++;

				
				if(loops[inloopmin1] == inloops_passed[inloopmin1] && loops_passed[inloopmin1] != 0 && get_loop() == inloopmin1)
				{
					// alert('{.Enter the next loop. Details: loops[inloopmin1] (' + loops[inloopmin1] + ') is inloops_passed[inloopmin1] (' + inloops_passed[inloopmin1] + '). get_loop() (' + get_loop() + ') is inloopmin1 (' + inloopmin1 + ').');
				}
				else
				{
					// alert('{.DO NOT ENTER NEXT LOOP. Details: loops[inloopmin1] (' + loops[inloopmin1] + ') is inloops_passed[inloopmin1] (' + inloops_passed[inloopmin1] + '). get_loop() (' + get_loop() + ') is inloopmin1 (' + inloopmin1 + ').');
				}

				said++;
				sayable = true;
				show_dialogue();
				return;

			}
			if(saystring == ':skip:')
			{
				said++;
				sayable = true;
				show_dialogue();
				return;
			}

			
			var inloopmin1 = inloop - 1;
				


			if(get_loop() != (inloop - 1) || loops[get_loop()] != inloops_passed[(inloop - 1)])
			{
				// alert('realskip. getloop is ' + get_loop() + ' en (inloop - 1) is ' + (inloop - 1) + '. '+loops[get_loop()] + ' is niet ' + inloops_passed[(inloop - 1)]);
				said++;
				sayable = true;
				show_dialogue();
				return;
			}


			document.getElementById('dialogue_character_right').style.display = 'none';
			document.getElementById('dialogue_character_left').style.display = 'none';

			var char0split = char[0].split('|');
			if(char0split.length == 3)
			{
				var chardirec = char0split[1];
				var charimage = char0split[2];
				if(chardirec == 'r')
				{
					document.getElementById('dialogue_character_right').style.display = '';
					document.getElementById('dialogue_character_right').style.backgroundImage = "url(dialog_chars/"+charimage+")";
				}
				else
				{
					document.getElementById('dialogue_character_left').style.display = '';
					document.getElementById('dialogue_character_left').style.backgroundImage = "url(dialog_chars/"+charimage+")";
				}
				char[0] = char0split[0];
			}

			document.getElementById('dialogue_character').innerHTML = '<font color="' + char[1]+ '">' + char[0] + '</font>: ';

			var saysplit = saystring.split('');
			var saytime = talkspeed;
			var sayend = false;
			for(var o in saysplit)
			{
				var msg = saysplit[o];
				// ~ 
				if(msg == '~' || sayend == true)
				{
					sayend = true;
				}
				else
				{
					if(msg == '"')
					{
						msg = '&quot;';
					}
					setTimeout("document.getElementById('dialogue').innerHTML = document.getElementById('dialogue').innerHTML + \""+msg+"\";",saytime);
					saytime = saytime + talkspeed;
					if(o == (saysplit.length - 1))
					{
						setTimeout("document.getElementById('dialogue').innerHTML = document.getElementById('dialogue').innerHTML + '"+nextmsg+"';",saytime);
					}
				}
			}
			if(sayend == true)
			{
				setTimeout("document.getElementById('dialogue').innerHTML = \"" + saystring.replace("~", '') + "\";",saytime);
				
				// maak said gelijk aan sayarray.length om in de said >= sayarray.length te komen
			}
			
			setTimeout("sayable = true;",saytime);
			said++;

			if(go_choose == true)
			{
				setTimeout("do_choose = true;",saytime);
				// setTimeout("alert('do choose!');",saytime);
			}
		}
		else if(said >= sayarray.length)
		{
			in_dialogue = false;
			do_choose = false;
			document.getElementById('dialogue_character').innerHTML = '';
			document.getElementById('dialogue').innerHTML = '-';
			moveable = true;
			sayable = true;
			document.getElementById('dialogue_box').style.display = 'none';
			if(eval_after_dialogue)
			{
				// Shop openen, bijvoorbeeld
				eval(eval_after_dialogue);
				eval_after_dialogue = false;
			}
			if(dialogue_feedback)
			{
				dialogue_feedback = false;
				setTimeout("npc_action("+dialogue_feedback_array+");",10);
			}
			if(set_after_completing_dialogue != 0 && save_for_after_dialogue != 0)
			{
//alert('set objects['+save_for_after_dialogue+'][4] to '+set_after_completing_dialogue);
				objects[save_for_after_dialogue][4] = set_after_completing_dialogue;
				//alert('done. objects['+save_for_after_dialogue+'][4] is now '+objects[save_for_after_dialogue][4]);
				save_for_after_dialogue = 0;
				set_after_completing_dialogue = 0;
			}
		}
	}
}