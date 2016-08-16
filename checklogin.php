<?php
session_start();
require_once "header.php";
require_once "config.php";

// Define $myusername and $mypassword 
$myusername=$_POST['myusername']; 
$mypassword=$_POST['mypassword']; 

// To protect MySQL injection (more detail about MySQL injection)
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
	session_start();
	$_SESSION['myusername'] = $myusername;
	$_SESSION['mypassword'] = $mypassword;

	if($mypassword === "12345") {
		$_SESSION['errorMessage'] = 'Ваш текущий пароль очень простой. Пожалуйста, смените его на более сложный.';
		header("location:change_password.php");
		# $_SESSION['loginError'] = 'Ваш текущий пароль был сброшен в целях повышения безопасности. Пожалуйста, смените пароль, воспользовавшись формой "Забыли пароль?"';
		# header("location:index.php");
	}
	else {
		header("location:menu.php");	
	}
}
else {
	$_SESSION['loginError'] = 'Некорректный адрес электронной почты и/или пароль';
	header("location:index.php");
}
?>
