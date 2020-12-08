<?php

function eregi($input, $check)
{
	return strpos(strtolower($check), strtolower($input));
}

function detect_system()
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	if(eregi('Nintendo 3DS', $_SERVER['HTTP_USER_AGENT']))
	{
		return '3ds';
	}
	else if(eregi('mobile', $_SERVER['HTTP_USER_AGENT']))
	{
		return '3ds';
	}
	else
	{
		return 'wiiu';
	}
	if(true)
	{
		
	}
	elseif(eregi('Nintendo DSi', $_SERVER['HTTP_USER_AGENT']))
	{
		return 'dsi';
	}
	elseif(eregi('Nintendo WiiU', $_SERVER['HTTP_USER_AGENT']))
	{
		return 'wiiu';
	}
	else
	{
		return 'pc';
	}
}
?>