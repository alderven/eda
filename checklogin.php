<?php
session_start();
require_once "header.php";
require_once "config.php";

// Define User settings
$myusername=strtolower($_POST['myusername']); 
$mypassword=$_POST['mypassword'];

// To protect MySQL injection
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);
$sql = "SELECT Password FROM users WHERE Login=\"$myusername\" AND isActive = 1";
$result = $conn->query($sql);
$count=mysqli_num_rows($result);

$error_msg = 'Некорректный адрес электронной почты и/или пароль, или аккаунт деактивирован';
$error_redirect = 'index.php';
if($count==0){
	$_SESSION['loginError'] = $error_msg;
	header("location:$error_redirect");
	exit();
}

while ($row = $result->fetch_assoc()) {
	$password = $row["Password"];
}

if (password_verify($mypassword, $password)) {

	$_SESSION['myusername'] = $myusername;

	# Check for mobile client
	$useragent=$_SERVER['HTTP_USER_AGENT'];
	if(mobile()) {
		header('location: menu.m.php');
	}
	else {
		# Generate refer link for Desktop version
		$refer_url = null;
		$sql = "SELECT MenuFilter FROM users WHERE Login = \"$myusername\"";
		# print $sql;
		$result = $conn->query($sql);
		while ($row = $result->fetch_assoc()) {
			$refer_url = $row["MenuFilter"];
		}
		$refer_url = $refer_url == '' ? 'menu.php' : $refer_url;
		header("location:$refer_url");
	}
}
else {
	$_SESSION['loginError'] = $error_msg;
	header("location:$error_redirect");
}
?>
