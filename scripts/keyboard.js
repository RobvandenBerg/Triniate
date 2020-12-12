var typestring = '';
function type(char)
{
	if(char == 'space')
	{
		char = ' ';
	}
	if(char == 'backspace')
	{   
		var strLen = typestring.length;
		typestring = typestring.slice(0,strLen-1);
	}
	else
	{
		typestring += char;
	}
	document.getElementById('chat_text_bar').innerHTML = typestring;
	document.getElementById('chat_text_bar').scrollLeft = 1000000;
	if(shifted == true)
	{
		shift();
	}
}

// #caps_button

var shifted = false;

function shift()
{
	var shiftbutton = document.getElementById('shift_button_1');
	var shiftbutton2 = document.getElementById('shift_button_2');
	if((caps == false && shifted == false) || (caps == true && shifted == true))
	{

		var els = document.getElementsByName('caps_sensitive');
		for(var i in els)
		{
			if(els[i].innerHTML)
			{
				els[i].innerHTML = strtoupper(els[i].innerHTML);
			}
		}

	}
	else if((caps == true && shifted == false) || (caps == false && shifted == true))
	{
		// alert(strtolower(str));

		var els = document.getElementsByName('caps_sensitive');
		for(var i in els)
		{
			if(els[i].innerHTML)
			{
				// alert(els[i].innerHTML);
				els[i].innerHTML = strtolower(els[i].innerHTML);
			}
		}
	}


	if(shifted == false)
	{
		shifted = true;
		shiftbutton.style.background = 'aqua';
		shiftbutton2.style.background = 'aqua';
	}
	else if(shifted == true)
	{
		shifted = false;
		shiftbutton.style.background = 'white';
		shiftbutton2.style.background = 'white';
	}
}

var caps = false;

function capslock()
{
var capsbutton = document.getElementById('caps_button');
if((caps == false && shifted == false) || (caps == true && shifted == true))
{

var els = document.getElementsByName('caps_sensitive');
for(var i in els)
{
if(els[i].innerHTML)
{
els[i].innerHTML = strtoupper(els[i].innerHTML);
}
}


}
else if((caps == true && shifted == false) || (caps == false && shifted == true))
{
// alert(strtolower(str));

var els = document.getElementsByName('caps_sensitive');
for(var i in els)
{
if(els[i].innerHTML)
{
// alert(els[i].innerHTML);
els[i].innerHTML = strtolower(els[i].innerHTML);
}
}


}


if(caps == false)
{
caps = true;
capsbutton.style.background = 'aqua';
}
else if(caps == true)
{
caps = false;
capsbutton.style.background = 'white';
}

}

// caps_sensitive


function strtolower (str) {

switch(str){

case '!':
return '1';
break;

case '@':
return '2';
break;
case '#':
return '3';
break;
case '$':
return '4';
break;
case '%':
return '5';
break;
case '^':
return '6';
break;
case '&amp;':
return '7';
break;
case '*':
return '8';
break;
case '(':
return '9';
break;
case ')':
return '0';
break;

case '_':
return '-';
break;

case '+':
return '=';
break;

case '{':
return '[';
break;

case '}':
return ']';
break;

case ':':
return ';';
break;

case '"':
return "'";
break;

case '&lt;':
return ',';
break;

case '&gt;':
return '.';
break;

case '?':
return '/';
break;

case '~':
return '`';
break;

case '|':
return '\\';
break;

case 'space':
return 'space';
break;
}


    return (str + '').toLowerCase();}

function strtoupper (str) {

switch(str){

case '1':
return '!';
break;

case '2':
return '@';
break;
case '3':
return '#';
break;
case '4':
return '$';
break;
case '5':
return '%';
break;
case '6':
return '^';
break;
case '7':
return '&amp;';
break;
case '8':
return '*';
break;
case '9':
return '(';
break;
case '0':
return ')';
break;

case '-':
return '_';
break;

case '=':
return '+';
break;

case '[':
return '{';
break;

case ']':
return '}';
break;

case ';':
return ':';
break;

case "'":
return '"';
break;

case ',':
return '&lt;';
break;

case '.':
return '&gt;';
break;

case '/':
return '?';
break;

case '`':
return '~';
break;

case '\\':
return '|';
break;

case 'space':
return 'space';
break;
}

    return (str + '').toUpperCase();}

var typing = false;