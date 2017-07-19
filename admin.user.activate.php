<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
require_once "common.php";

# Check for Admin priveleges
is_admin($role_id);

# Get Modified User Id
$modified_user_id = isset($_POST['UserId']) ? $_POST['UserId'] : '';
if ($modified_user_id == '') {
	alert("danger", "Пользователь не выбран", "admin.users.php");
}

# Get User's 'isActive' state
$sql = "SELECT isActive, Name, Surname FROM users WHERE Id = $modified_user_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$isActive = $row["isActive"];
	$name = $row["Name"];
	$surname = $row["Surname"];
}

# Change User's 'isActive' state on the opposite
$isActive = ($isActive == 1) ? 0 : 1;
$sql = "UPDATE users SET isActive = $isActive WHERE Id = $modified_user_id";
$result = $conn->query($sql);

# Show result message
$action = ($isActive == 1) ? 'активирован' : 'деактивирован';
alert("success", "Пользователь <b>$surname $name</b> $action", "admin.users.php");
 ?>