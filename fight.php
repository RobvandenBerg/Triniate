<?php

function hp_upgrade($hp,$level,$strongness)
{
if(($hp - $level * 3 - 47) < 53)
{
// HP kan erbij
$trainedhp = $hp - $level * 3 - 47;
$newhp = 53 - $trainedhp;
if($newhp < $strongness)
{
$outputhp = 100 + $level * 3;
}
else
{
$outputhp = $hp + $strongness;
}
return($outputhp);
}
else
{
// HP kan er niet bij
return($hp);
}
}

function att($att,$def,$strength=10)
{
if($att >= $def)
{
$hp = (($att-$def)/500 + 1) * ((5*$att+1940)/199);
$hp = $hp / 10 * $strength;
return(ceil($hp));
}
else
{
$hp = (1/(abs($att-$def)/500 + 1)) * ((5*$att+1940)/199);
$hp = $hp / 10 * $strength;
return(floor($hp));
}
}

echo "HP damage: " . att(1000,500,10);

echo "<hr>";

echo "Returned HP: " . hp_upgrade(50,1,0);
?>