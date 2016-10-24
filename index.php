<?php

function orders_sum()
{
	require_once "config.php";
	$sql = "SELECT SUM(Count) as Sum FROM $table_orders";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$sum = $row['Sum'];
	}	
	$conn->close();
	return $sum;
}

function bludo($sum)
{
	$last_digit = substr( $sum, -1 );
	
	switch ($last_digit) {
		case 0:
		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
			return 'блюд';
		case 1:
			return 'блюдо';
		case 2:
		case 3:
		case 4:
			return 'блюда';
	}
}

session_start();
require_once "header.php";

$sum = orders_sum();

print '
<head>
	<link href="signin.css" rel="stylesheet">
</head>
<body>
	<div class="container">
	<h1 align="center"><span class="glyphicon glyphicon-cutlery"></span> Сервис «Еда»</h1>
	<h3 align="center"><p>Уже заказано <b>' . $sum . '</b> ' . bludo($sum) . '</h3>
		<form class="form-signin" method="post" action="checklogin.php">
			<label for="inputEmail" class="sr-only">Email address</label>
			<input type="email" name="myusername" id="inputEmail" class="form-control" placeholder="Адрес электронной почты" required autofocus>
			<label for="inputPassword" class="sr-only">Password</label>
			<input type="password" name="mypassword" id="inputPassword" class="form-control" placeholder="Пароль" required>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
			<div align="right"><a href="ForgotPassword.php">Забыли пароль?</a></div>
		</form>';

if (isset($_SESSION['loginError'])) {
	print '<div class="alert alert-danger"><div align="center">' . $_SESSION['loginError'] . '</div></div>';
	unset($_SESSION['loginError']);
}
else if (isset($_SESSION['changePasswordSuccess'])) {
	print '<div class="alert alert-success"><div align="center">' . $_SESSION['changePasswordSuccess'] . '</div></div>';
	unset($_SESSION['changePasswordSuccess']);
}

print '</div>';
require_once "footer.php";