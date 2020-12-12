function start_npc_action(array)
{
	var arrayid = npc_action_arrays.length
	npc_action_arrays[arrayid] = array;
	npc_action(arrayid,0);
}


function npc_action(arrayid,step)
{
	var total_in_array = npc_action_arrays[arrayid];
	var total_steps = total_in_array.length;
	if(step < total_steps)
	{
		var inarray = total_in_array[step];
		var npcid = inarray[0];
		if(npcid == 0)
		{
			var npchandle = document.getElementById('player_'+player_id);
		}
		else
		{
			var npchandle = document.getElementById('object_'+npcid);
		}

		var npcaction = inarray[1];
		switch(npcaction)
		{
			case 'walk':
			var to_leftcoord = inarray[2];
			var to_topcoord = inarray[3];

			if(inarray[5] && inarray[5] == 'relative')
			{
				to_leftcoord = to_leftcoord + parseInt(npchandle.style.left);
				to_topcoord = to_topcoord + parseInt(npchandle.style.top);
			}
			var sprite = inarray[4];
			npc_walk(arrayid,to_leftcoord,to_topcoord,npcid,step);
			break;

			case 'changesprite':
			var sprite = inarray[2];
			change_sprite(npchandle.id + '_sprite',sprite.src);
			step++;
			npc_action(arrayid,step);
			break;
			
			case 'wait':
			var wait_time = inarray[2];
			step++;
			setTimeout("npc_action("+arrayid+","+step+");",wait_time);
			break;

			case 'blockmoving':
			stop_movement();
			npc_blocks_moving = true;
			step++;
			npc_action(arrayid,step);
			break;

			case 'unblockmoving':
			npc_blocks_moving = false;
			step++;
			npc_action(arrayid,step);
			break;

			case 'warp':
			var method = inarray[2];
			var lcoord = inarray[3];
			var tcoord = inarray[4];

			if(method == 'coords')
			{
				npchandle.style.left = lcoord + 'px';
				npchandle.style.top = tcoord + 'px';
				npchandle.style.zIndex = tcoord + charheight;
			}
			else if(method == 'from_player')
			{
				var from = inarray[3];
				var plcoord = parseInt(document.getElementById('player_'+player_id).style.left);
				var ptcoord = parseInt(document.getElementById('player_'+player_id).style.top);
				
				var nlcoord = plcoord + lcoord;
				var ntcoord = ptcoord + tcoord;
				npchandle.style.left = nlcoord + 'px';
				npchandle.style.top = ntcoord + 'px';
				npchandle.style.zIndex = ntcoord + charheight;
				
			}
			step++;
			npc_action(arrayid,step);
			break;

			case 'touser':
			var to_leftcoord = parseInt(document.getElementById('player_'+player_id).style.left);
			var to_topcoord = parseInt(document.getElementById('player_'+player_id).style.top);
			var curr_leftcoord = parseInt(npchandle.style.left);
			var curr_topcoord = parseInt(npchandle.style.top);
			
			var charid = inarray[2];

			var approach_set = false;

			if(inarray[3] && (inarray[3] == 'right' || inarray[3] == 'left' || inarray[3] == 'top' || inarray[3] == 'bottom'))
			{
				approach_set = true;
				var approach = inarray[3];
				switch(approach)
				{
					case 'left':
					to_leftcoord = to_leftcoord - 15;
					break;
					
					case 'right':
					to_leftcoord = to_leftcoord + 15;
					break;

					case 'top':
					to_topcoord = to_topcoord - 15;
					break;

					case 'bottom':
					to_topcoord = to_topcoord + 15;
					break;
					
				}
			}

			var topdiff = to_topcoord - curr_topcoord;
			if(topdiff < 0)
			{
				var testtopdiff = topdiff * -1;
			}
			else
			{
				var testtopdiff = topdiff;
			}

			var leftdiff = to_leftcoord - curr_leftcoord;
			if(leftdiff < 0)
			{
				var testleftdiff = leftdiff * -1;
			}
			else
			{
				var testleftdiff = leftdiff;
			}

			if(testtopdiff > leftdiff)
			{
				if(topdiff < 0)
				{
					var sprite = sprite_move_up[charid];
					if(!approach_set)
					{
						to_topcoord = to_topcoord + 15;
					}
				}
				else
				{
					var sprite = sprite_move_down[charid];
					if(!approach_set)
					{
						to_topcoord = to_topcoord - 15;
					}
				}
			}
			else
			{
				if(leftdiff < 0)
				{
					var sprite = sprite_move_left[charid];
					if(!approach_set)
					{
						to_leftcoord = to_leftcoord + 15;
					}
				}
				else
				{
					var sprite = sprite_move_right[charid];
					if(!approach_set)
					{
						to_leftcoord = to_leftcoord - 15;
					}
				}
			}

			npc_action_arrays[arrayid][step][4] = sprite;

			npc_walk(arrayid,to_leftcoord,to_topcoord,npcid,step);
			break;

			case 'conversation':
			var conversation = inarray[2];
			dialogue_feedback = true;
			step++;
			dialogue_feedback_array = new Array(arrayid,step);
			converttodialogue(conversation);
			break;
			
			case 'redirect':
			var rurl = inarray[2];
			redirecting_page = true;
			window.location = rurl;
			break;
		}
	}

}

var npc_blocks_moving = false;

function npc_walk(arrayid,to_leftcoord,to_topcoord,npcid,step)
{
	var npchandle = document.getElementById('object_'+npcid);
	if(npcid == 0)
	{
		npchandle = document.getElementById('player_'+player_id);
	}
	var curr_leftcoord = parseInt(npchandle.style.left);
	var curr_topcoord = parseInt(npchandle.style.top);

	var sprite = npc_action_arrays[arrayid][step][4];
	// alert('sprite: ' + npc_action_arrays[arrayid][step][1]);
	change_sprite(npchandle.id + '_sprite',sprite.src);


	if(curr_leftcoord < to_leftcoord - 3)
	{
		var new_leftcoord = curr_leftcoord + 3;
	}
	else if(curr_leftcoord > to_leftcoord + 3)
	{
		var new_leftcoord = curr_leftcoord - 3;
	}
	else
	{
		var new_leftcoord = to_leftcoord;
	}
	
	if(curr_topcoord < to_topcoord - 3)
	{
		var new_topcoord = curr_topcoord + 3;
	}
	else if(curr_topcoord > to_topcoord + 3)
	{
		var new_topcoord = curr_topcoord - 3;
	}
	else
	{
		var new_topcoord = to_topcoord;
	}
	
	npchandle.style.top = new_topcoord + 'px';
	npchandle.style.left = new_leftcoord + 'px';
	npchandle.style.zIndex = new_topcoord + charheight;

	if(new_leftcoord != to_leftcoord || new_topcoord != to_topcoord)
	{
		setTimeout("npc_walk("+arrayid+","+to_leftcoord+","+to_topcoord+","+npcid+","+step+");",100);
	}
	else
	{
		step++;
		npc_action(arrayid,step);
	}
}



var redirecting_page = false;
var walkspeed = 3;
// var wallsarray = new Array('381,0,410,363','382,378,407,445','407,402,500,445');

// wallsarray[wallsarray.length] = [0,0,100,100,'use_function','open_craftbox'];
function check_movable(coordleft,coordtop)
{
	var coordbottom = coordtop + charheight;
	coordtop = coordbottom - 2;
	// var coordmiddle = coordleft + Math.floor(charwidth / 2);
	var coordright = coordleft + charwidth;
	var no_collision = true;
	for(var a in wallsarray)
	{
		var wallsplit = wallsarray[a];
		var wallleft = wallsplit[0];
		var walltop = wallsplit[1];
		var wallright = wallsplit[2];
		var wallbottom = wallsplit[3];
		if(coordleft <= wallright && coordright >= wallleft && coordtop <= wallbottom && coordbottom >= walltop)
		{
			if(wallsplit[4] && wallsplit[4] == 'teleport')
			{
				stop_movement();
				if(window.confirm('Walk to next area?'))
				{
					redirecting_page = true;
					window.location = 'switchrooms.php?newroom=' + wallsplit[5] + '&&inoption=' + wallsplit[6];
				}
			}
			else if(wallsplit[4] && wallsplit[4] == 'use_function' && wallsplit[5])
			{
				eval(wallsplit[5] + '();');
			}
			return false;
		}
	}
	if(no_collision)
	{
		for(var a in extrablocks)
		{
			var wallsplit = extrablocks[a];
			var wallleft = wallsplit[0];
			var walltop = wallsplit[1];
			var wallright = wallsplit[2];
			var wallbottom = wallsplit[3];
			if(coordleft <= wallright && coordright >= wallleft && coordtop <= wallbottom && coordbottom >= walltop)
			{
					return false;
			}
		}
	}
	return true;
}
// alert(check_movable(50,101));



var my_character = player_id;
var others_character = 1;
var villain_character = 2;



	var sprite_up = new Array();
	var sprite_down = new Array();
	var sprite_left = new Array();
	var sprite_right = new Array();
	
	var sprite_move_up = new Array();
	var sprite_move_down = new Array();
	var sprite_move_left = new Array();
	var sprite_move_right = new Array();
	
	var sprite_attack_up = new Array();
	var sprite_attack_down = new Array();
	var sprite_attack_left = new Array();
	var sprite_attack_right = new Array();
	
	var sprite_attack_up_mana = new Array();
	var sprite_attack_down_mana = new Array();
	var sprite_attack_left_mana = new Array();
	var sprite_attack_right_mana = new Array();
	
	var sprite_hurt_up = new Array();
	var sprite_hurt_down = new Array();
	var sprite_hurt_left = new Array();
	var sprite_hurt_right = new Array();

	var sprite_dead = new Array();
	

// ------------------
	for(var i in objects)
	{
		load_sprite(objects[i][3],objects[i][1]);
	}

	load_sprite(player_id);
	load_sprite(2,'Goblin');
	load_sprite(3,'Caveman');
	load_sprite(4,'Madeye');
	











function load_sprite(id,name)
{
	var beforeid = 'characters/';
	if(!name)
	{
		name = id;
		beforeid = 'customize/saved/';
	}
	else if(parseInt(name) == name)
	{
		// The sprite is from a NPC
		name = 'npc_'+name;
		beforeid = 'npcs/sprites/';
	}
	var addrand = '?r='+Math.random();
    sprite_up[name] = new Image();
    sprite_up[name].src = beforeid+id+"/stand_up.gif"+addrand;
    sprite_down[name] = new Image();
    sprite_down[name].src = beforeid+id+"/stand_down.gif"+addrand;
    sprite_left[name] = new Image();
    sprite_left[name].src = beforeid+id+"/stand_left.gif"+addrand;
    sprite_right[name] = new Image();
    sprite_right[name].src = beforeid+id+"/stand_right.gif"+addrand;
    
    	sprite_move_up[name] = new Image();
    sprite_move_up[name].src = beforeid+id+"/walk_up.gif"+addrand;
    sprite_move_down[name] = new Image();
    sprite_move_down[name].src = beforeid+id+"/walk_down.gif"+addrand;
    sprite_move_left[name] = new Image();
    sprite_move_left[name].src = beforeid+id+"/walk_left.gif"+addrand;
    sprite_move_right[name] = new Image();
    sprite_move_right[name].src = beforeid+id+"/walk_right.gif"+addrand;
    
    	sprite_attack_up[name] = new Image();
    sprite_attack_up[name].src = beforeid+id+"/attack_up.gif"+addrand;
    sprite_attack_down[name] = new Image();
    sprite_attack_down[name].src = beforeid+id+"/attack_down.gif"+addrand;
    sprite_attack_left[name] = new Image();
    sprite_attack_left[name].src = beforeid+id+"/attack_left.gif"+addrand;
    sprite_attack_right[name] = new Image();
    sprite_attack_right[name].src = beforeid+id+"/attack_right.gif"+addrand;
	
	sprite_attack_up_mana[name] = sprite_attack_up[name];
	sprite_attack_down_mana[name] = sprite_attack_down[name];
	sprite_attack_left_mana[name] = sprite_attack_left[name];
	sprite_attack_right_mana[name] = sprite_attack_right[name];
    
	sprite_hurt_up[name] = new Image();
    sprite_hurt_up[name].src = beforeid+id+"/hurt_up.gif"+addrand;
    sprite_hurt_down[name] = new Image();
    sprite_hurt_down[name].src = beforeid+id+"/hurt_down.gif"+addrand;
    sprite_hurt_left[name] = new Image();
    sprite_hurt_left[name].src = beforeid+id+"/hurt_right.gif"+addrand;
    sprite_hurt_right[name] = new Image();
    sprite_hurt_right[name].src = beforeid+id+"/hurt_left.gif"+addrand;


	sprite_dead[name] = new Image();
    sprite_dead[name].src = beforeid+id+"/dead.gif";

}


