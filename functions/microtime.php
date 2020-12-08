<?php

function micro_time()
{
$microtime1 = explode(' ',microtime());
$microtime2 = explode('.',$microtime1[0]);
$microtime = $microtime1[1] . '.' . $microtime2[1];
return($microtime);
}

?>