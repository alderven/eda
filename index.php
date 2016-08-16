<?php
session_start();
require_once "header.php";
?>
<head>
	<link href="signin.css" rel="stylesheet">
</head>
<body>
	<div class="container">
		<form class="form-signin" method="post" action="checklogin.php">
			<div align="center">
				<h2 class="form-signin-heading">Представьтесь, пожалуйста</h2>
			</div>
			<label for="inputEmail" class="sr-only">Email address</label>
			<input type="email" name="myusername" id="inputEmail" class="form-control" placeholder="Адрес электронной почты" required autofocus>
			<label for="inputPassword" class="sr-only">Password</label>
			<input type="password" name="mypassword" id="inputPassword" class="form-control" placeholder="Пароль" required>
			<!--
			<div class="checkbox">
				<label><input type="checkbox" value="remember-me"> Запомнить меня</label>
			</div>
			-->
			<button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
			<div align="right"><a href="ForgotPassword.php">Забыли пароль?</a></div>
		</form>

<?php
if (isset($_SESSION['loginError'])) {
	print '<div class="alert alert-danger"><div align="center">' . $_SESSION['loginError'] . '</div></div>';
	unset($_SESSION['loginError']);
}
else if (isset($_SESSION['changePasswordSuccess'])) {
	print '<div class="alert alert-success"><div align="center">' . $_SESSION['changePasswordSuccess'] . '</div></div>';
	unset($_SESSION['changePasswordSuccess']);
}
?>			

</div>

<?php
require_once "footer.php";
?>