function unload_sprite(id)
{


// alert('unload sprite van id '+id+'. sprite up is nu: '+sprite_up[id].src);
	sprite_up.splice(id,1);
	sprite_down.splice(id,1);
	sprite_left.splice(id,1);
	sprite_right.splice(id,1);

	sprite_move_up.splice(id,1);
	sprite_move_down.splice(id,1);
	sprite_move_left.splice(id,1);
	sprite_move_right.splice(id,1);

	sprite_attack_up.splice(id,1);
	sprite_attack_down.splice(id,1);
	sprite_attack_left.splice(id,1);
	sprite_attack_right.splice(id,1);

	sprite_hurt_up.splice(id,1);
	sprite_hurt_down.splice(id,1);
	sprite_hurt_left.splice(id,1);
	sprite_hurt_right.splice(id,1);


	sprite_dead.splice(id,1);
}


function get_villain_id(villain_name)
{
	var output = 2;
	var villain_name_array = villain_name.split(' ');
	villain_name = villain_name_array[0];
	switch(villain_name)
	{
		case 'Goblin':
		output = 2;
		break;
	
		case 'Caveman':
		output = 3;
		break;
		
		case 'Madeye':
		output = 4;
		break;
	}

	return output;
}

var charheights = new Array();
charheights['Goblin'] = 30;
charheights['Caveman'] = 30;
charheights['Madeye'] = 20;
var charwidths = new Array();
charwidths['Goblin'] = 20;
charwidths['Caveman'] = 20;
charwidths['Madeye'] = 20;

var charheight = 30;
var charwidth = 20;
var currsprite;
var moveable = true;

var stop_moving = false;
var movement = false;
var leftclick = true;
var upclick = true;
var downclick = true;
var rightclick = true;
var aclick = true;
var bclick = false;

var maxscroll_left = mapsize_width - 160;
// var maxscroll_top = mapsize_height - 106;
var maxscroll_top = mapsize_height - 76;
var maxmove_down = mapsize_height - charheight - 2;
var maxmove_right = mapsize_width - 15;

document.onkeydown = function(event){
     var holder;
     //IE uses this
     if(window.event){
            holder=window.event.keyCode;
     }
     //FF uses this
     else{
            holder=event.which;
     } 
     KeyDownCheck(holder);
}

/*function keyz(holder){
	alert(holder);
     if(holder == 13){
            alert('13!');
     }
}*/



// document.onkeydown = KeyDownCheck;
/*document.onkeydown = function(e)
{
	if(window.event){
	            KeyDownCheck(0);
	     }
	     //FF uses this
	     else{
	     	alert(e.KeyCode);
	           KeyDownCheck(e);
	     } 
}*/
// document.onkeydown = KeyDownCheck(e);
document.onkeyup = stop_movement;  


function use_item(id,item_id,notoggle,in_line)
{
	switch(item_id)
	{	
		case 1:
  		var itemconfirm = confirm('Are you sure you want to eat your apple?');
		if(itemconfirm)
		{
			// --- start add extra script ---
				
			var extras_count = send_extras.length;
			send_extras[extras_count] = 'use_' + id;

			// --- End add extra script ---
			document.getElementById('inventory_box').innerHTML = 'Eating apple...';
			setTimeout("get_inventory();",2000);
		}
  		break;

		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
		case 10:
		if(confirm('Are you sure you want to use your potion?'))
		{
			// --- start add extra script ---
				
			var extras_count = send_extras.length;
			send_extras[extras_count] = 'use_' + id;

			// --- End add extra script ---
			document.getElementById('inventory_box').innerHTML = 'Drinking potion...';
			setTimeout("get_inventory();",2000);
		}
		break;
		
		case 17:
		use_tool('axe',1,id);
		close_screen("inventory",notoggle);
		break;
		case 18:
		use_tool('axe',2,id);
		close_screen("inventory",notoggle);
		break;
		case 19:
		use_tool('axe',3,id);
		close_screen("inventory",notoggle);
		break;
		case 20:
		use_tool('axe',4,id);
		close_screen("inventory",notoggle);
		break;
		case 21:
		use_tool('axe',5,id);
		close_screen("inventory",notoggle);
		break;
		
		case 22:
		use_tool('pickaxe',1,id);
		close_screen("inventory",notoggle);
		break;
		
		case 23:
		use_tool('pickaxe',2,id);
		close_screen("inventory",notoggle);
		break;
		
		case 24:
		use_tool('pickaxe',3,id);
		close_screen("inventory",notoggle);
		break;
		
		case 25:
		use_tool('pickaxe',4,id);
		close_screen("inventory",notoggle);
		break;
		
		case 26:
		use_tool('pickaxe',5,id);
		close_screen("inventory",notoggle);
		break;

		default:
		//toggle_screen("craftbox");
		alert('You cannot use this item');
		break;
	}
	// alert('done');
}

function open_craftbox()
{
	if(confirm('Do you want to craft?'))
	{
		open_iframe('craft','craftbox');
	}
}

function close_craftbox()
{
	close_iframe();
}

function open_iframe(opensrc,setclass)
{
	crafting = true;
	show_screen("iframebox");
	document.getElementById("iframebox").className = setclass;
	document.getElementById("iframebox").src = opensrc + '?rand=' + Math.random();
}

function close_iframe()
{
	setTimeout('close_screen("iframebox");',20);
	document.getElementById("iframebox").src = '';
	crafting = false;
}

function check_existance(cplayer)
{
	var productElement = document.getElementById(cplayer);
	if (productElement != null)
	{
		// alert('existance');
		return true;
	}
	else
	{
		// alert('no existance');
		return false;
	}
}

var dead = false;

function KeyDownCheck(KeyID)
{
	if(dead == true || typing == true || npc_blocks_moving == true)
	{
		return;
	}
	if(inventory_open)
	{
		switch(KeyID)
		{
			case 13: select_inventory_item(selected_item); break;
			case 38: inventory_up(1); break;
			case 40: inventory_down(1); break;
		}
	}
	if(windows_open == 1)
	{
		if(document.getElementById('iframebox').src != '')
		{
			document.getElementById('iframebox').contentWindow.KeyDownCheck(KeyID);
		}
	}
	if(moveable == false && do_choose == true)
	{
		switch(KeyID)
		{
			case 13:
			multipile_choice('enter');
			break;

			case 37:
			multipile_choice('prev');
			break;

			case 39:
			multipile_choice('next');
			break;
		}
	}
	if(moveable == true && mining == false && windows_open == 0)
	{
		switch(KeyID)
		{
			case 13:
case 83:
			if(aclick==true)
			{
				aclick = false; setTimeout("aclick=true;",200); stop_movement(); attack();
			}
			break;
			case 37:
			case 65:
			if(!direction)
			{
				wcount++; direction=1;  move_player(wcount);
			}
			break;
			case 38:
			case 87:
			if(!direction)
			{
				wcount++;  direction=2;  move_player(wcount);
			}
			break;
			case 39:
case 68:

			if(movement == false && rightclick==true)
			{
				if(!direction)
				{
					wcount++; direction=3;  move_player(wcount);
				}
			}
			break;
			case 40:
			if(!direction)
			{
				wcount++;  direction=4;  move_player(wcount);
			}
			break;
			
			
			case 32:
			// B-button simulation
			if(aclick == true)
			{
				aclick = false;
				setTimeout("aclick=true;",200);
				attack(true);
			}
			break;
			
		}
	}
	else
	{
		if(KeyID == 13 && in_dialogue == true)
		{
			start_dialogue(next_dialogue);
		}
	}
}

function KeyUpCheck()
{
	if(window.event){
	            var KeyID =event.keyCode;}
}



function changewalkspeed()
{
	if(walkspeed == 3)
	{
		if(fitness > 0)
		{
			walkspeed = 5;
			document.getElementById('fitness_span').innerHTML = 'Running';
		}
	}
	else if(walkspeed == 5)
	{
		walkspeed = 3;
		document.getElementById('fitness_span').innerHTML = 'Walking';
	}
	// walkspeed=parseInt(window.prompt('What is the walkspeed?'));
}

function drain_fitness()
{
	if(walkspeed == 5)
	{
		if(fitness > 0)
		{
			fitness--;
			fitness--;
		}
		if(fitness > 0)
		{
			fitness--;
			fitness--;
		}
		else if(fitness == 0)
		{
			changewalkspeed();
		}
	}
	else if(walkspeed == 3)
	{
		if(fitness < 100)
		{
			fitness++;
			fitness++;
		}
	}
	document.getElementById('fitness_bar').style.width = fitness + '%';
	setTimeout("drain_fitness();",500);
}
setTimeout("drain_fitness();",3000);

var direction = 0;
var f_direction = 4;

function stop_movement()
{
	if(npc_blocks_moving)
	{
		return;
	}
	stop_moving = true;
	movement = false;
	if(moveable == true)
	{
		if(direction==2)
		{
			change_sprite('player_' + player_id + '_sprite',sprite_up[my_character].src);
			currentsprite = 'sprite_up';
		}
		if(direction==4)
		{
			change_sprite('player_' + player_id + '_sprite',sprite_down[my_character].src);
			currentsprite = 'sprite_down';
		}
		if(direction==1)
		{
			change_sprite('player_' + player_id + '_sprite',sprite_left[my_character].src);
			currentsprite = 'sprite_left';
		}
		if(direction==3)
		{
			change_sprite('player_' + player_id + '_sprite',sprite_right[my_character].src);
			currentsprite = 'sprite_right';
		}
	}
	direction = 0;
}

var wcount = 0;

var lastTickTime = 0;
var speedFactor = 1;
var syncTime = 80;
var currentTickTime = 0;

function move_player()
{
	//placeholder function
}

function move_player_new(gwcount)
{
	
	if(connect_problems || npc_blocks_moving || direction == false || dead ) /*|| gwcount != wcount*/
	{
		//console.log(connect_problems, npc_blocks_moving, direction, dead );
		return;
	}
	
	
	
	var currentSpeed = walkspeed * speedFactor;
	
	f_direction = direction;
	wcount++;
			if(direction == 4)
			{
				// down
				var newPotentialX = pos.x;
				var newPotentialY = pos.y + currentSpeed;
				var moveToX = Math.round(newPotentialX);
				var moveToY = Math.round(newPotentialY);
				var extra_permission_check = (moveToY < maxmove_down);
				var changeToSprite = 'sprite_move_down';
				var changeToSpriteArray = sprite_move_down;
			}
			if(direction == 2)
			{
				// up
				var newPotentialX = pos.x;
				var newPotentialY = pos.y - currentSpeed;
				var moveToX = Math.round(newPotentialX);
				var moveToY = Math.round(newPotentialY);
				var extra_permission_check = (moveToY > 0);
				var changeToSprite = 'sprite_move_up';
				var changeToSpriteArray = sprite_move_up;
			}
			if(direction == 1)
			{
				var newPotentialX = pos.x - currentSpeed;
				var newPotentialY = pos.y;
				var moveToX = Math.round(newPotentialX);
				var moveToY = Math.round(newPotentialY);
				var extra_permission_check = (moveToX > 0);
				var changeToSprite = 'sprite_move_left';
				var changeToSpriteArray = sprite_move_left;
				
			}
			if(direction == 3)
			{
				var newPotentialX = pos.x + currentSpeed;
				var newPotentialY = pos.y;
				var moveToX = Math.round(newPotentialX);
				var moveToY = Math.round(newPotentialY);
				var extra_permission_check = (moveToX < maxmove_right);
				var changeToSprite = 'sprite_move_right';
				var changeToSpriteArray = sprite_move_right;
			}
			
			
			if(extra_permission_check && check_movable(moveToX, moveToY))
			{
				currentsprite = changeToSprite;
				change_sprite('player_' + player_id + '_sprite',changeToSpriteArray[my_character].src);
				var old_rounded_x = Math.round(pos.x);
				var old_rounded_y = Math.round(pos.y);
				
				pos.x = newPotentialX;
				pos.y = newPotentialY;
				
				viewportLeft += moveToX - old_rounded_x;
				viewportTop += moveToY - old_rounded_y;
				document.getElementById('maincontain').scrollLeft = Math.max(0, Math.min(mapsize_width - viewportWidth, viewportLeft));
				document.getElementById('maincontain').scrollTop = Math.max(0, Math.min(mapsize_height - viewportHeight, viewportTop));
				
				document.getElementById('player_' + player_id).style.left = moveToX + 'px';
				document.getElementById('player_' + player_id).style.top = moveToY + 'px';
				document.getElementById('player_' + player_id).style.zIndex = moveToY + charheight;
				
			}
			// alert(player_id + 'cplayer top: '+document.getElementById('player_'+player_id).style.zIndex)
}

