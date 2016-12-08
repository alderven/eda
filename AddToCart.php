<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

# 1. Get Dish Id
$dish_id = isset($_GET['dishId']) ? $_GET['dishId'] : '';
$_SESSION['dish_id'] = $dish_id;

# 2. Check whether MenuItemId already exist
$count = 0;
$sql = "SELECT Count FROM $table_orders WHERE UserId = $user_id AND MenuItemId = $dish_id";
$result = $conn->query($sql);

# 3. Increment Count if such Order already exist
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
# 4. Create new Order item if Order not exist
$count = 1;
$sql = "INSERT INTO $table_orders (UserId, MenuItemId, Count) VALUES ($user_id, $dish_id, $count)";
$result = $conn->query($sql);
}

# 5. Return count
print $count;

$conn->close();
?>