<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

# 1. Find User's dishes to delete
$sql = "SELECT MenuItemId FROM $table_orders
		LEFT OUTER JOIN $table_food ON $table_food.Id=$table_orders.MenuItemId
		WHERE $table_food.Date >= CURDATE() AND $table_orders.UserId = $user_id";
$result = $conn->query($sql);
if ($result->num_rows > 0)	{
	$ordersToDelete = [];
	while ($row = $result->fetch_assoc()) {
	$ordersToDelete[] = $row['MenuItemId'];
	}
	
	# 2. Delete dishes
	$sql = "DELETE FROM $table_orders WHERE MenuItemId IN (" . implode(', ', $ordersToDelete) . ") AND UserId = $user_id";
	$result = $conn->query($sql);
}
?>