var attacking = '';
var attack_left;
var attack_right;
var attack_top;
var attack_bottom;

function attack(b_button)
{
	stop_movement();
	moveable = false;
	var mana = false;
	if(b_button)
	{
		mana = true;
		if(magic_value < 20)
		{
			return;
		}
		magic_value = magic_value - 20;
	}
	// alert(currentsprite);
	if(f_direction == 2)
	{
		var hitdirec = 'up';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + 7;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 5;
		attack_bottom = attack_top + charwidth - 5;
	}
	if(f_direction == 4)
	{
		var hitdirec = 'down';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + 7;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + 5;
	}
	if(f_direction == 1)
	{
		var hitdirec = 'left';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) - 5;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + charheight - 30;
	}
	if(f_direction == 3)
	{
		var hitdirec = 'right';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + charwidth;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + charheight - 30;
	}
	
	
	var doattack = true;
	var contloop = true;
	// -- OBJECTS CHECK SCRIPT

	var arr = new Array();
	arr = document.getElementsByName('object'); 
	for(var a in objects)
	{
		var cobject = objects[a];
		var cobjtype = cobject[0];
		var cobjid = cobject[1];
		var cobjname = cobject[2];
		var cobjsprite = cobject[3];
		var cobjcoords = cobject[4];
		var cobjcoordssplit = cobjcoords.split(',');
		var cobjleft = cobjcoordssplit[0];
		var cobjtop = cobjcoordssplit[1];
		var cobjdimensions = cobject[5];
		var cobjdimensionssplit = cobjdimensions.split(',');
		var cobjwidth = cobjdimensionssplit[0];
		var cobjheight = cobjdimensionssplit[1];
		var cobjproperty1 = cobject[6];

		save_for_after_dialogue = a;
		
		if(contloop == true && !b_button)
		{
			// Check the player attemps to talk to a NPC
			var obj = document.getElementById('object_'+cobjid);
			var obj_top = parseInt(obj.style.top);
			var obj_bottom = obj_top + charheight;
			var obj_left = parseInt(obj.style.left);
			var obj_right = obj_left + charwidth;
		
			if((attack_left > obj_left && attack_left < obj_right) || (attack_right > obj_left && attack_right < obj_right))
			{

				if((attack_top > obj_top && attack_top < obj_bottom) || (attack_bottom > obj_top && attack_bottom < obj_bottom))
				{
					// alert('How dare you hit a NPC...?');
					doattack = false;
					movable = true;
					contloop = false;
					if(cobjtype == 'npc')
					{
						converttodialogue(cobjproperty1);
						if(f_direction == 2)
						{
							change_sprite(obj.id + '_sprite',sprite_down['npc_'+cobjid].src);
						}
						else if(f_direction == 4)
						{
							change_sprite(obj.id + '_sprite',sprite_up['npc_'+cobjid].src);
						}
						else if(f_direction == 1)
						{
							change_sprite(obj.id + '_sprite',sprite_right['npc_'+cobjid].src);
						}
						else if(f_direction == 3)
						{
							change_sprite(obj.id + '_sprite',sprite_left['npc_'+cobjid].src);
						}
					}
				}
			}
		}
	}
	if(contloop == true)
	{
		var arr = new Array();
		arr = document.getElementsByName('lying_item'); 
		for(var i = 0; i < arr.length; i++)
		{
			var obj = document.getElementsByName('lying_item').item(i);

			var item_top = parseInt(obj.style.top);
			var item_bottom = item_top + 15;
			var item_left = parseInt(obj.style.left);
			var item_right = item_left + 15;
	
			if((item_left > item_left && attack_left < item_right) || (attack_right > item_left && attack_right < item_right))
			{
				//alert('Hit 1...');
				if((attack_top > item_top && attack_top < item_bottom) || (attack_bottom > item_top && attack_bottom < item_bottom))
				{
					// alert('Hit item ' + obj.id + '!');
					// --- start add extra script ---
					var extras_count = send_extras.length;
					send_extras[extras_count] = 'pick_' + obj.id;
					// ---- end add extra script
					doattack = false;
					movable = true;
					moveable = true;
					contloop = false;
				}
			}
		}
	}
	// -- END OF OBJECTS CHECK SCRIPT
	if(doattack == true)
	{
		// alert('doattack');
		if(f_direction == 2)
		{
			// alert(currentsprite);
			if(mana)
			{
				currentsprite = 'sprite_attack_up_mana';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_up_mana[my_character].src);
			}
			else
			{
				currentsprite = 'sprite_attack_up';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_up[my_character].src);
			}
			setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_up[my_character].src); currentsprite = 'sprite_up'; moveable = true;}", 1000);
		}
		if(f_direction == 4)
		{
			if(mana)
			{
				currentsprite = 'sprite_attack_down_mana';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_down_mana[my_character].src);
			}
			else
			{
				currentsprite = 'sprite_attack_down';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_down[my_character].src);
			}
			setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_down[my_character].src); currentsprite = 'sprite_down'; moveable = true;}", 1000);
		}
		if(f_direction == 1)
		{
			if(mana)
			{
				currentsprite = 'sprite_attack_left_mana';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_left_mana[my_character].src);
			}
			else
			{
				currentsprite = 'sprite_attack_left';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_left[my_character].src);
			}
			setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_left[my_character].src); currentsprite = 'sprite_left'; moveable = true;}", 1000);
		}
		if(f_direction == 3)
		{
			if(mana)
			{
				currentsprite = 'sprite_attack_right_mana';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_right_mana[my_character].src);
			}
			else
			{
				currentsprite = 'sprite_attack_right';
				change_sprite('player_' + player_id + '_sprite',sprite_attack_right[my_character].src);
			}
			setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_right[my_character].src); currentsprite = 'sprite_right'; moveable = true;}", 1000);
		}
		if(b_button)
		{
			check_attack(hitdirec,true);
		}
		else
		{
			check_attack(hitdirec);
		}
	}
}

function check_attack(hitdirec,b_button)
{

	var display_attack_bottom = attack_bottom;
var display_attack_left = attack_left;
var display_attack_right = attack_right;
var display_attack_top = attack_top;
	if(b_button)
	{
		var extras_count = send_extras.length;
		send_extras[extras_count] = 'mana';
		if(hitdirec == 'left')
		{
			attack_left = attack_left - 100;
		}
		if(hitdirec == 'right')
		{
			attack_right = attack_right + 100;
		}
		if(hitdirec == 'up')
		{
			attack_top = attack_top - 100;
		}
		if(hitdirec == 'down')
		{
			attack_bottom = attack_bottom + 100;
		}
		display_attack_left = attack_left;
		display_attack_top = attack_top;
		display_attack_right = attack_right;
		display_attack_bottom = attack_bottom;
		if(hitdirec == 'right' || hitdirec == 'left')
		{
			display_attack_bottom = display_attack_bottom + 5;
		}
	}
	else
	{
		display_attack_right = attack_left + 5;
		display_attack_bottom = attack_top + 5;
	}

	var newdiv = document.createElement('div');
	  var divIdName = 'attack_' + player_id;
	  newdiv.setAttribute('id',divIdName);
	  newdiv.setAttribute('style', 'position: absolute; left: '+display_attack_left+'; top: '+display_attack_top+'; width: '+(display_attack_right - display_attack_left)+'px; height: '+(display_attack_bottom - display_attack_top)+'px; background-image: url("images/hit_ind.png"); z-index: '+document.getElementById('player_'+player_id).style.zIndex+';');
	  document.getElementById('maincontain').appendChild(newdiv);
	setTimeout("var elObj = document.getElementById('"+divIdName+"'); elObj.parentNode.removeChild(elObj);",500);

	
	
	
	var arr = new Array();
	arr = document.getElementsByName('villain');
	for(var i in villains)
	{
		var obj = document.getElementById('villain_'+villains[i][0]);
		var villain_left = villains[i][1];
		var villain_top = villains[i][2];
		var villain_right = villain_left + villains[i][5];
		var villain_bottom = villain_top + villains[i][6];
		
		if(attack_left <= villain_right && attack_right >= villain_left && attack_top <= villain_bottom && attack_bottom >= villain_top)
		{
			// alert('Hit villain ' + obj.id + '!');
			// --- start add extra script ---
					
			var extras_count = send_extras.length;
			if(b_button)
			{
				send_extras[extras_count] = 'attack_' + obj.id + '_' + hitdirec + '_villain' + '_mana';
			}
			else
			{
					send_extras[extras_count] = 'attack_' + obj.id + '_' + hitdirec + '_villain';
			}
				// ---- end add extra script
		}
	}
	
	
}

var other_attack_top;
var other_attack_left;
var other_attack_bottom;
var other_attack_right;



