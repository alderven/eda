<?php
require_once "header.php";
session_start();
?>

<head>
	<link href="signin.css" rel="stylesheet">
</head>
	<body>
		<div class="container">


<?php
if (isset($_GET['restoreId']) and isset($_GET['id'])) {
	require_once "config.php";
	$sql='SELECT Login FROM ' . $table_users . ' WHERE RestorePasswordGUID="' . $_GET['restoreId'] . '" AND Id=' . $_GET['id'];
	$result = $conn->query($sql);
	$count=mysqli_num_rows($result);
	if($count == 1){
		while ($row = $result->fetch_assoc()) {
			$_SESSION['myusername'] = $row["Login"];
		}
		$_SESSION['restoreId'] = $_GET['restoreId'];
		$_SESSION['userId'] = $_GET['id'];
		
		print '	<form class="form-signin" method="post" action="restore_password_verification.php">
					<div align="center"><h2 class="form-signin-heading">Смена пароля</h2></div>
					<label for="inputPassword" class="sr-only">Password</label>
					<input type="password" name="newPassword" id="newPassword" class="form-control" placeholder="Новый пароль" required>
					<label for="inputPassword" class="sr-only">Password</label>
					<input type="password" name="newPasswordRepeat" id="newPasswordRepeat" class="form-control" placeholder="Новый пароль (еще раз)" required>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Сменить пароль</button>
				</form>';
	}
	else {
		$_SESSION['errorMessage'] = 'Ошибка восстановления пароля. Не найдено информации об аккаунте пользователя.';
	}
}
else {
	$_SESSION['errorMessage'] = 'Ошибка восстановления пароля. Параметры восстановления некорректные/не найдены.';
}

if (isset($_SESSION['errorMessage'])) {
	print '<div class="alert alert-danger"><div align="center">' . $_SESSION['errorMessage'] . '</div></div>';
	unset($_SESSION['errorMessage']);
}
?>	
	
</div>
	
<?php
require_once "footer.php";
?>