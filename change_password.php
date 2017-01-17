<?php
require_once "header.php";
session_start();
?>

<head>
	<link href="signin.css" rel="stylesheet">
</head>
	<body>
		<div class="container">
			<form class="form-signin" method="post" action="ChangePasswordVerification.php">
				<div align="center"><h2 class="form-signin-heading">Смена пароля</h2></div>
				<label for="inputPassword" class="sr-only">Password</label>
				<input type="password" name="newPassword" id="newPassword" class="form-control" placeholder="Новый пароль" required>
				<label for="inputPassword" class="sr-only">Password</label>
				<input type="password" name="newPasswordRepeat" id="newPasswordRepeat" class="form-control" placeholder="Новый пароль (еще раз)" required>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Сменить пароль</button>
			</form>

<?php
if (isset($_SESSION['errorMessage'])) {
	print '<div class="alert alert-danger"><div align="center">' . $_SESSION['errorMessage'] . '</div></div>';
	unset($_SESSION['errorMessage']);
}
?>	

</div>
	
<?php
require_once "footer.php";
?>