function opponent_attack(hitdirec,opponent_id,mana,is_player)
{

	var attack_top = other_attack_top;
	var attack_bottom = other_attack_bottom;
	var attack_left = other_attack_left;
	var attack_right = other_attack_right;
	var display_attack_top = attack_top;
	var display_attack_bottom = attack_bottom;
	var display_attack_left = attack_left;
	var display_attack_right = attack_right;
	
	if(mana)
	{
		if(hitdirec == 'left')
		{
			attack_left = attack_left - 100;
			display_attack_left = attack_left;
		}
		if(hitdirec == 'right')
		{
			attack_right = attack_right + 100;
			display_attack_right = attack_right;
		}
		if(hitdirec == 'up')
		{
			attack_top = attack_top - 100;
			display_attack_top = attack_top;
		}
		if(hitdirec == 'down')
		{
			attack_bottom = attack_bottom + 100;
			display_attack_bottom = attack_bottom;
		}
	}
	var zindex = display_attack_top;
	if(hitdirec == 'left' || hitdirec == 'right')
	{
		display_attack_bottom = attack_bottom + 5;
		var zindex = display_attack_bottom;
	}
	if(!mana)
	{
		if(hitdirec == 'up')
		{
			//alert(display_attack_left + ',' +  display_attack_top + ',' +  display_attack_right + ',' +  display_attack_bottom);
			//alert(display_attack_right-display_attack_left);
			display_attack_top = attack_top;
			display_attack_bottom = attack_bottom - 10;
			var zindex = display_attack_bottom;
		}
	}
	
	var newdiv = document.createElement('div');
	  var divIdName = 'attack_' + opponent_id;
	  newdiv.setAttribute('id',divIdName);
	  // newdiv.setAttribute("class", "tab_iframe");
	  newdiv.setAttribute('style', 'position: absolute; left: '+display_attack_left+'; top: '+display_attack_top+'; width: '+(display_attack_right-display_attack_left)+'px; height: '+(display_attack_bottom-display_attack_top)+'px; background-image: url("images/hit_ind.png"); z-index: '+(document.getElementById('player_'+player_id).style.zIndex + 1)+';');
	  // newdiv.setAttribute('src', 'newtab.php');
	  // newdiv.innerHTML = 'Element Number '+divIdName+' has been added! <a href=\'#\' onclick=\'removeElement('+divIdName+')\'>Remove the div "'+divIdName+'"</a>';
	  document.getElementById('maincontain').appendChild(newdiv);
	setTimeout("var elObj = document.getElementById('"+divIdName+"'); elObj.parentNode.removeChild(elObj);",500);


	
if(dead == false)
{	
// alert(attack_left + ',' + attack_top + ',' + attack_right + ',' + attack_bottom);
		var obj = document.getElementById('player_'+player_id);
		// obj.style.left;
		var villain_top = parseInt(obj.style.top);
		var villain_bottom = villain_top + charheight;
		var villain_left = parseInt(obj.style.left);
		var villain_right = villain_left + charwidth;
		//alert('Top: ' + villain_top + '. Left: ' + villain_left +  '. Villain right: ' + villain_right + '. Attack left: '  + attack_left + '. Attack right: ' + attack_right);

		//if((attack_left > villain_left && attack_left < villain_right) || (attack_right > villain_left && attack_right < villain_right))
		if(attack_left <= villain_right && attack_right >= villain_left)
		{
			// alert('hit 1');
			//if((attack_top > villain_top && attack_top < villain_bottom) || (attack_bottom > villain_top && attack_bottom < villain_bottom))
			// alert(attack_top +' <= ' + villain_bottom + ' && ' + attack_bottom + ' >= ' + villain_top);
			if(attack_top <= villain_bottom && attack_bottom >= villain_top)
			{
				// document.getElementById('health_' + obj.id).style.width = parseInt(document.getElementById('health_' + obj.id).style.width) - 5 + '%';
				// --- start add extra script ---
				
				var extras_count = send_extras.length;
				// alert(extras_count + ': ');
				if(is_player == false || (is_player == true && pvp == true))
				{
					if(is_player == true)
					{
						send_extras[extras_count] = 'hit_' + opponent_id + '_' + hitdirec;
					}
					else
					{
						send_extras[extras_count] = 'hit_' + opponent_id + '_' + hitdirec + '_villain';
					}
					// ---- end add extra script
					// alert('he gotcha');
					stop_movement();
					moveable = false;
					if(hitdirec == 'up')
					{
						f_direction = 4;
						currentsprite = 'sprite_hurt_up';
						change_sprite('player_' + player_id + '_sprite',sprite_hurt_up[my_character].src);
						document.getElementById('player_' + player_id).style.top = parseInt(document.getElementById('player_' + player_id).style.top) - 2 + 'px';
						setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_down[my_character].src); currentsprite = 'sprite_down'; moveable = true;}", 1000);
					}
					if(hitdirec == 'down')
					{
						f_direction = 2;
						currentsprite = 'sprite_hurt_down';
						change_sprite('player_' + player_id + '_sprite',sprite_hurt_down[my_character].src);
						document.getElementById('player_' + player_id).style.top = parseInt(document.getElementById('player_' + player_id).style.top) + 2 + 'px';
						setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_up[my_character].src); currentsprite = 'sprite_up'; moveable = true;}", 1000);
					}
					if(hitdirec == 'left')
					{
						f_direction = 3;
						currentsprite = 'sprite_hurt_left';
						change_sprite('player_' + player_id + '_sprite',sprite_hurt_left[my_character].src);
						document.getElementById('player_' + player_id).style.left = parseInt(document.getElementById('player_' + player_id).style.left) - 2 + 'px';
						setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_right[my_character].src); currentsprite = 'sprite_right'; moveable = true;}", 1000);
					}
					if(hitdirec == 'right')
					{
						f_direction = 1;
						currentsprite = 'sprite_hurt_right';
						change_sprite('player_' + player_id + '_sprite',sprite_hurt_right[my_character].src);
						document.getElementById('player_' + player_id).style.left = parseInt(document.getElementById('player_' + player_id).style.left) + 2 + 'px';
						setTimeout("if(dead == false){change_sprite('player_' + player_id + '_sprite',sprite_left[my_character].src); currentsprite = 'sprite_left'; moveable = true;}", 1000);
					}
					// alert('he got ya');
					document.getElementById('health_player_' + player_id).style.width = parseInt(document.getElementById('health_player_' + player_id).style.width) - 5 + '%';
					
				}
			}
		}
	

}

}







var responseraw;
var cplayer;
var updaterequest;
var characterrequest;
var inventoryrequest;
var msgsrequest;
var postrequest;
var requeststats;
var traderequest;
var questrequest;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
   updaterequest=new XMLHttpRequest();
   characterrequest=new XMLHttpRequest();
   inventoryrequest=new XMLHttpRequest();
   msgsrequest = new XMLHttpRequest();
   postrequest=new XMLHttpRequest();
   requeststats=new XMLHttpRequest();
   traderequest=new XMLHttpRequest();
   questrequest=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  updaterequest=new ActiveXObject("Microsoft.XMLHTTP");
  characterrequest=new ActiveXObject("Microsoft.XMLHTTP");
  inventoryrequest=new ActiveXObject("Microsoft.XMLHTTP");
  msgsrequest=new ActiveXObject("Microsoft.XMLHTTP");
  postrequest=new ActiveXObject("Microsoft.XMLHTTP");
  requeststats=new ActiveXObject("Microsoft.XMLHTTP");
  traderequest=new ActiveXObject("Microsoft.XMLHTTP");
  questrequest=new ActiveXObject("Microsoft.XMLHTTP");
  }

var check_existance_var;
var smooth_started = new Array();
var currenttop = new Array();
var currentleft = new Array();
var newtop = new Array();
var newleft = new Array();


function microtime (get_as_float) {
    // Returns either a string or a float containing the current time in seconds and microseconds  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/microtime    // +   original by: Paulo Freitas
    // *     example 1: timeStamp = microtime(true);
    // *     results 1: timeStamp > 1000000000 && timeStamp < 2000000000
    var now = new Date().getTime() / 1000;
    var s = parseInt(now, 10); 
    return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
}

var parse_sprites = new Array();
// [object_id,stage,coordleft,coordtop,toleft,totop,type]


function render()
{
	var now = new Date().getTime();
	currentTickTime = now;
	if(!lastTickTime) { lastTickTime = now;}
	speedFactor = (now - lastTickTime)/syncTime;
	
	
	move_player_new();
	render_movement();
	var wait_time = Math.max(1, (syncTime - (new Date().getTime() - now)));
	lastTickTime = now;
	setTimeout("render();",wait_time);
}

setTimeout("render();",3000);

var lag = 500; // for rendering of other objects
var currentUpdateTime = 0;
var lastUpdateTime = 0;

function render_movement()
{
	// drain_fitness();
	
	var virtualNow = currentTickTime - lag;
	
	var parse_sprites_length = parse_sprites.length;
	for(var i = 0; i < parse_sprites_length; i++)
	{
		var current_sprite_array = parse_sprites[i];
		if(current_sprite_array)
		{
			var object_id = current_sprite_array[0];
			var stage = current_sprite_array[1];
			var from_left = current_sprite_array[2];
			var from_top = current_sprite_array[3];
			var to_left = current_sprite_array[4];
			var to_top = current_sprite_array[5];
			var object_height = charheight;
			if(current_sprite_array[7])
			{
				object_height = current_sprite_array[7];
			}
			var spriteGetId = current_sprite_array[8];
			var current_sprite = current_sprite_array[9];
			// alert(arrayset);
			
			var endTime = currentUpdateTime + lag;
			var f = Math.max(0, Math.min(1, (currentTickTime - lastUpdateTime)/(endTime-lastUpdateTime)));
			var currentLeft = Math.round(from_left + f * (to_left - from_left));
			var currentTop = Math.round(from_top + f * (to_top - from_top));
			
			eval(current_sprite_array[6]+'[1] = '+currentLeft+'; ' + current_sprite_array[6]+'[2] = '+currentTop+';');
			
			if(Math.abs(currentLeft - to_left) == 1)
			{
				currentLeft = to_left;
			}
			if(Math.abs(currentTop - to_top) == 1)
			{
				currentTop = to_top;
			}
			
			/*console.log(object_id + ' must go from ' + from_left + ' to '+ to_left);
			console.log('f = ('+virtualNow+' - '+lastUpdateTime+')/('+currentUpdateTime+' - '+ lastUpdateTime + ' = ' + f);*/
			
			/*
			if(stage == 1 && Math.abs(to_top - current_top) < 3 && Math.abs(to_left - current_left < 3))
			{
				var new_top = parseInt(to_top);
				var new_left = parseInt(to_left);
			}
			else
			{
				var new_left = get_new_coordinate(current_left,to_left,stage);
				var new_top = get_new_coordinate(current_top,to_top,stage);
			}*/
			document.getElementById(object_id).style.left = currentLeft + 'px';
			document.getElementById(object_id).style.top = currentTop + 'px';
			document.getElementById(object_id).style.zIndex = currentTop + object_height;
			
			
			if((f >= 1 || (currentLeft == to_left && currentTop == to_top))  && (current_sprite.substr(0,12) == 'sprite_move_'))
			{
				

				var goDirection = current_sprite.split('_')[2];
				console.log("change_sprite('"+object_id +"_sprite',sprite_"+goDirection+"['"+spriteGetId+"'].src);");
				eval("change_sprite('"+object_id +"_sprite',sprite_"+goDirection+"['"+spriteGetId+"'].src);");
				
			}
			
			/*if(f >= 1)
			{
				parse_sprites.splice(i,1);
			}*/
		}
	}
	//var wait_time = 70 + parse_sprites_length * 30;
	//setTimeout("render_movement();",wait_time);
}



function get_new_coordinate(currentcoord,tocoord,stage)
{
	currentcoord = currentcoord;
	tocoord = tocoord;
	stage = stage;
	var oldcoord = Math.floor(currentcoord - (stage - 1)/4 * tocoord)/(1-(stage-1)/4);
	var newcoord = Math.ceil(stage/4 * (tocoord - oldcoord) + oldcoord);
	// stage/4 * (tocoord - currentcoord) + currentcoord
	return newcoord;
}



var currmsg;
var checkmsg;
var delmsg;
var checktoppos;
var checkleftpos;
var checktopdiff;
var checkleftdiff;
var topdirec;
var leftdirec;
var equipped_item = new Array(0,0);
// inv id, item_id

// ------------------------------------
var last_sprite = new Array();
var players = new Array();
var villains = new Array();
var items = new Array();
var specialobjects = new Array();
var extrablocks = new Array();
var lasttraderequest = 0;

// var currplayers = new Array();
// ------------------------------------

