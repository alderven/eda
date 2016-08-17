<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

# 1. Get Settings values and save them to DB
foreach ($autofillSettings as $autofillSetting) {
	$settingValue = isset($_POST[$autofillSetting]) ? 1 : 0;
	$sql = "UPDATE $table_users SET $autofillSetting = $settingValue WHERE Id = $user_id";
	$result = $conn->query($sql);
}

# 2. Close DB connection
$conn->close();

# 3. Return back to previous page
header('Location: ' . $_SERVER['HTTP_REFERER']);
?>