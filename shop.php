<?php
include("include_this.php");

light_login();

?>
<html>
<head>
<meta name='viewport' content='width=320'>
<title>
Test shop
</title>
<style>
body
{
padding: 0px;
margin: 0px;
color: white;
font-size: 10px;
}
.container
{
background-image: url(images/backgrounds/shop_background.png);
background-repeat:no-repeat;
width: 320px;
height: 212px;
overflow-x: hidden;
overflow-y: auto;
}
.sellbox
{
padding-left: 5px;
padding-right: 5px;
margin-bottom: 5px;
width: 280px;
height: 70px;
overflow: auto;
border: 2px solid black;
background-color: white;
opacity: 0.8;
text-align: left;
color: black;
}
.buybox
{
padding-left: 5px;
padding-right: 5px;
width: 280px;
height: 50px;
overflow: auto;
border: 2px solid black;
background-color: white;
opacity: 0.8;
text-align: left;
color: black;
}

.exit
{
color: lime;
}
</style>
</head>
<body><div class='container'>
<?php

$mapsize_height = 1000;
$mapsize_width = 1000;

$buyable = array(array(1,'Tasty apple'),array(5,'Red potion small'),array(7,'Blue potion small'),array(9,'Green potion small'),array(17,'Wooden axe'),array(18,'Stone axe'));


mysql_connect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());

if(isset($_GET['sell']) && is_numeric($_GET['sell']))
{
	$inventory_id = $_GET['sell'];
	$get_price_request = mysql_query("SELECT it.price, it.name, it.quest_item from items as it, inventories as inv where inv.id='$inventory_id' and it.id=inv.item_id") or die(mysql_error());
	if(mysql_num_rows($get_price_request) == 1)
	{
		$delete_item_request = mysql_query("DELETE from inventories where id='$inventory_id'") or die(mysql_error());
		$r = mysql_fetch_row($get_price_request);
		$price = ceil($r[0] / 2);
		$name = $r[1];
		$quest_item = $r[2];
		if($quest_item == 'no')
		{
			echo "<b>You sold item $name for $price coins.</b><br>";

			$get_money_request = mysql_query("UPDATE position SET money = (money + '$price') WHERE id = '$player_id' ") or die(mysql_error());
		}
	}
}

if(isset($_GET['buy']) && is_numeric($_GET['buy']))
{
	$inventory_id = $_GET['buy'];
	$do_buy = false;
	foreach($buyable as $buy_array)
	{
		if($inventory_id == $buy_array[0])
		{
			$do_buy = true;
		}
	}
	if($do_buy == true)
	{
	$get_price_request = mysql_query("SELECT price, name from items where id='$inventory_id'") or die(mysql_error());
	if(mysql_num_rows($get_price_request) == 1)
	{
		$r = mysql_fetch_row($get_price_request);
		$price = $r[0];
		$name = $r[1];

		$get_mon_req = mysql_query("SELECT money from position where id='$player_id'") or die(mysql_error());
		$grr = mysql_fetch_row($get_mon_req);
		$money = $grr[0];

		if($money >= $price)
		{

			echo "<b>You bought item $name for $price coins.</b><br>";

			$get_money_request = mysql_query("UPDATE position SET money = (money - '$price') WHERE id = '$player_id' ") or die(mysql_error());
			$get_item_request = mysql_query("INSERT into inventories (item_id,belongs_to) VALUES ('$inventory_id','$player_id')") or die(mysql_error());
		}
		else
		{
			echo "<b>You don't have enough money to buy item $name</b><br>";
		}
	}
	}
}

$select_money_request = mysql_query("SELECT money from position where id='$player_id'") or die(mysql_error());
$select_money_row = mysql_fetch_row($select_money_request);
$money = $select_money_row[0];

echo "<center>You have $money coins <img src='images/items/3.png'></center>";

echo "<center><div class='sellbox'>Sell items:<br>";

$select_inventory_request = mysql_query("SELECT inv.id, it.name, it.id, it.quest_item from inventories as inv, items as it where it.id=inv.item_id and inv.belongs_to='$player_id'") or die(mysql_error());
$total_items = mysql_num_rows($select_inventory_request);
for($m = 0; $m < $total_items; $m++)
{
	$item_row = mysql_fetch_row($select_inventory_request);
	$inventory_id = $item_row[0];
	$item_name = $item_row[1];
	$item_id = $item_row[2];
	$quest_item = $item_row[3];
	if($quest_item == 'no')
	{
		echo "<a href=\"$_SERVER[PHP_SELF]?sell=$inventory_id\">";
	}
	else
	{
		echo '<a href="#'.$item_id.'" onClick="alert(\'You can not sell this item\');">';
	}
	echo "$inventory_id: <img src='images/items/$item_id.png'> <b>$item_name</b></a><br>\n";
}
if($total_items == 0)
{
	echo "Your inventory is empty.";
}

echo "</div><div class='buybox'>Buy items:</b><br>";
foreach($buyable as $buy_array)
{
	echo "<a href=\"$_SERVER[PHP_SELF]?buy=$buy_array[0]\"><img src='images/items/$buy_array[0].png'> $buy_array[1]</a><br>";
}
// echo "<a href=\"$_SERVER[PHP_SELF]?buy=1\">Apple</a><br><a href=\"$_SERVER[PHP_SELF]?buy=5\">Red potion small</a><br><a href=\"$_SERVER[PHP_SELF]?buy=6\">Red potion big</a><br><a href=\"$_SERVER[PHP_SELF]?buy=17\">Wooden axe</a><br><a href=\"$_SERVER[PHP_SELF]?buy=18\">Stone axe</a>";
echo "</div></center>";
mysql_close();




echo "<center><a href='redirect.php' class='exit'>Back to game</a></center>";
?>

</div></body>
</html>