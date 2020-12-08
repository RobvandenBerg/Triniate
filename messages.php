<?php



$output = '';
$select_messages_request = mysql_query("SELECT c.type,c.receiver,c.sender,c.message,p.name from chat as c, position as p where c.room='$room' and (c.receiver='everybody' or c.receiver='$player_id') and p.id=c.sender order by c.id DESC LIMIT 0,10") or die(mysql_error());
$total_messages = mysql_num_rows($select_messages_request);
for($m = 0; $m < $total_messages; $m++)
{
	$select_messages_row = mysql_fetch_row($select_messages_request);
	$type = $select_messages_row[0];
	$receiver = $select_messages_row[1];
	$sender = $select_messages_row[2];
	$message = $select_messages_row[3];
	$name = $select_messages_row[4] . ": ";
	
	$color = 'yellow';
	if($type == 'notice')
	{
		$color = 'purple';
	}
	if($sender == '0')
	{
		$name = '';
	}
	$output .= "<font color='$color'>$name$message</font><br>";
}


echo $output;

?>