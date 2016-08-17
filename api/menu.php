<?php

# 1. Auth in Service
$user_id = Null;
include 'auth.php';

# 2. Get Menu from DB
/*
$sql = "SELECT Id, Date, Type, Name, Weight, Price, Contain, Company FROM $table_food
		LEFT OUTER JOIN $table_orders ON $table_food.Id=$table_orders.MenuItemId
		WHERE $table_orders.UserId = $user_id
		AND Date > '" . date("Y-m-d") . "'
		ORDER BY Date";
*/

$sql = "SELECT Date, Name FROM $table_food
		LEFT OUTER JOIN $table_orders ON $table_food.Id=$table_orders.MenuItemId
		WHERE $table_orders.UserId = $user_id
		AND Date >= CURDATE()
		ORDER BY Date";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$rows = array();
	 while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
}
# 3. Print result as JSON.
print json_encode($rows, JSON_UNESCAPED_UNICODE);

# 4. Close DB connection.
$conn->close();
?>