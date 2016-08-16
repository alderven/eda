<?php
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

// 3. Get Settings values and save them to DB
foreach ($autofillSettings as $autofillSetting) {
	$settingValue = isset($_POST[$autofillSetting]) ? 1 : 0;
	$sql = "UPDATE $table_users SET $autofillSetting = $settingValue WHERE Id = $user_id";
	$result = $conn->query($sql);
}

// 4. Close DB connection
$conn->close();

// 5. Return back to previous page
header('Location: ' . $_SERVER['HTTP_REFERER']);
?>