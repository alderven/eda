<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

// 1. Get enabled parameter.
$enabled = isset($_POST['enabled']) ? $_POST['enabled'] : '';

// 2. Get Data.
$data = isset($_POST['data']) ? $_POST['data'] : '';
	
// 5. Cleanup current Filter
$sql = "DELETE FROM $table_filters WHERE UserId = $user_id AND Enabled = $enabled";
$result = $conn->query($sql);
	
// 6. Write new Filters to DB
$sql = '';
$priority = 0;
foreach ($data as &$data1) {
	//print $data1;
	$data2 = explode('|', $data1);
	//print count($data2);
	$food_type = $data2[0];
	$company = $data2[1];
	//print "Food Type: $food_type, Company: $company";
	$sql .= "INSERT INTO $table_filters (UserId, FoodType, Company, Enabled, Priority) VALUES ($user_id, '$food_type', '$company', $enabled, $priority);";
	$priority++;
}
$result = $conn->multi_query($sql);

// 7. Close Connection
$conn->close();
?>