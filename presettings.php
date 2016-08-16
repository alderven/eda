<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}

// 1. Configure DB connection
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);

// 2. Get UserId
$sql = "SELECT Id FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
        $user_id = $row["Id"];
    }

// 3. Get Custom Filter
$sql = "SELECT * FROM $table_filters WHERE UserId = $user_id";
$result = $conn->query($sql);
$row_cnt = '';
while($row = $result->fetch_assoc()) {
	$row_cnt = $result->num_rows;
}
// 4. Generate Default Filter if Custom Filter is Empty
if ($row_cnt === '') {
	
	// 4.1 Generate Default Filter
	$sql = "SELECT DISTINCT Type, Company FROM $table_food WHERE Date >= 2015-12-01 AND TRIM(Type) <> '' ORDER By Type";
	$result = $conn->query($sql);
	$sql_tmp = '';
	$priority = 0;
	 while ($row = $result->fetch_assoc()) {
		$type = $row["Type"];
		$company = $row["Company"];
		$sql_tmp .= "INSERT INTO $table_filters (UserId, FoodType, Company, Enabled, Priority) VALUES ($user_id, '$type', '$company', 1, $priority);";
		$priority++;
    }
	
	// 4.2 Write Default Filter to DB
	$result = $conn->multi_query($sql_tmp);
	
	// 4.3 Wait while DB modifications applied
	sleep(1);
}

// 5. Close DB connection
$conn->close();

// 6. Redirect to other page
header("Location: settings.php");
 ?>