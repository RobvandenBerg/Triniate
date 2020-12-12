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
	if(cobjtype == 'npc')
	{
		document.write("<div name='object' id='object_"+cobjid+"' style='position: absolute; top: "+cobjtop+"px; left: "+cobjleft+"px; width: "+cobjwidth+"px; height: "+cobjheight+"px; background-image: url(); z-index: "+(parseInt(cobjtop) + parseInt(cobjheight))+";' oNclick='alert(\"This is "+cobjname+"\");'><img src=\""+sprite_down['npc_'+cobjid].src+"\" style='width: 20px; height: 30px; position: absolute; top: 0px; left: 0px;' id='object_"+cobjid+"_sprite'><div class='player_name' style='visibility: visible;'><div id='health_npc_"+cobjid+"' class='health_bar' style='width: 100%;'>&nbsp;</div>"+cobjname+"</div><div class='chattext' style='display: none;' id='chattext_villain_"+cobjid+"'>&nbsp;</div>&nbsp;</div>");
	}
}