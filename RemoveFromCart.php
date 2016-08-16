<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}

// 1. Get Dish Id.
$dish_id = isset($_GET['dishId']) ? $_GET['dishId'] : '';
$_SESSION['dish_id'] = $dish_id;

// 2. Configure DB connection.
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);

// 3. Get UserId.
$sql = "SELECT Id FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
        $user_id = $row["Id"];
    }

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