function handle_info(responseraw)
{
	var now = new Date().getTime();
	if(!currentUpdateTime) { currentUpdateTime = now; }
	lastUpdateTime = currentUpdateTime;
	currentUpdateTime = now;
		
			responseraw = responseraw.split(';')
			var currplayers = new Array();
			var currvillains = new Array();
			var curritems = new Array();
			var currspecialobjects = new Array();
			parse_sprites = new Array();
			for(var i in responseraw)
			{
				responsetext = responseraw[i].split(',');
				if(responsetext[7] == 'traderequest')
				{
					if(responsetext[0] != lasttraderequest)
					{
						// alert('You got a trade request. id: ' +responsetext[0]);
						lasttraderequest = responsetext[0];

						get_trade_request(responsetext[0],responsetext[2]);
						/*if(confirm('Accept trade request from '+responsetext[2]+'?'))
						{
							accept_trade(responsetext[0]);
						}
						else
						{
							alert('Okay :okay:');
						}*/
					}
				}
				else if(responsetext[7] == 'player')
				{
					var checksprite = responsetext[3];
					if(responsetext[0] == player_id)
					{
						cplayer = 'player_' + responsetext[0];
						var eq_item = responsetext[8];
						var eq_item_id = responsetext[9];
						var eq_item_percentage = responsetext[10];
						var magic_percentage = responsetext[11];
						magic_value = responsetext[12];
						var reset_left = responsetext[13];
						var reset_top = responsetext[14];
						if(reset_left != 0)
						{
							alert('Force reset');
							document.getElementById(cplayer).style.left = reset_left + 'px';
							document.getElementById(cplayer).style.top = reset_top + 'px';
							document.getElementById('maincontain').scrollTop = reset_top - 100;
							document.getElementById('maincontain').scrollLeft = reset_left - 155;
							stop_movement();
						}
						if(responsetext[15])
						{
							fitness = responsetext[15];
						}
						document.getElementById('magic_bar').style.width = magic_percentage + '%';
						document.getElementById('my_magic').innerHTML = magic_percentage + '%';
						//alert('eq_item: '+eq_item);
						if(eq_item != equipped_item[0] || eq_item_id != equipped_item[1] || eq_item_percentage != equipped_item[2])
						{
							// alert('item not the same');
							set_equipped_item(eq_item,eq_item_id,eq_item_percentage);
						}
						if(checksprite == 'sprite_dead')
						{
							if(dead == false)
							{
								dead = true;
								if(checksprite == 'sprite_dead' && last_sprite[responsetext[0]] != 'sprite_dead')
								{
									change_sprite(cplayer + '_sprite',sprite_dead[my_character].src);
								}
								setTimeout("redirecting_page = true; window.location = 'die.php';",2000);
								
							}
						}
						document.getElementById('health_player_' + responsetext[0]).style.width = responsetext[6] + '%';
						document.getElementById('my_health').innerHTML = responsetext[6] + '%';
					}
					if(responsetext[0] != player_id)
					{
						cplayer = 'player_' + responsetext[0];
						var cplayerid = responsetext[0];
						
						var 
						//currplayers[responsetext[0]] = 'a';
						//,objstage,objleft,objtop,objwidth,objheight,minetime,minetool,tool_level
						
						check_existance_var = check_existance(cplayer);
						if(check_existance_var)
						{
							// alert('exists');
							currplayers[responsetext[0]] = ['a',players[responsetext[0]][1],players[responsetext[0]][2],responsetext[1],responsetext[2]];
						}
						else
						{
							load_sprite(cplayerid);
							document.getElementById('mainscreen').innerHTML = document.getElementById('mainscreen').innerHTML + "<div oNclick='request_stats("+cplayerid+");' id='"+cplayer+"' style='position: absolute; top: "+responsetext[2]+"px; left: "+responsetext[1]+"px; width: 20px; height: 30px; z-index: "+responsetext[1]+"; visibility: visible;'><img id='player_"+responsetext[0]+"_sprite' style='position: absolute; top: 0px; left: 0px; width: 20px; height: 30px;' src=''><div class='player_name'><div id='health_player_"+responsetext[0]+"' class='health_bar' style='width: "+responsetext[6]+"%;'>&nbsp;</div>"+responsetext[5]+"</div><div class='chattextcontain'><div class='chattext' style='visibility: hidden;' id='chattext_"+responsetext[0]+"'>&nbsp;</div></div></div>";


							eval("change_sprite(cplayer + '_sprite',"+responsetext[3]+"[cplayerid].src);");
							// alert('A new player entered the room');
							// players[responsetext[0]] = 'a';
							currplayers[responsetext[0]] = ['a',responsetext[1],responsetext[2],responsetext[1],responsetext[2]];
							players[responsetext[0]] = currplayers[responsetext[0]];
						}
						// alert(cplayer);
						document.getElementById('health_player_'+responsetext[0]).style.width = responsetext[6] + '%';

							    
							    
						// ---------------------------------------------------------------------
							    
						// checktoppos = parseInt(document.getElementById(cplayer).style.top);
						// checkleftpos = parseInt(document.getElementById(cplayer).style.left);
						checkleftpos = parseInt(document.getElementById(cplayer).style.left);
						checktoppos = parseInt(document.getElementById(cplayer).style.top);
						checktopdiff = checktoppos - responsetext[2];
						checkleftdiff = checkleftpos - responsetext[1];
						
						if(checktopdiff != 0)
						{
							if(checktopdiff > 0)
							{
								topdirec = 'up';
							}
							if(checktopdiff < 0)
							{
							    	topdirec = 'down';
							    	checktopdiff = checktopdiff * -1;
							}
						}
						else
						{
							topdirec = 'still';
						}
						if(checkleftdiff != 0)
						{
							if(checkleftdiff > 0)
							{
							    	leftdirec = 'left';
							}
							if(checkleftdiff < 0)
							{
							    	leftdirec = 'right';
							    	checkleftdiff = checkleftdiff * -1;
							}
						}
						else
						{
							leftdirec = 'still';
						}
						
						currsprite = responsetext[3];
						
						
						if(leftdirec == 'still' && topdirec == 'still')
						{
							if(currsprite == 'sprite_move_up')
							{
								currsprite = 'sprite_up';
							}
							if(currsprite == 'sprite_move_down')
							{
								currsprite = 'sprite_down';
							}
							if(currsprite == 'sprite_move_left')
							{
								currsprite = 'sprite_left';
							}
							if(currsprite == 'sprite_move_right')
							{
								currsprite = 'sprite_right';
							}
						}
							      
							    
							    
						
						if(currsprite == 'sprite_up' || currsprite == 'sprite_down' || currsprite == 'sprite_left' || currsprite == 'sprite_right')
						{
							if(topdirec != 'still')
							{
								if(leftdirec != 'still')
								{
								    	if(checkleftdiff > checktopdiff)
								    	{
								    		if(leftdirec == 'left')
								    		{
									    		currsprite = 'sprite_move_left';
								    		}
								    		if(leftdirec == 'right')
								    		{
								    			currsprite = 'sprite_move_right';
								    		}
								    	}
								    	else
								    	{
								    		if(topdirec == 'up')
								    		{
								    			currsprite = 'sprite_move_up';
								    		}
								    		if(topdirec == 'down')
								    		{
								    			currsprite = 'sprite_move_down';
								    		}
								    	}
								}
								else
								{
								    	if(topdirec == 'up')
								    	{
								    		currsprite = 'sprite_move_up';
								    	}
								    	if(topdirec == 'down')
								    	{
										currsprite = 'sprite_move_down';
									}
								}
							}
							else
							{
								if(checkleftdiff != 'still')
								{
								    	if(leftdirec == 'left')
								    	{
								    		currsprite = 'sprite_move_left';
								    	}
								    	if(leftdirec == 'right')
								    	{
								    		currsprite = 'sprite_move_right';
									}
								}
							}
						}
							    
						// -----------------------------------------------------------------
	parse_sprites[parse_sprites.length] = [cplayer,1,checkleftpos,checktoppos,responsetext[1],responsetext[2],'players['+cplayerid+']',players[cplayerid][6],cplayerid, currsprite];
	//parse_sprites[parse_sprites.length] = [cplayer,1,checkleftpos,checktoppos,responsetext[1],responsetext[2],"villains['"+cplayerid+"']",villains[cplayerid][6],villain_character,currsprite];
						currmsg = responsetext[4];
						// alert(currmsg);
						checkmsg = document.getElementById('chattext_' + responsetext[0]).innerHTML;
						// alert(currmsg);
						if(currmsg != '' && currmsg != 'nomsg' && currmsg != checkmsg)
						{
							document.getElementById('chattext_' + responsetext[0]).innerHTML = currmsg;
							document.getElementById('chattext_' + responsetext[0]).style.display = '';
							setTimeout("document.getElementById('chattext_"+responsetext[0]+"').style.display = 'none';",10000);
						}
							    
						// alert(last_sprite[responsetext[0]]);

						if(currsprite == 'sprite_attack_up' && last_sprite[responsetext[0]] != 'sprite_attack_up')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = players[cplayerid][1] + 7;
							other_attack_right = other_attack_left + 5;
							// other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 5;
							other_attack_top = players[cplayerid][2] + 5;
							other_attack_bottom = other_attack_top + charwidth - 5;
							opponent_attack('up',responsetext[0],false,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_up[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_down' && last_sprite[responsetext[0]] != 'sprite_attack_down')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = players[cplayerid][1] + 7;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + 5;
							opponent_attack('down',responsetext[0],false,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_down[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_left' && last_sprite[responsetext[0]] != 'sprite_attack_left')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) - 5;
							other_attack_left = players[cplayerid][1] - 5;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + charheight - 30;
							opponent_attack('left',responsetext[0],false,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_left[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_right' && last_sprite[responsetext[0]] != 'sprite_attack_right')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + charwidth;
							other_attack_left = players[cplayerid][1] + charwidth;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + charheight - 30;
							opponent_attack('right',responsetext[0],false,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_right[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_up_mana' && last_sprite[responsetext[0]] != 'sprite_attack_up_mana')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = players[cplayerid][1] + 7;
							other_attack_right = other_attack_left + 5;
							// other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 5;
							other_attack_top = players[cplayerid][2] + 5;
							other_attack_bottom = other_attack_top + charwidth - 5;
							opponent_attack('up',responsetext[0],true,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_up_mana[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_down_mana' && last_sprite[responsetext[0]] != 'sprite_attack_down_mana')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = players[cplayerid][1] + 7;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + 5;
							opponent_attack('down',responsetext[0],true,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_down_mana[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_left_mana' && last_sprite[responsetext[0]] != 'sprite_attack_left_mana')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) - 5;
							other_attack_left = players[cplayerid][1] - 5;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + charheight - 30;
							opponent_attack('left',responsetext[0],true,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_left_mana[cplayerid].src);
						}
						if(currsprite == 'sprite_attack_right_mana' && last_sprite[responsetext[0]] != 'sprite_attack_right_mana')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + charwidth;
							other_attack_left = players[cplayerid][1] + charwidth;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = players[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + charheight - 30;
							opponent_attack('right',responsetext[0],true,true);
							
							change_sprite(cplayer + '_sprite',sprite_attack_right_mana[cplayerid].src);
						}

						
						if(currsprite != 'sprite_attack_up' && currsprite != 'sprite_attack_down' && currsprite!='sprite_attack_left' && currsprite!='sprite_attack_right' && currsprite != 'sprite_attack_up_mana' && currsprite != 'sprite_attack_down_mana' && currsprite!='sprite_attack_left_mana' && currsprite!='sprite_attack_right_mana')
						{
							eval("if(currsprite == '"+currsprite+"' && last_sprite[responsetext[0]] != '"+currsprite+"'){change_sprite(cplayer + '_sprite',"+currsprite+"[cplayerid].src);}");
						}
						loadit = true;
						last_sprite[responsetext[0]] = currsprite;
					    	// alert('testa: ' + i);
					}
				}
				else if(responsetext[7] == 'villain')
				{
					//if(responsetext[0] != player_id)
					//{
						responsetext[0] = 'v' + responsetext[0];
						cplayer = 'villain_' + responsetext[0];
						var cplayerid = responsetext[0];
						// currvillains[responsetext[0]] = 'a';
						
						var cname = responsetext[5];
						var cnamesplit = cname.split(' ');
						var cname = cnamesplit[0];
						// villain_character = get_villain_id(cname);
						villain_character = cname;
						var villain_height = charheights[villain_character];
						var villain_width = charwidths[villain_character];
						
						
						check_existance_var = check_existance(cplayer);
						if(check_existance_var)
						{
							// alert('exists');
							currvillains[responsetext[0]] = [responsetext[0],villains[responsetext[0]][1],villains[responsetext[0]][2],responsetext[1],responsetext[2],villain_width,villain_height,villain_character];
						}
						else
						{
							document.getElementById('mainscreen').innerHTML = document.getElementById('mainscreen').innerHTML + "<div name='villain' id='"+cplayer+"' style='position: absolute; top: "+responsetext[2]+"px; left: "+responsetext[1]+"px; width: "+villain_width+"px; height: "+villain_height+"px; z-index: "+responsetext[1]+"; visibility: visible;'><img id='"+cplayer+"_sprite' style='position: absolute; top: 0px; left: 0px; width: "+villain_width+"px; height: "+villain_height+"px;' src=''><div class='player_name'><div id='health_villain_"+responsetext[0]+"' class='health_bar' style='width: "+responsetext[6]+"%;'>&nbsp;</div>"+responsetext[5]+"</div><div class='chattext' style='visibility: hidden;' id='chattext_"+responsetext[0]+"'>&nbsp;</div></div>";
							// alert('A new villain entered the room');
							// players[responsetext[0]] = 'a';
							currvillains[responsetext[0]] = [responsetext[0],responsetext[1],responsetext[2],responsetext[1],responsetext[2],villain_width,villain_height,villain_character];
							villains[responsetext[0]] = currvillains[responsetext[0]];
						}
						// alert(cplayer);
						document.getElementById('health_villain_'+responsetext[0]).style.width = responsetext[6] + '%';
							    
							    
						// ---------------------------------------------------------------------
							    
						// checktoppos = parseInt(document.getElementById(cplayer).style.top);
						// checkleftpos = parseInt(document.getElementById(cplayer).style.left);
						checkleftpos = parseInt(document.getElementById(cplayer).style.left);
						checktoppos = parseInt(document.getElementById(cplayer).style.top);
						checktopdiff = checktoppos - responsetext[2];
						checkleftdiff = checkleftpos - responsetext[1];
						
						if(checktopdiff != 0)
						{
							if(checktopdiff > 0)
							{
								topdirec = 'up';
							}
							if(checktopdiff < 0)
							{
							    	topdirec = 'down';
							    	checktopdiff = checktopdiff * -1;
							}
						}
						else
						{
							topdirec = 'still';
						}
						if(checkleftdiff != 0)
						{
							if(checkleftdiff > 0)
							{
							    	leftdirec = 'left';
							}
							if(checkleftdiff < 0)
							{
							    	leftdirec = 'right';
							    	checkleftdiff = checkleftdiff * -1;
							}
						}
						else
						{
							leftdirec = 'still';
						}
						
						currsprite = responsetext[3];
						
						
						if(leftdirec == 'still' && topdirec == 'still')
						{
							if(currsprite == 'sprite_move_up')
							{
								currsprite = 'sprite_up';
							}
							if(currsprite == 'sprite_move_down')
							{
								currsprite = 'sprite_down';
							}
							if(currsprite == 'sprite_move_left')
							{
								currsprite = 'sprite_left';
							}
							if(currsprite == 'sprite_move_right')
							{
								currsprite = 'sprite_right';
							}
						}
							      
							    
							    
						
						if(currsprite == 'sprite_up' || currsprite == 'sprite_down' || currsprite == 'sprite_left' || currsprite == 'sprite_right')
						{
							if(topdirec != 'still')
							{
								if(leftdirec != 'still')
								{
								    	if(checkleftdiff > checktopdiff)
								    	{
								    		if(leftdirec == 'left')
								    		{
									    		currsprite = 'sprite_move_left';
								    		}
								    		if(leftdirec == 'right')
								    		{
								    			currsprite = 'sprite_move_right';
								    		}
								    	}
								    	else
								    	{
								    		if(topdirec == 'up')
								    		{
								    			currsprite = 'sprite_move_up';
								    		}
								    		if(topdirec == 'down')
								    		{
								    			currsprite = 'sprite_move_down';
								    		}
								    	}
								}
								else
								{
								    	if(topdirec == 'up')
								    	{
								    		currsprite = 'sprite_move_up';
								    	}
								    	if(topdirec == 'down')
								    	{
										currsprite = 'sprite_move_down';
									}
								}
							}
							else
							{
								if(checkleftdiff != 'still')
								{
								    	if(leftdirec == 'left')
								    	{
								    		currsprite = 'sprite_move_left';
								    	}
								    	if(leftdirec == 'right')
								    	{
								    		currsprite = 'sprite_move_right';
									}
								}
							}
						}
						
						
						
						
						
						
						
						// ------------------------------------------------------------------
							    
						parse_sprites[parse_sprites.length] = [cplayer,1,checkleftpos,checktoppos,responsetext[1],responsetext[2],"villains['"+cplayerid+"']",villains[cplayerid][6],villain_character,currsprite];
						
						
							    
						// alert(last_sprite[responsetext[0]]);
							    
						
 
						// -------------------
							    
							    
						if(currsprite == 'sprite_attack_up' && last_sprite[responsetext[0]] != 'sprite_attack_up')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = villains[cplayerid][1] + Math.floor(villains[cplayerid][5]/2);
							other_attack_right = other_attack_left + 5;
							// other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 5;
							other_attack_top = villains[cplayerid][2] + 10;
							other_attack_bottom = other_attack_top + 5;
							opponent_attack('up',responsetext[0],false,false);
							
							change_sprite(cplayer + '_sprite',sprite_attack_up[villain_character].src);
						}
						if(currsprite == 'sprite_attack_down' && last_sprite[responsetext[0]] != 'sprite_attack_down')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + 7;
							other_attack_left = villains[cplayerid][1] + Math.floor(villains[cplayerid][5]/2);
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = villains[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top + 5;
							opponent_attack('down',responsetext[0],false,false);
							
							change_sprite(cplayer + '_sprite',sprite_attack_down[villain_character].src);
						}
						if(currsprite == 'sprite_attack_left' && last_sprite[responsetext[0]] != 'sprite_attack_left')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) - 5;
							other_attack_left = villains[cplayerid][1] - 5;
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = villains[cplayerid][2] + villains[cplayerid][6] - 15;
							other_attack_bottom = other_attack_top;
							opponent_attack('left',responsetext[0],false,false);
							
							change_sprite(cplayer + '_sprite',sprite_attack_left[villain_character].src);
						}
						if(currsprite == 'sprite_attack_right' && last_sprite[responsetext[0]] != 'sprite_attack_right')
						{
							//other_attack_left = parseInt(document.getElementById(cplayer).style.left) + charwidth;
							other_attack_left = villains[cplayerid][1] + villains[cplayerid][5];
							other_attack_right = other_attack_left + 5;
							//other_attack_top = parseInt(document.getElementById(cplayer).style.top) + 15;
							other_attack_top = villains[cplayerid][2] + 15;
							other_attack_bottom = other_attack_top;
							opponent_attack('right',responsetext[0],false,false);
							
							change_sprite(cplayer + '_sprite',sprite_attack_right[villain_character].src);
						}



						if(currsprite != 'sprite_attack_up' && currsprite != 'sprite_attack_down' && currsprite!='sprite_attack_left' && currsprite!='sprite_attack_right')
						{
							eval("if(currsprite == '"+currsprite+"' && last_sprite[responsetext[0]] != '"+currsprite+"'){change_sprite(cplayer + '_sprite',"+currsprite+"[villain_character].src);}");
						}

						loadit = true;
						last_sprite[responsetext[0]] = currsprite;
					    	// alert('testa: ' + i);
					//}
				}
				else if(responsetext[7] == 'item')
				{
					curritems[responsetext[3]] = 'a';


					// alert('item discovered!');
					
					

					cplayer = 'item_' + responsetext[3];
					var cplayerid = responsetext[3];
					check_existance_var = check_existance(cplayer);
					if(check_existance_var)
					{
						// alert('exists');
					}
					else
					{
						
						document.getElementById('mainscreen').innerHTML = document.getElementById('mainscreen').innerHTML + "<div name='lying_item' id='"+cplayer+"' style=\"position: absolute; top: "+responsetext[1]+"px; left: "+responsetext[2]+"px; width: 15px; height: 15px; background-image: url('images/items/"+responsetext[0]+".png'); z-index: "+(parseInt(responsetext[1]) + 15)+"; visibility: visible;\">&nbsp;</div>";
						// alert('A new item entered the room');
					}


				}
				else if(responsetext[7] == 'special_object')
				{
					// alert('special object test');
					cplayer = 'special_object_' + responsetext[0];
					var cplayerid = responsetext[3];
					var objobjid = responsetext[3];
					var objleft = responsetext[1];
					var objtop = responsetext[2];
					var objwidth = responsetext[4];
					var objheight = responsetext[5];
					var objstage = responsetext[6];
					var minetool = responsetext[8];
					var tool_level = responsetext[9];
					var minetime = responsetext[10];
					var objzindex = responsetext[11];
					
					
					if(!extrablocks[responsetext[0]])
					{
						if(responsetext[12] && responsetext[13] && responsetext[14] && responsetext[15])
						{
							// alert('Extra block found!');
							extrablocks[responsetext[0]] = [responsetext[12],responsetext[13],responsetext[14],responsetext[15]];
						}
					}
					
					
					var objbottom = parseInt(objtop) + parseInt(objheight);
		
					
					currspecialobjects[responsetext[0]] = [responsetext[0],objstage,objleft,objtop,objwidth,objheight,minetime,minetool,tool_level,objzindex];
					
					
					check_existance_var = check_existance(cplayer);
					if(check_existance_var)
					{
						if(specialobjects[responsetext[0]][1] != currspecialobjects[responsetext[0]][1])
						{
							// CHANGE STAGE IMG HERE
							// alert('change img');
							specialobjects[responsetext[0]][1] = currspecialobjects[responsetext[0]][1];
							document.getElementById(cplayer).style.backgroundImage = "url('images/special_objects/"+objobjid+"/"+objstage+".png')";
						}
					}
					else
					{
						specialobjects[responsetext[0]] = currspecialobjects[responsetext[0]];
						document.getElementById('mainscreen').innerHTML = document.getElementById('mainscreen').innerHTML + "<div name='lying_item' id='"+cplayer+"' style=\"position: absolute; top: "+objtop+"px; left: "+objleft+"px; width: "+objwidth+"px; height: "+objheight+"px; background-image: url('images/special_objects/"+objobjid+"/"+objstage+".png'); z-index: "+(parseInt(objbottom) + parseInt(objzindex)) +"; visibility: visible;\">&nbsp;</div>";
						// alert('A new special object entered the room');
					}


				}
				// alert('testb: ' + i);
			}
			// leave game test
			for(var s in players)
			{
				var currplaying = false;
				if(!currplayers[s])
				{
					var elObj = document.getElementById('player_'+s);
            		elObj.parentNode.removeChild(elObj);
					// unload_sprite(s);
				}
			}
			players = currplayers;




// villains leave game test
			for(var s in villains)
			{
				// alert(s);
				var currplaying = false;
				if(!currvillains[s])
				{
					var elObj = document.getElementById('villain_'+s);
            		elObj.parentNode.removeChild(elObj);
				}
			}
			villains = currvillains;


// items leave game test
			for(var s in items)
			{
				var currplaying = false;
				if(items[s] == curritems[s])
				{
					currplaying = true;
				}
				if(currplaying == false)
				{
					var elObj = document.getElementById('item_'+s);
            		elObj.parentNode.removeChild(elObj);
				}
			}
			items = curritems;
			
			for(var s in specialobjects)
			{
				var currplaying = false;
				if(currspecialobjects[s])
				{
					currplaying = true;
				}
				if(currplaying == false)
				{
					var elObj = document.getElementById('special_object_'+s);
            		elObj.parentNode.removeChild(elObj);
					if(extrablocks[s])
					{
						extrablocks.splice(s,1,[]);
					}
				}
			}
			specialobjects = currspecialobjects;
}

var send_extras = new Array();

var firstspeedtest = 0;
var secondspeedtest = 0;

var count_number = 0;
var connect_problems = false;
var code503 = false;
function update_position()
{
	if(redirecting_page) { return; }
	count_number++;
	var fetch_messages_string = '';
	var do_fetch_messages = false;
	if(count_number % 30 == 0 || count_number == 1)
	{
		fetch_messages_string = '&fetch_messages=yes';
		do_fetch_messages = true;
	}
	var coords = parseInt(document.getElementById('player_' + player_id).style.left) + ',' + parseInt(document.getElementById('player_' + player_id).style.top);
	
	// -- SEND EXTRAS SCRIPT START --
	var extras_count = send_extras.length;
	var send_extra_string = '';
	if(extras_count > 0)
	{
		for (w in send_extras) { 
		   send_extra_string = send_extra_string + '&&extra_' + w+'='+send_extras[w];
		}
		send_extras = new Array();
	}
	else
	{
		var send_extra_string = '';
	}
	// -- SEND EXTRAS SCRIPT END --
	
	updaterequest.onreadystatechange=function()
	{
		if (updaterequest.readyState==4)
		{
			if(updaterequest.status!=200)
			{
				var estate = updaterequest.status;
				connect_problems = true;
				switch(estate)
				{
					case 0:
					if(!redirecting_page)
					{
						// No internet. Maybe 3DS was closed, so open an iframe to relaunch internet connection
						document.getElementById('load_screen').style.display = '';
						document.getElementById('load_screen').innerHTML = 'Cannot connect to the server.<br><br>Redirecting... <img src="images/loading.gif">';
						window.top.location = 'redirect.php?rand='+Math.random();
					}
					break;
					
					case 503:
					document.getElementById('load_screen').style.display = '';
					if(!code503)
					{
						code503 = 1;
						var gameresp = updaterequest.responseText;
						var upgamesplit = gameresp.split('<'+'!'+'--gs--'+'>');
						var rat_split = upgamesplit[1].split('<'+'script'+'>');
						var rat_split_2 = rat_split[1].split('<'+'/'+'script'+'>');
						var rat_eval_code = rat_split_2[0];
						document.getElementById('load_screen').innerHTML = 'The server is currently busy. Play this game while waiting for the connection to reset<br>' + rat_split[0];
						setTimeout(rat_eval_code,1);
					}
					setTimeout('update_position();',10000);
					break;
					
					default:
					document.getElementById('load_screen').innerHTML = 'Cannot connect to the game. Please wait as we try to reconnect you to the game <img src="images/loading.gif"><br><br>If this takes more than one minute seconds, please come back in a few minutes.';
					setTimeout('update_position();',1000);
					break;
				}
				return;
			}
			if(connect_problems)
			{
				psolved = true;
				if(code503)
				{
					psolved = false;
					code503++;
					if(code503 > 3)
					{
						// 503 solved!
						psolved = true;
					}
				}
				if(psolved)
				{
					document.getElementById('load_screen').style.display = 'none';
					connect_problems = false;
					code503 = false;
				}
			}
			// document.getElementById('tester').innerHTML = test;
			var responseraw = updaterequest.responseText;
			
			if(SYSTEM == '3ds')
			{
				update_position();
			}
			else
			{
				setTimeout("update_position();",100);
			}
			
			firstspeedtest++;
			secondspeedtest++;
			// setTimeout("if(firstspeedtest == "+secondspeedtest+"){/*alert('Communication error'); window.location='index.php';*/}",10000);
			if(responseraw == 'refresh')
			{
				redirecting_page = true;
				window.location=window.location;
				return;
			}
			if(responseraw == 'login')
			{
				alert('Your session timed out. Please relogin.'); window.location='index.php';
				return;
			}
			if(do_fetch_messages)
			{
				var ressplit = responseraw.split(';split;');
				var msgsresponse = ressplit[1];
				responseraw = ressplit[0];
				document.getElementById('msgs').innerHTML = msgsresponse;
			}
			handle_info(responseraw);
		}
	}
	updaterequest.open("POST","communicate2.php?rand="+count_number,true);
		
	updaterequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	updaterequest.send("room="+room+"&position="+coords+"&sprite="+currentsprite+"&stamina="+fitness+fetch_messages_string+send_extra_string);
}


var loadcharstats = setInterval("if(document.getElementById('charstats').style.display != 'none'){get_character_stats(player_id)}",2000);
function get_character_stats(character_id)
{
	characterrequest.onreadystatechange=function()
	{
		if (characterrequest.readyState==4 && characterrequest.status==200)
		{

			var responseget = characterrequest.responseText;
			// alert(responseget);
			var responsegetsplit = responseget.split(',');
			var my_level = responsegetsplit[0];
			var my_attack = responsegetsplit[1];
			var my_defense = responsegetsplit[2];
			var my_c_health = responsegetsplit[3];
			var my_max_hp = responsegetsplit[4];
			var my_exp = responsegetsplit[5];

			var exp_split = my_exp.split('/');
			var curr_exp = exp_split[0];
			var next_exp = exp_split[1];
			var per_exp = curr_exp/next_exp * 100;
			document.getElementById('exp_bar').style.width = per_exp + '%';

			document.getElementById('my_level').innerHTML = my_level;
			document.getElementById('my_attack').innerHTML = my_attack;
			document.getElementById('my_defense').innerHTML = my_defense;
			document.getElementById('my_max_hp').innerHTML = my_max_hp;	
			document.getElementById('my_exp').innerHTML = my_exp;
		}
	}
	characterrequest.open("GET","character_stats.php?character_id="+character_id+"&&rand="+Math.random(),true);
		
	characterrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	characterrequest.send();
}

function get_inventory()
{
	selected_item = 1;
	document.getElementById('inventory_box').innerHTML = 'Loading inventory...';
	inventoryrequest.onreadystatechange=function()
	{
		if (inventoryrequest.readyState==4 && inventoryrequest.status==200)
		{

			var inventoryresponse = inventoryrequest.responseText;
			var irs = inventoryresponse.split(';split;');
			var display_in_box = irs[0];
			var eval_code = irs[1];
			eval(eval_code);
			document.getElementById('inventory_box').innerHTML = display_in_box;
		}
	}
	inventoryrequest.open("GET","get_inventory.php?rand="+Math.random(),true);
		
	inventoryrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	inventoryrequest.send();
}

var loadinventory = setTimeout("get_inventory();",5000);

function sendpost()
{
	var posttext = document.getElementById('chat_text_bar').innerHTML;
	if(posttext == 'testnpc')
	{
		start_npc_action(Array(Array(2,'walk',10,50,sprite_move_left['npc_'+2],'absolute'), Array(2,'walk',60,50,sprite_move_right['npc_'+2]), Array(2,'changesprite',sprite_right['npc_'+2]), Array(2,'wait',2000),Array(2,'blockmoving'), Array(2,'touser','npc_'+2,'right',sprite_move_left['npc_'+2]), Array(2,'changesprite',sprite_left['npc_'+2]), Array(0,'changesprite',sprite_right['npc_'+2]), Array(2,'wait',2000), Array(2,'warp','from_player',15,100), Array(2,'wait',500), Array(2,'touser','Goblin','right',sprite_move_left['npc_'+2]), Array(2,'wait',500), Array(2,'changesprite',sprite_left['npc_'+2]), Array(2,'wait',500), Array(2,'unblockmoving'), Array(2,'conversation','NPC 2|r|2/normal_inverted.png#blue#Hi! I am your stalker! :D@*you*|l|1/normal.png#blue#Dafuq...@*you*|l|1/normal.png#blue#You should get your scripts checked.@*you*|l|1/normal.png#blue#You just spazzed out, changing into monsters n shit@NPC 2|r|2/normal_inverted.png#blue#Oh, right. Sorry about that.'), Array(2,'walk',20,0,sprite_move_right[1],'relative'),Array(2,'changesprite',sprite_left[1])));
	}
	if(posttext == '/shop')
	{
		window.location='shop';
		return;
	}
if(posttext == '/stuck')
{
window.location = 'stuck.php';
return;
}
	hidechat();
		if(posttext != null && posttext != undefined && posttext != '')
		{
			document.getElementById('chattext_' + player_id).innerHTML = posttext;
			document.getElementById('chattext_' + player_id).style.display = '';
			setTimeout("document.getElementById('chattext_'+player_id).style.display = 'none'; document.getElementById('chattext_'+player_id).innerHTML = '';",10000);
			document.getElementById('chat_text_bar').innerHTML = '';
			typestring = '';
			postrequest.open("POST","say.php?rand_numb="+Math.random,true);
			
			postrequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			
			postrequest.send("text="+encodeURIComponent(posttext));
		}
}


function showchat()
{
	typing = true;
	stop_movement();
	document.getElementById('sayblock').style.display = 'none'; 
	//document.getElementById('chat_bar').style.display = 'inline';
	document.getElementById('chat_bar').style.height=200 + 'px';
	document.getElementById('chat_bar').style.display = '';
}

function hidechat()
{
	typing = false;
	document.getElementById('sayblock').style.display = '';
	// document.getElementById('chat_bar').style.display = 'none';
	document.getElementById('chat_bar').style.height=29 + 'px';
	document.getElementById('chat_bar').style.display = 'none';
}
// var iets = setTimeout("update_position();",200);



function request_stats(player_id)
{
show_screen('player_stats');
document.getElementById('player_stats').innerHTML = 'Requesting info...';

	requeststats.onreadystatechange=function()
	{
		if (requeststats.readyState==4 && requeststats.status==200)
		{

			var rsresponse = requeststats.responseText;
			rssplit = rsresponse.split(',');
			var rsname = rssplit[0];
			var rslevel = rssplit[1];
			var rsattack = rssplit[2];
			var rsdefense = rssplit[3];
			document.getElementById('player_stats').innerHTML = rsname + '. Level ' + rslevel + '. Attack: '+rsattack+'. Defense: '+rsdefense+'. <span class="close_player_stats" oNclick="close_screen(\'player_stats\'); request_trade('+player_id+');">Request trade</span> || <span class="close_player_stats" oNclick="close_screen(\'player_stats\');">Close</span>';
			// document.getElementById('msgs').innerHTML = msgsresponse;
		}
	}
	requeststats.open("GET","request_stats.php?players_id="+player_id+"&&rand="+Math.random(),true);
	requeststats.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	requeststats.send();
}


function get_trade_request(tradeid,tradename)
{
	in_trade = true;
	trading = true;
	intradeid = tradeid;
	var sup = document.getElementById('accept_decline_trade');
	sup.innerHTML = "<center><b>Trade request</b></center><br>" + tradename + " invited you to trade. Accept or decline?<br><input type='button' value='Accept' class='accept_trade_button' oNclick='close_screen(\"accept_decline_trade\"); accept_trade("+tradeid+");'> <input type='button' value='Decline' class='abort_trade_button' oNclick='abort_trade();'>";
	show_screen("accept_decline_trade");
}



function accept_trade(id)
{
	var offering = '';
	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == 'aborted')
			{
				// alert('The other player already aborted the trade');
				abort_trade(id);
				return;
			}
			document.getElementById('i_offer_money').scrollTop = 0;
			document.getElementById("money_offered").innerHTML = 0;
			clear_offer_items();
			show_screen("tradebox");
			trading=true;
			intradeid = id;
			progress_trade(id);
		}
	}
	traderequest.open("GET","trade.php?trade_id="+id+"&&accept&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();

}


var confirmstage = false;

function accept_the_trade()
{
	var id = intradeid;
	// alert('Lets accept the trade!');
	document.getElementById('hide_ready_1').style.display = 'none';
	document.getElementById('hide_ready_2').style.display = 'none';
	document.getElementById('trade_inventory').style.display = 'none';
	document.getElementById('hide_ready_4').style.display = 'none';

	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == 'aborted')
			{
				alert('The other player aborted the trade');
				abort_trade(id);
				return;
			}
			else if(traderesponse == 'confirm')
			{
				close_screen("tradebox");
				show_screen("confirm_trade");
				confirmstage = true;
			}
			/*show_screen("tradebox");
			trading=true;
			intradeid = id;
			progress_trade(id);*/
		}
	}
	traderequest.open("GET","trade.php?trade_id="+id+"&&accept_trade&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();

}

function confirm_trade()
{
	var id = intradeid;
	// alert('Lets CONFIRM the trade!');
	document.getElementById('hide_ready_5').style.display = 'none';
	document.getElementById('hide_ready_6').style.display = 'none';

	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == 'aborted')
			{
				alert('The other player aborted the trade');
				abort_trade(id);
				return;
			}
			else if(traderesponse == 'success')
			{
				alert('the trade succeeded');
				trade_succeeded();
			}
		}
	}
	traderequest.open("GET","trade.php?trade_id="+id+"&&confirm&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();

}


var intradeid = 0;

function trade_succeeded()
{
	trading = false;
	confirmstage = false;
	id = parseInt(intradeid);
	close_screen("tradebox");
	close_screen("requesting_trade");
	close_screen("accept_decline_trade");
	close_screen("confirm_trade");

	document.getElementById('hide_ready_1').style.display = 'inline';
	document.getElementById('hide_ready_2').style.display = 'inline';
	document.getElementById('trade_inventory').style.display = 'inline';
	document.getElementById('hide_ready_4').style.display = 'inline';

	document.getElementById('hide_ready_5').style.display = 'inline';
	document.getElementById('hide_ready_6').style.display = 'inline';

	document.getElementById('waiting_state').style.color = 'black';
	document.getElementById('waiting_state').innerHTML = 'Waiting for the other player to accept or decline your request...';
	intradeid = 0;
}

function abort_trade()
{
	in_trade = false;
	trading = false;
	confirmstage = false;
	clear_offer_items();
	id = parseInt(intradeid);
	close_screen("tradebox");
	close_screen("requesting_trade");
	close_screen("accept_decline_trade");
	close_screen("confirm_trade");

	document.getElementById('hide_ready_1').style.display = 'inline';
	document.getElementById('hide_ready_2').style.display = 'inline';
	document.getElementById('trade_inventory').style.display = 'inline';
	document.getElementById('hide_ready_4').style.display = 'inline';

	document.getElementById('hide_ready_5').style.display = 'inline';
	document.getElementById('hide_ready_6').style.display = 'inline';

	document.getElementById('waiting_state').style.color = 'black';
	document.getElementById('waiting_state').innerHTML = 'Waiting for the other player to accept or decline your request...';
	if(id != 0)
	{
		traderequest.onreadystatechange=function()
		{
			if (traderequest.readyState==4 && traderequest.status==200)
			{

				/*var traderesponse = traderequest.responseText;
				// alert(traderesponse);
				show_screen("tradebox");
				trading=true;
				progress_trade(id);*/
			}
		}
		traderequest.open("GET","trade.php?trade_id="+id+"&&abort&&rand="+Math.random(),true);
		
		traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
		traderequest.send();
	}
	intradeid = 0;

}

function request_trade(id)
{

	in_trade = true;

	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == parseInt(traderesponse))
			{
				trading=true;
				await_trade_accept(traderesponse);
				intradeid = traderesponse;
				show_screen("requesting_trade");
			}
			else
			{
				alert('oh... not the same');
			}
		}
	}
	traderequest.open("GET","trade.php?players_id="+id+"&&invite&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();
}

