<?php
function get_position($to,$from,$speed,$margin=0)
{
	$tosplit = explode(',',$to);
	$toleft = $tosplit[0];
	$totop = $tosplit[1];
	$fromsplit = explode(',',$from);
	$fromleft = $fromsplit[0];
	$fromtop = $fromsplit[1];

	$leftdiffr = $toleft - $fromleft;
	$leftdiff = abs($toleft - $fromleft);
	$topdiffr = $totop - $fromtop;
	$topdiff = abs($totop - $fromtop);


	$diagonal_distance = get_diagonal($fromleft,$fromtop,$toleft,$totop);

	if($diagonal_distance <= $margin)
	{
		if($leftdiff >= $topdiff)
		{
			if($leftdiffr > 0)
			{
				$sprite = 'sprite_right';
			}
			else
			{
				$sprite = 'sprite_left';
			}
		}
		else
		{
			if($topdiffr > 0)
			{
				$sprite = 'sprite_down';
			}
			else
			{
				$sprite = 'sprite_up';
			}
		}
		return(array($sprite));
	}

	if($leftdiff <= $speed && $topdiff <= $speed)
	{
		if($leftdiff >= $topdiff)
		{
			if($leftdiffr > 0)
			{
				$sprite = 'sprite_move_right';
			}
			else
			{
				$sprite = 'sprite_move_left';
			}
		}
		else
		{
			if($topdiffr > 0)
			{
				$sprite = 'sprite_move_down';
			}
			else
			{
				$sprite = 'sprite_move_up';
			}
		}
		return(array($toleft,$totop,$sprite));
	}

	if($leftdiff >= $speed)
	{
		if($leftdiffr > 0)
		{
			$newleft = $fromleft + $speed;
			$sprite = 'sprite_move_right';
		}
		else
		{
			$newleft = $fromleft - $speed;
			$sprite = 'sprite_move_left';
		}
		if($leftdiff < $speed)
		{
			$newleft = $toleft;
		}
		// $newtop = $fromtop;
	}
else
{
$newleft = $fromleft;
}
	if($topdiff >= $speed)
	{
		if($topdiffr > 0)
		{
			$newtop = $fromtop + $speed;
			$sprite = 'sprite_move_down';
		}
		else
		{
			$newtop = $fromtop - $speed;
			$sprite = 'sprite_move_up';
		}
		if($topdiff < $speed)
		{
			$newtop = $totop;
		}
		// $newleft = $fromleft;
	}
else
{
$newtop = $fromtop;
}

if($leftdiff >= $topdiff)
		{
			if($leftdiffr > 0)
			{
				$sprite = 'sprite_move_right';
			}
			else
			{
				$sprite = 'sprite_move_left';
			}
		}
		else
		{
			if($topdiffr > 0)
			{
				$sprite = 'sprite_move_down';
			}
			else
			{
				$sprite = 'sprite_move_up';
			}
		}

	return(array($newleft,$newtop,$sprite));

}

function get_diagonal($x1,$y1,$x2,$y2)
{
$out = sqrt(pow(abs($x1 - $x2),2) + pow(abs($y1 - $y2),2));
return($out);
}
?>