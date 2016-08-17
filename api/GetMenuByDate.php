<?php
/*
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
*/

// 1. Get Date.
$date = isset($_GET['date']) ? $_GET['date'] : '';

// 2. Get User Id.
$user_id = isset($_GET['userId']) ? $_GET['userId'] : '';

if ($date === '') {
	
	print 'Ошибка. Пустой параметр "date".';
}
else {
	
	// 3. Encode DB symbols..
	require_once "./../config.php";
	$sql = "SET NAMES utf8";
	$conn->query($sql);
	
	if ($user_id !== '') {
		
		// 4.1 Generate SQL query with User Id.
		$sql = "SELECT Id, Date, Type, Name, Weight, Price, Contain, Company FROM $table_food WHERE Date = '$date'";
		
		$sql = "SELECT Id, Date, Type, Name, Weight, Price, Contain, Company FROM $table_food
				LEFT OUTER JOIN $table_orders ON $table_food.Id=$table_orders.MenuItemId
				WHERE $table_orders.UserId = $user_id
				AND Date = '$date'
				ORDER BY $table_food.Id";
	}
	else {

		// 4.1 Generate SQL query without User Id.
		$sql = "SELECT Id, Date, Type, Name, Weight, Price, Contain, Company FROM $table_food WHERE Date = '$date'";
	}	

	//  5. Make SQL request.
	$result = $conn->query($sql);
	$rows = array();
	 while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		
	// 6. Print result as JSON.
	print json_encode($rows, JSON_UNESCAPED_UNICODE);
	
	// 6. Close DB connection.
	$conn->close();
}
?>