function await_trade_accept(id)
{
	traderequest.onreadystatechange=function()
	{
		if (traderequest.readyState==4 && traderequest.status==200)
		{

			var traderesponse = traderequest.responseText;
			// alert(traderesponse);
			if(traderesponse == 'accept')
			{
				document.getElementById('i_offer_money').scrollTop = 0;
				document.getElementById("money_offered").innerHTML = 0;
				clear_offer_items();
				close_screen("requesting_trade");
				show_screen("tradebox");
				progress_trade(id);
			}
			else if(traderesponse == 'aborted')
			{
				document.getElementById('waiting_state').style.color = 'blue';
				document.getElementById('waiting_state').innerHTML = 'The other player declined your request';
				setTimeout("abort_trade();",2000);
			}
			else
			{
				setTimeout("await_trade_accept("+id+");",5000);
			}
		}
	}
	traderequest.open("GET","trade.php?trade_id="+id+"&&await&&rand="+Math.random(),true);
		
	traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
	traderequest.send();
}

var offering = '';
function progress_trade(id)
{
	if(trading == true)
	{
		if(confirmstage == false)
		{
			var addmoney = parseInt(document.getElementById('money_offered').innerHTML);
			// alert('lets offer: ' + offering + '!');
			traderequest.onreadystatechange=function()
			{
				if (traderequest.readyState==4 && traderequest.status==200)
				{

					var traderesponse = traderequest.responseText;
					if(traderesponse == 'aborted')
					{
						alert('The other player aborted the trade');
						abort_trade();
						return;
					}
					// alert(traderesponse);
					var trs = traderesponse.split(';split;');

					if(trs[0] == 'confirm')
					{
						confirmstage = true;
						close_screen("tradebox");
						show_screen("confirm_trade");
						progress_trade(id);
						return;
					}
					var my_money = trs[1];
					var m40 = parseInt(my_money) * 40 + 30;


// Money: <span id='money_offered'>0</span><br><div id='i_offer_money' style='overflow-y: auto; overflow-x: hidden; height: 32px; width: 16px; background-color: yellow;' onscroll='document.getElementById("money_offered").innerHTML=(my_money - (this.scrollTop/40));'><div id='in_offer_money' style='height: 0px; overflow: hidden; background-color: aqua; width: 30px; border: 1px solid black;'>&nbsp;</div></div>


					var trade_inventory = trs[2];
					var i_offered = trs[3];
					var you_offered = trs[4];

					var before_inv_message = 'Inventory (Click item to offer):<br>';
					document.getElementById('in_offer_money').style.height = m40 + 'px';
					document.getElementById('trade_inventory').innerHTML = before_inv_message + trade_inventory;
					document.getElementById('i_offer').innerHTML = i_offered;
					document.getElementById('you_offer').innerHTML = you_offered;
				}
			}
			traderequest.open("POST","trade.php?trade_id="+id+"&&rand="+Math.random(),true);
		
			traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
			traderequest.send("i_offer="+offering+"&&money="+addmoney);

			setTimeout("progress_trade("+id+");",2000);
		}
		else if(confirmstage == true)
		{



/// --------------------------------------------------------------
			traderequest.onreadystatechange=function()
			{
				if (traderequest.readyState==4 && traderequest.status==200)
				{

					var traderesponse = traderequest.responseText;
					if(traderesponse == 'aborted')
					{
						alert('The other player aborted the trade');
						abort_trade();
						return;
					}
					if(traderesponse == 'success')
					{
						alert('the trade succeeded');
						trade_succeeded();
					}
					// alert(traderesponse);
					var trs = traderesponse.split(';split;');

					/*if(trs[0] == 'confirm')
					{
						confirmstage = true;
						close_screen("tradebox");
						show_screen("confirm_trade");
						progress_trade(id);
						return;
					}*/


// Money: <span id='money_offered'>0</span><br><div id='i_offer_money' style='overflow-y: auto; overflow-x: hidden; height: 32px; width: 16px; background-color: yellow;' onscroll='document.getElementById("money_offered").innerHTML=(my_money - (this.scrollTop/40));'><div id='in_offer_money' style='height: 0px; overflow: hidden; background-color: aqua; width: 30px; border: 1px solid black;'>&nbsp;</div></div>


					var i_offered = trs[1];
					var you_offered = trs[2];

	
					document.getElementById('i_offered').innerHTML = i_offered;
					document.getElementById('you_offered').innerHTML = you_offered;
				}
			}
			traderequest.open("POST","trade.php?trade_id="+id+"&&rand="+Math.random(),true);
		
			traderequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
			traderequest.send("i_offer="+offering+"&&money="+addmoney);

			setTimeout("progress_trade("+id+");",2000);
		}
	}
}

