<?php
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}


$login = isset($_GET['myusername']) ? $_GET['myusername'] : '';

print 'PHP Login: ' . $login;

/*
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);

$sql = "SELECT Id FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
        $user_id = $row["Id"];
    }
	
$sql = "INSERT INTO $table_orders (UserId, MenuItemId) VALUES ($user_id, $dish_id)";
$result = $conn->query($sql);

$conn->close();
*/
?>