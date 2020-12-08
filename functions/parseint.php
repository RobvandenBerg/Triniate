<?php
function parseInt($string) {
return preg_replace('/\D/', '', $string);
}

?>