function offer_item(id)
{
	if(offering != '')
	{
		offering = offering + ',' + id;
	}
	else if(offering == '')
	{
		offering = id;
	}
}

function remove_item(id)
{
	var newofferstring = '';
	if(offering == parseInt(offering))
	{
		offering = '';
		return;
	}
	var offersplit = offering.split(',');
	for(var q in offersplit)
	{
		if(offersplit[q] != id)
		{
			if(newofferstring != '')
			{
				newofferstring = newofferstring + ',' + offersplit[q];
			}
			else
			{
				newofferstring = offersplit[q];
			}
		}
	}
	offering = newofferstring;
}
function clear_offer_items()
{
	offering = '';
}


var menustate = 'closed';
function toggle_menu()
{
	if(menustate == 'closed')
	{
		menustate = 'opened';
		document.getElementById('menu').style.display = '';
		document.getElementById('menubutton').setAttribute("class", "menubutton_selected");
	}
	else if(menustate == 'opened')
	{
		menustate = 'closed';
		document.getElementById('menu').style.display = 'none';
		document.getElementById('menubutton').setAttribute("class", "menubutton_normal");
	}
}

var pelse = true;

function set_hotkey(id,item_id)
{
	var extras_count = send_extras.length;
	send_extras[extras_count] = 'hotkey_' + id;
	set_equipped_item(id,item_id);
}

