<?php
$mapid = $_GET['id'];
if(round($mapid) != $mapid or !is_numeric($mapid) or !file_exists('../rooms/'.$mapid))
{
	exit('Invalid Map ID. <a href="./">Back</a>');
}

$mapdir = '../rooms/'.$mapid;
$background_file_contents = file_get_contents($mapdir . '/background.txt');
$backgroundsplit = explode(',',$background_file_contents);
$columns = ceil($backgroundsplit[0]/20) + 1;
$rows = ceil($backgroundsplit[1]/20) + 1;
?>
<html>
<head>
<title>
Triniate - Map maker
</title>
<style>
td
{
width: 20px;
height: 20px;
padding: 0px;
margin: 0px;
}

.tile_normal
{
border: 1px solid black;
}

.tile_selected
{
border: 2px solid red;
}

.d
{
	background-color: gray;
	color: red;
	font-size: 10px;
	padding: 1px;
}

.a
{
	background-color: aqua;
	color: blue;
	font-size: 10px;
	padding: 1px;
}

.notile
{
	background-image: url(images/notile.png);
}
</style>

<script>

var columns = <?php echo ($columns - 1);?>;
var rows = <?php echo ($rows - 1);?>;
var layer = 1;

function set_layer(n)
{
	layer = n;
	if(layer == 1)
	{
		var olayer = 2;
	}
	else if(true)
	{
		var olayer = 1;
	}
	document.getElementById('layer_button_'+layer).style.backgroundColor = 'yellow';
	document.getElementById('layer_button_'+olayer).style.backgroundColor = 'gray';
	
}


var current_tile = 'tiles/blank.png';
var cbackground = 'tiles/blank.png';
function switch_tile(id)
{
var cobj = document.getElementById('tile_'+id);
var pobj = document.getElementById('tile_'+current_tile);
pobj.className = 'tile_normal';
cobj.className = 'tile_selected';
current_tile = id;
cbackground = cobj.src;
}

function set_tile(tleft,ttop,ulayer,utile)
{
	var use_layer = layer;
	var use_current_tile = current_tile;
	var use_cbackground = cbackground;
	if(ulayer)
	{
		use_layer = ulayer;
	}
	if(utile)
	{
		use_current_tile = utile;
		use_cbackground = utile;
	}
	var adder = '';
	if(use_layer == 2)
	{
		adder = '_2';
	}
	var cspot = document.getElementById('spot_'+tleft+'_'+ttop+adder);
	if(use_layer == 2)
	{
		cspot.src=use_cbackground;
	}
	else
	{
		cspot.style.backgroundImage="url("+use_cbackground+")";
	}

	var sendobj = document.getElementById('sendstring');


	var sendvaluesplit = sendobj.value.split(';');
	var already_in = false;
	var new_string = '';
	var al_set = false;
	for(var a in sendvaluesplit)
	{
		var craw = sendvaluesplit[a];
		var crawsplit = craw.split(':');
		var crawsplit2 = crawsplit[0].split(',');
		if(al_set == true)
		{
			new_string += ';';
		}
		al_set = true;
		if(crawsplit2[0] == tleft && crawsplit2[1] == ttop && crawsplit2[2] == use_layer)
		{
			// alert('Already in! overwrite!');
			already_in = true;
			new_string += tleft + ',' + ttop + ',' + use_layer + ':' + use_current_tile;
		}
		else
		{
			new_string += craw;
		}
	}

	if(already_in == false)
	{
		if(new_string != '')
		{
			new_string += ';';
		}
		new_string += tleft + ',' + ttop + ',' + use_layer + ':' + use_current_tile;
	}

	sendobj.value = new_string;

}


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

function KeyDownCheck(button)
{
	if(button == 13)
	{
		if(confirm('fill everything with ' + current_tile + '?'))
		{
			for(var m = 1; m <= rows; m++)
			{
				for(var n = 1; n <= columns; n++)
				{
					set_tile(n,m);
				}
			}
		}

	}
}

function getImageHeight(myImage) {
	var y, obj;
	if (document.layers) {
		var img = getImage(myImage);
		return img.height;
	} else {
		return getElementHeight(myImage);
	}
	return -1;
}

