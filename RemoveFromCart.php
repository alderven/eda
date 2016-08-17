<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

// 1. Get Dish Id.
$dish_id = isset($_GET['dishId']) ? $_GET['dishId'] : '';
$_SESSION['dish_id'] = $dish_id;

// 4. Check whether MenuItemId already exist.
$sql = "SELECT Count FROM $table_orders WHERE UserId = $user_id AND MenuItemId = $dish_id";
$result = $conn->query($sql);

// 5. Check if Order already exist.
$count = 0;
if ($result->num_rows > 0)
{
	while($row = $result->fetch_assoc()) {
		$count = $row["Count"];
	}
	
	// 6. Delete Order entity if its Count was equal to 1.
	if ($count == 1)
	{
		$count = 0;
		$sql = "DELETE FROM $table_orders WHERE MenuItemId = " . $dish_id . " AND UserId = " . $user_id;
		$result = $conn->query($sql);
	}
	else
	{
	// 7. Decrement Order Count if its Count was above 1.
	$count--;
	$sql = "UPDATE $table_orders SET Count = " . $count . " WHERE UserId = $user_id AND MenuItemId = $dish_id";
	$result = $conn->query($sql);
	}
}
else
{
// Do nothing if such order not exist.
}

$conn->close();

// Return Count.
print $count;
?>