<?php
session_start();
require_once "config.php";

// Define $myusername and $mypassword 
$myusername=strtolower($_SESSION['myusername']);
$newPassword=$_POST['newPassword'];
$newPasswordRepeat=$_POST['newPasswordRepeat'];

// To protect MySQL injection
$myusername = stripslashes($myusername);
$newPassword = stripslashes($newPassword);
$newPasswordRepeat = stripslashes($newPasswordRepeat);

$myusername = mysql_real_escape_string($myusername);
$newPassword = mysql_real_escape_string($newPassword);
$newPasswordRepeat = mysql_real_escape_string($newPasswordRepeat);


$errorMessage = NULL;

$sql="SELECT * FROM $table_users WHERE login='$myusername'";
$result = $conn->query($sql);
$count=mysqli_num_rows($result);
if($count == 1){

	# Password Validation
	if (strcmp($newPassword, $newPasswordRepeat) !== 0) {	
		$errorMessage .= '- Новый пароль и повтор нового пароля не совпадают<br>';
	}
	if (strlen($newPassword) < 5) {
		$errorMessage .= '- Длина нового пароля должна быть не менее 5 символов<br>';
	}
	if (!preg_match('/[a-zA-Z]/', $newPassword)) {
		$errorMessage .= '- Новый пароль должен содержать латинские буквы<br>';
	}
	if (!preg_match('/\d/', $newPassword)) {
		$errorMessage .= '- Новый пароль должен содержать цифры<br>';
	}
	if ($errorMessage === NULL)  {
		# Write new Password to DB
		$newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
		$sql = "UPDATE $table_users SET Password = '$newPasswordHashed' WHERE Login='$myusername'";
		$result = $conn->query($sql);
		unset($_SESSION['myusername']);
		unset($_SESSION['mypassword']);
		$_SESSION['changePasswordSuccess'] = 'Пароль успешно изменен! Войдите на сайт с новым паролем.';
		header("location:index.php");
		exit;
	}
}
else {
	$errorMessage = 'Текущий аккаунт не найден';
}
$_SESSION['errorMessage'] = $errorMessage;
header("location:change_password.php");
?>