function bgchange(path)
{
	var newImg = new Image();
	newImg.src = path;
	var imh = Math.round(newImg.height * 1.1);
	var imw = Math.round(newImg.width * 1.1);
	document.getElementById('construct_table').style.backgroundImage = 'url('+path+')';
	document.getElementById('construct_table').style.backgroundSize= imw + 'px '+imh+'px';
}

function done()
{
	toggle_element('finish_button');
	toggle_element('submit_div');
}

function toggle_element(id)
{
if(document.getElementById(id).style.display=='none')
{
document.getElementById(id).style.display='inline';
}
else if(document.getElementById(id).style.display=='inline')
{
document.getElementById(id).style.display='none';
}
}

function delcol()
{
	if(columns < 2)
	{
		return;
	}
	document.getElementById('delcol_'+columns).innerHTML = '<input class="a" type="button" value="+" onClick="addcol();">';
	document.getElementById('delcol_'+(columns - 1)).innerHTML = '<input class="d" type="button" value="X" onClick="delcol();">';
	delete_element('delcol_'+(columns + 1));
	delete_element('deadcol_'+(columns + 1));
	for(var i = 1; i <= rows; i++)
	{
		delete_element('spot_'+columns+'_'+i);
	}
	columns--;
	
	document.getElementById('columns').value = columns;
}

function delrow()
{
	if(rows < 2)
	{
		return;
	}
	document.getElementById('spot_0_'+(rows - 1)).innerHTML = '<input class="d" type="button" value="X" onClick="delrow();">';
	delete_element('row_'+rows);
	rows--;
	
	document.getElementById('rows').value = rows;
}


function addrow()
{
	document.getElementById('spot_0_'+rows).innerHTML = '';
	rows++;
	var newRow = document.getElementById('construct_table').insertRow(rows);
	newRow.id = 'row_'+rows;
	var newCell = newRow.insertCell(0);
	newCell.id = 'spot_0_'+rows;
	newCell.innerHTML = '<input class="d" type="button" value="X" onClick="delrow();">';
	newCell.className = 'notile';
	for(var i = 1; i <= columns; i++)
	{
		var newCell = newRow.insertCell(i);
		newCell.id = 'spot_'+i+'_'+rows;
		newCell.innerHTML = "<img id='spot_"+i+"_"+rows+"_2' style='width: 20px; height: 20px;' src='tiles/blank.png' class='layer2' oNclick='set_tile("+i+","+rows+");'>";
		set_tile(i,rows,1,'tiles/blank.png');
		set_tile(i,rows,2,'tiles/blank.png');
	}
	var newCell = newRow.insertCell(columns+1);
	newCell.id = 'deadrow_'+rows;
	newCell.className = 'notile';
	
	document.getElementById('rows').value = rows;
}

function addcol()
{
	document.getElementById('delcol_'+columns).innerHTML = '';
	columns++;
	document.getElementById('delcol_'+columns).id = 'delcol_'+(columns+1);
	var newCell = document.getElementById('upperrow').insertCell(columns);
	newCell.innerHTML = '<input class="d" type="button" value="X" onClick="delcol();">';
	newCell.id = 'delcol_'+columns;
	newCell.className = 'notile';
	for(var i = 1; i <= rows; i++)
	{
		var newCell = document.getElementById('row_'+i).insertCell(columns);
		newCell.id = 'spot_'+columns+'_'+i;
		newCell.innerHTML = "<img id='spot_"+columns+"_"+i+"_2' style='width: 20px; height: 20px;' src='tiles/blank.png' class='layer2' oNclick='set_tile("+columns+","+i+");'>";
		set_tile(columns,i,1,'tiles/blank.png');
		set_tile(columns,i,2,'tiles/blank.png');
	}
	var newCell = document.getElementById('deadrow').insertCell(columns + 1);
	newCell.id = 'deadcol_' + (columns + 1);
	newCell.className = 'notile';
	
	document.getElementById('columns').value = columns;
	
}

