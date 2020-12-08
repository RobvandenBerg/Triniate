<?php

$p1 = '900150983cd24fb0d6963f7d28e17f72';

//$ph = password_hash($p1, PASSWORD_DEFAULT);
$ph = '$2y$10$XssWOTi8d3Gq//3pfbCeXeXtn/HzsCYZObBTCqmWFSZAfkwsyElUS';

var_dump(password_verify($p1, $ph));

?>