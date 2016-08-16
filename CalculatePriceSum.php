<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}

// 1. Get User Id.
$user_id = isset($_GET['userId']) ? $_GET['userId'] : '';

// 2. Get Date.
$date = isset($_GET['date']) ? $_GET['date'] : '';

// 3. Configure DB connection.
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);

// 4. Calculate Sum Price
$day_sum = 0;
$sql = "SELECT SUM($table_food.Price * $table_orders.Count) AS TotalPrice
		FROM $table_food LEFT OUTER JOIN $table_orders ON $table_food.Id=$table_orders.MenuItemId
		WHERE $table_food.Date = '$date' AND $table_orders.UserId = $user_id";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
	$day_sum = $row["TotalPrice"];
}
if ($day_sum == Null)
{
	$day_sum = 0;
}

// 5. Find out Excel Id for specified dates.
$sql = "SELECT DISTINCT ExcelId FROM $table_food WHERE Date = '$date'";
$result = $conn->query($sql);
$excel_ids = array();
while($row = $result->fetch_assoc()) {
	array_push($excel_ids, $row["ExcelId"]);
}

// 6. Calculate Total Week Price.
$week_sum = 0;
foreach ($excel_ids as &$excel_id) {
    $sql = "SELECT SUM($table_food.Price * $table_orders.Count) AS TotalPrice
			FROM $table_food LEFT OUTER JOIN $table_orders ON $table_food.Id=$table_orders.MenuItemId
			WHERE $table_food.ExcelId = '$excel_id'
			AND $table_orders.UserId = $user_id
			AND $table_food.Date >= CURDATE()";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()) {
		$week_sum = $week_sum + $row["TotalPrice"];
	}
}

// 7. Get the number of days in week.
$sql = "SELECT DISTINCT Date FROM $table_food WHERE DATE >= CURDATE() AND ExcelId = '$excel_ids[0]'";
$result = $conn->query($sql);
$days_count = 0;
if ($result = $conn->query($sql)) {
	$days_count = $result->num_rows;
}

// 8. Calculate Average Week Price.
$average = intval($week_sum / $days_count);

// 9. Calculate Week number for the specified date.
$sql = "SELECT DISTINCT Date FROM $table_food WHERE DATE >= CURDATE() ORDER BY Date ASC";
$result = $conn->query($sql);
$week_number = 0;
$daysofweek = array();
while($row = $result->fetch_assoc()) {
	$dayofweek_dgt = date('w', strtotime($row["Date"]));
	foreach ($daysofweek as &$day_nmbr) {
		if ($dayofweek_dgt == $day_nmbr or $dayofweek_dgt <= $day_nmbr) {
			$week_number++;
			// Cleanup the array.
			$daysofweek = array();			
		}
	}
	if ($row["Date"] === $date) {
		break 1;
	}
	array_push($daysofweek, $dayofweek_dgt);
}

print json_encode(array('day_sum' => $day_sum, 'week_sum' => $week_sum, 'week_number' => $week_number, 'average' => $average));