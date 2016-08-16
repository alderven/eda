<?php
require_once "header.php";
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
$count = 0;
$sql = "SELECT Count FROM $table_orders WHERE UserId = $user_id AND MenuItemId = $dish_id";
$result = $conn->query($sql);

// 5. Increment Count if such Order already exist.
if ($result->num_rows > 0)
{
	while($row = $result->fetch_assoc()) {
		$count = $row["Count"];
	}
	
	$sql = "UPDATE $table_orders SET Count = " . ++$count . " WHERE UserId = $user_id AND MenuItemId = $dish_id";
	$result = $conn->query($sql);
}
else
{
// 6. Create new Order item if Order not exist.
$count = 1;
$sql = "INSERT INTO $table_orders (UserId, MenuItemId, Count) VALUES ($user_id, $dish_id, $count)";
$result = $conn->query($sql);
}

// Return Count.
print $count;

$conn->close();
?>