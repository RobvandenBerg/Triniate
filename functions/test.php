<?php
include("../include_this.php");

// function check_has_item($player,$item,$mysql_active = true)

$number = check_has_item(2,1,false);

echo "player 2 has $number apples.<br><br>";

$status = check_quest_status(2,1,false);

echo "The quest status of quest 1 for player 2 is $status";

?>