function delete_element(id)
{
	var el = document.getElementById(id);
	el.parentNode.removeChild(el);
}
</script>
</head>
<body>
<div id='screen'>
<form action='createmap.php?id=<?php echo $mapid;?>&rows=<?php echo ($rows - 1);?>&columns=<?php echo ($columns - 1);?>' method='POST'>
Background image: <input type='text' name='bgimg' value='' onKeyDown='bgchange(this.value);' onChange='bgchange(this.value);'><br>
<input type='button' id='layer_button_1' value='Layer 1' style='background-color: yellow;' onClick='set_layer(1);'>
<input type='button' id='layer_button_2' value='Layer 2' style='background-color: gray;' onClick='set_layer(2);'><br>
<img id='tile_tiles/blank.png' src='tiles/blank.png' oNclick="switch_tile('tiles/blank.png');" class='tile_selected'><br>

<?php



$directory = "tiles";

$iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory), 
            RecursiveIteratorIterator::SELF_FIRST);

foreach($iterator as $file) {
    if($file->isDir()) {
        //echo $file, PHP_EOL;
	$directory = $file;
//echo $directory;

$tiles = array();

$filePath = $directory;
$dir = opendir($filePath); # Open the path
while ($file = readdir($dir)) { 
  if (strpos($file, '.png') or strpos($file, '.gif')) { # Look at only files with a .php extension
    $tiles[count($tiles)] = "$directory/$file";
    $fileCount++;
  }
}
if ($fileCount > 0) {
  //echo sprintf("<strong>List of Files in %s</strong><br />%s<strong>Total Files: %s</strong>",$filePath,$string,$fileCount);
}

foreach($tiles as $tile)
{
//echo "<a href='$tile'><img src='$tile'> $tile</img></a><br>\n";
echo "<img id='tile_$tile' src='$tile' oNclick=\"switch_tile('$tile');\" class='tile_normal'>";
}












echo "<br>";



    }
}


?>
<table id='construct_table' border=1 cellspacing="0" cellpadding="0" style='background-image: url();'>
<?php
echo '<tr id="upperrow">';
for($n = 0; $n <= $columns; $n++)
{
	echo '<td class="notile" id="delcol_'.$n.'">';
	if($n == ($columns - 1))
	{
		echo '<input class="d" type="button" value="X" onClick="delcol();">';
	}
	if($n == $columns)
	{
		echo '<input class="a" type="button" value="+" onClick="addcol();">';
	}
	echo '</td>';
}
echo '</tr>' . "\n";
for($m = 1; $m <= $rows; $m++)
{
	if($m == $rows)
	{
		echo '<tr id="deadrow"><td class="notile">';
	}
	else
	{
		echo '<tr id="row_'.$m.'"><td class="notile" id="spot_0_'.$m.'">';
	}
	if($m == ($rows - 1))
	{
		echo '<input class="d" type="button" value="X" onClick="delrow();">';
	}
	if($m == $rows)
	{
		echo '<input class="a" type="button" value="+" onClick="addrow();">';
		echo '</td>';
		for($n = 1; $n <= $columns; $n++)
		{
			echo '<td id="deadcol_'.$n.'" class="notile"></td>';
		}
	}
	else
	{
		echo '</td>';
		for($n = 1; $n <= $columns; $n++)
		{
			if($n == $columns)
			{
				echo '<td id="deadrow_'.$m.'" class="notile"></td>';
			}
			else
			{
				echo "<td id='spot_".$n."_".$m."'><img id='spot_".$n."_".$m."_2' style='width: 20px; height: 20px;' src='tiles/blank.png' class='layer2' oNclick='set_tile($n,$m);'></td>";
			}
		}
		echo '</tr>' . "\n";
	}
}
?>
</table>
<input type='button' value='Done' oNclick='done();' id='finish_button' style='display: inline;'>
<div id='submit_div' style='display: none;'>
<input type='text' id='sendstring' name='sendstring' value=''><br>
<input type='text' id='columns' name='columns' value='<?php echo ($columns - 1);?>'><br>
<input type='text' id='rows' name='rows' value='<?php echo ($rows - 1);?>'><br>
Are you sure you want to finish this step?<br>
<input type='button' oNclick="toggle_element('finish_button'); toggle_element('submit_div');" value='No'> <input type='submit' value='Yes, finish step'>

</div>
</form>


</div>
</body>
</html>