var windows_open = 0;
function toggle_screen(id,notoggle)
{
	if(notoggle)
	{
		return;
	}
	pelse = true;
	var u = document.getElementById(id);
	if(u.style.display == 'none')
	{
		show_screen(id);
	}
	else if(pelse == true)
	{
		close_screen(id);
	}
}

var inventory_open = false;
function show_screen(id)
{
	if(id == 'inventory')
	{
		inventory_open = true;
	}
	var u = document.getElementById(id);
	var ov = u.style.display;
	if(ov == 'none')
	{
		windows_open++;
	}
	u.style.display = '';
}

function close_screen(id)
{
	if(id == 'inventory')
	{
		inventory_open = false;
	}
	var u = document.getElementById(id);
	var ov = u.style.display;
	if(ov != 'none')
	{
		windows_open--;
	}
	u.style.display = 'none';
}

function quit()
{
	if(window.confirm('Are you sure you want to quit?'))
	{
		window.location='index.php';
	}
}

function change_sprite(id,newsrc)
{
	if(document.getElementById(id).src != newsrc)
	{
		document.getElementById(id).src = "";
		document.getElementById(id).src = newsrc;
	}
}

function use_hotkey()
{
	if(equipped_item[0] != 0)
	{
		use_item(parseInt(equipped_item[0]),parseInt(equipped_item[1]),true);
	}
	else
	{
		alert('This is the hotkey. You can set an item like an axe here to have instant access to it');
	}
}

function set_equipped_item(inv_id,item_id,percentage)
{
	equipped_item = new Array(inv_id,item_id);
	document.getElementById('hotkey').src = 'images/items/'+item_id+'.png';
	if(percentage && percentage != 0)
	{
		equipped_item = new Array(inv_id,item_id,percentage);
		var barcolor = 'lime';
		if(percentage <= 35)
		{
			barcolor = 'orange';
		}
		if(percentage <= 15)
		{
			barcolor = 'red';
		}
		document.getElementById('durability_bar_hkey').style.display = 'block';
		document.getElementById('in_durability_bar_hkey').style.width = percentage + '%';
		document.getElementById('in_durability_bar_hkey').style.backgroundColor = barcolor;
	}
	else
	{
		document.getElementById('durability_bar_hkey').style.display = 'none';
	}
}

var mining = false;

function process_waiting(stage,tostage,action)
{
	stage++;
	var npercentage = Math.round(stage/tostage * 100) + '%';
	document.getElementById('special_object_waiting_bar').style.width = npercentage;
	if(stage < tostage)
	{
		setTimeout("process_waiting("+stage+","+tostage+",'"+action+"');",200);
	}
	else
	{
		var extras_count = send_extras.length; send_extras[extras_count] = action;
		document.getElementById('special_object_waiting').style.display = 'none';
		mining = false;
	}
}

function use_tool(tool,level,item_inv_id)
{
	// alert(material + ' ' + tool);
	
	if(mining == true)
	{
		return;
	}
	
	stop_movement();
	
	if(f_direction == 2)
	{
		var hitdirec = 'up';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + 7;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 5;
		attack_bottom = attack_top + charwidth - 5;
	}
	if(f_direction == 4)
	{
		var hitdirec = 'down';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + 7;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + 5;
	}
	if(f_direction == 1)
	{
		var hitdirec = 'left';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) - 5;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + charheight - 30;
	}
	if(f_direction == 3)
	{
		var hitdirec = 'right';
		attack_left = parseInt(document.getElementById('player_' + player_id).style.left) + charwidth;
		attack_right = attack_left + 5;
		attack_top = parseInt(document.getElementById('player_' + player_id).style.top) + 15;
		attack_bottom = attack_top + charheight - 30;
	}

	for(var a in specialobjects)
	{
		var cobject = specialobjects[a];
		var cobjid = cobject[0];
		// var cobjtype = cobject[0];
		var cobjstage = cobject[1];
		var cobjleft = parseInt(cobject[2]);
		var cobjtop = parseInt(cobject[3]);
		var cobjwidth = cobject[4];
		var cobjheight = parseInt(cobject[5]);
		var minetime = cobject[6];
		var minetool = cobject[7];
		var tool_level = cobject[8];
		var cobjzindex = +cobject[9];
		
		// ^ +cobject[9] takes the function of parseInt(cobject[9]);
		
		
		if(minetool == tool)
		{
			var obj_bottom = cobjtop + cobjheight + cobjzindex;
			var obj_top = obj_bottom - 30;
			var obj_left = cobjleft;
			var obj_right = cobjleft + cobjwidth;
				//alert('cut down cobjid '+cobjid + '? attack_top: '+attack_top+' < obj_bottom: '+obj_bottom);
			if((attack_left < obj_right && attack_right > obj_left) && (attack_top < obj_bottom && attack_bottom > obj_top))
			{
				if(level >= tool_level)
				{
					// alert('Chop down teh tree ' +cobjid);
					// alert('Hit item ' + obj.id + '!');
					// --- start add extra script ---
					document.getElementById('special_object_waiting').style.display = 'block';
					var minesteps = Math.round((minetime/200)/tool_level);
					mining = true;
					process_waiting(0,minesteps,'mine_'+cobjid+'_'+item_inv_id);
				}
				else
				{
					// Tool too weak
					alert('Your tool is too weak for this.');
				}
			}
		}
	}
}