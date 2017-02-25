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

# Get Next User Id
$next_user_id = isset($_POST['UserId']) ? $_POST['UserId'] : '';
if ($next_user_id == '') {
	alert("danger", "Пользователь не выбран", "admin.users.php");
}

# # Get Next User Name
$sql = "SELECT Login FROM users WHERE Id = $next_user_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$login = $row["Login"];
}

# Update 'myusername' session
$_SESSION['myusername'] = $login;

# Redirect to menu.php
header("location:menu.php");

 ?>