<?php
session_start();
require_once "header.php";
require_once "config.php";

// Define $myusername and $mypassword 
$myusername=$_SESSION['myusername'];
$currentPassword=$_POST['currentPassword'];
$newPassword=$_POST['newPassword'];
$newPasswordRepeat=$_POST['newPasswordRepeat'];

// To protect MySQL injection
$myusername = stripslashes($myusername);
$currentPassword = stripslashes($currentPassword);
$newPassword = stripslashes($newPassword);
$newPasswordRepeat = stripslashes($newPasswordRepeat);

$myusername = mysql_real_escape_string($myusername);
$currentPassword = mysql_real_escape_string($currentPassword);
$newPassword = mysql_real_escape_string($newPassword);
$newPasswordRepeat = mysql_real_escape_string($newPasswordRepeat);


$errorMessage = NULL;

$sql="SELECT * FROM $table_users WHERE login='$myusername' and password='$currentPassword'";
$result = $conn->query($sql);
$count=mysqli_num_rows($result);
if($count == 1){

	# Password Validation
	if (strcmp($currentPassword, $newPassword) === 0) {
		$errorMessage = 'Новый пароль должен отличаться от старого';
	}
	else if (strcmp($newPassword, $newPasswordRepeat) !== 0) {	
		$errorMessage = 'Новый пароль и повтор нового пароля не совпадают';
	}
	else if (strlen($newPassword) < 5) {
		$errorMessage = 'Длина нового пароля должна быть не менее 5 символов';
	}
	else if (!preg_match('/[a-zA-Z]/', $newPassword)) {
		$errorMessage = 'Новый пароль должен содержать латинские буквы';
	}
	else if (!preg_match('/[^a-zA-Z\d]/', $newPassword)) {
		$errorMessage = 'Новый пароль должен содержать символы';
	}
	else if (!preg_match('/\d/', $newPassword)) {
		$errorMessage = 'Новый пароль должен содержать цифры';
	}
	else {
		# Write new Password to DB
		$sql = "UPDATE $table_users SET Password = '$newPassword' WHERE Login='$myusername'";
		$result = $conn->query($sql);
		unset($_SESSION['myusername']);
		unset($_SESSION['mypassword']);
		$_SESSION['changePasswordSuccess'] = 'Пароль успешно изменен! Войдите на сайт с новым паролем.';
		header("location:index.php");
		exit;
	}
}
else {
	$errorMessage = 'Некорректный текущий пароль';
}
$_SESSION['errorMessage'] = $errorMessage;
header("location:change_password.php");
?>