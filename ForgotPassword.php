<?php
session_start();
require_once "header.php";
?>
<head>
	<link href="signin.css" rel="stylesheet">
</head>
<body>
	<div class="container">
		<form class="form-signin" method="post" action="ForgotPasswordSendEmail.php">
			<div align="center">
				<h2 class="form-signin-heading">Восстановление пароля</h2>
			</div>
			<label for="inputEmail" class="sr-only">Email address</label>
			<input type="email" name="myusername" id="inputEmail" class="form-control" placeholder="Ваш адрес электронной почты" required autofocus>
			<br>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Восстановить пароль</button>
		</form>

<?php
if (isset($_SESSION['errorMessage'])) {
	print '<div class="alert alert-danger">' . $_SESSION['errorMessage'] . '</div>';
	unset($_SESSION['errorMessage']);
}
?>			

</div>

<?php
require_once "footer.php";
?>