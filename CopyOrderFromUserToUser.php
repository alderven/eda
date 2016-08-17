<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

# 1. Get all days starting from tomorrow.
$sql = "SELECT DISTINCT Date FROM $table_food WHERE Date > CURDATE()";
$result = $conn->query($sql);
$all_dates = array();
while($row = $result->fetch_assoc()) {
	array_push($all_dates, $row["Date"]);
}

# 2. Filter dates which are empty for current user.
$empty_dates = array();
foreach ($all_dates as &$date) {
	$sql = "SELECT MenuItemId FROM $table_orders
			LEFT OUTER JOIN $table_food ON $table_food.Id=$table_orders.MenuItemId
			LEFT OUTER JOIN $table_users ON $table_orders.UserId=$table_users.Id
			WHERE $table_food.Date = '$date' AND $table_orders.UserId = $user_id";
	$result = $conn->query($sql);
	//print $sql;
	//print 'If result empty: "' . empty($result) . '"';
	//print 'Result: "' . $result . '"';
	//print '$result->num_rows ' . $result->num_rows . '"';
	if ($result->num_rows == 0)
	{
		array_push($empty_dates, $date);
	}
}
# 3. If no empty dates, then show modal popup.
if (count($empty_dates) == 0)
{
	// Empty days are absent. Show modal alert.
	//$_SESSION['modal_title'] = 'Автозаполнение';
	//$_SESSION['modal_text'] = 'В Вашем меню нет незаполненных дней.';
	print 0;
}
else
{	
	# 4. Select random dishes for the empty dates for current user.
	$my_order = array();
	foreach ($empty_dates as &$date) {
						
		// 6.1. Select "Салаты".
		$sql = "SELECT Id FROM $table_food WHERE Date = '$date' AND Type LIKE '%алаты%'
					AND Price = (SELECT Price FROM $table_food WHERE Type LIKE '%алаты%' AND Date = '$date' ORDER BY Price DESC LIMIT 1 OFFSET 1)
					ORDER BY $table_food.Company ASC
					LIMIT 1";
		/*
		$sql = "SELECT Id FROM $table_food WHERE Date = '$date' AND Type LIKE '%алаты%'
					AND Price = (SELECT Price FROM $table_food WHERE Type LIKE '%алаты%' AND Date = '$date' ORDER BY Price DESC LIMIT 1 OFFSET 1)
					ORDER BY $table_food.Company ASC
					LIMIT 1";
		*/

		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			array_push($my_order, $row["Id"]);
		}

		// 6.2. Select "Первые блюда".
		$sql = "SELECT Id FROM $table_food WHERE Date = '$date' AND Type LIKE '%ерв%'
					AND Price = (SELECT MAX(Price) FROM $table_food WHERE Type LIKE '%ерв%' AND Date = '$date' LIMIT 1)
					ORDER BY $table_food.Company ASC
					LIMIT 1";
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			array_push($my_order, $row["Id"]);
		}

		// 6.3. Select "Вторые".
		$sql = "SELECT Id FROM $table_food WHERE Date = '$date' AND Type LIKE '%тор%'
					AND Price = (SELECT Price FROM $table_food WHERE Type LIKE '%тор%' AND Date = '$date' AND Price <= 200 ORDER BY Price DESC LIMIT 1)
					ORDER BY $table_food.Company ASC
					LIMIT 1";
		/*
		$sql = "SELECT Id FROM $table_food WHERE Date = '$date' AND Type LIKE '%тор%'
					AND Price = (SELECT Price FROM $table_food WHERE Type LIKE '%тор%' AND Date = '$date' ORDER BY Price DESC LIMIT 1 OFFSET 2)
					ORDER BY $table_food.Company ASC
					LIMIT 1";
		*/
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			array_push($my_order, $row["Id"]);
		}
		
	}

	// 7. Add selected dishes to the order.
	foreach ($my_order as &$menu_item_id) {
		$sql = "INSERT INTO $table_orders (UserId, MenuItemId, Count) VALUES ($user_id, $menu_item_id, 1)";
		$result = $conn->query($sql);
	}
	
	// Autofill is OK.
	print 1;
}
?>