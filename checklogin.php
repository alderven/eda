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
$sql="SELECT * FROM $table_users WHERE login='$myusername' and password='$mypassword'";
$result = $conn->query($sql);

// Mysql_num_row is counting table row
$count=mysqli_num_rows($result);

// If result matched $myusername and $mypassword, table row must be 1 row
if($count==1){

	// Register $myusername, $mypassword and redirect to file "menu.php"
	$_SESSION['myusername'] = $myusername;
	$_SESSION['mypassword'] = $mypassword;

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
else {
	$_SESSION['loginError'] = 'Некорректный адрес электронной почты и/или пароль';
	header("location:index.php");
}
?>
