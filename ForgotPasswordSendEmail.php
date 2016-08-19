<?php
session_start();

# 1. Start User session
$myusername=strtolower($_POST['myusername']);
$_SESSION['myusername'] = $myusername;

# 2. Include config
require_once "config.php";

$sql="SELECT Name, Id FROM $table_users WHERE login='$myusername'";
$result = $conn->query($sql);
$count=mysqli_num_rows($result);
if($count == 1){
	
	# Get User Name
	 while ($row = $result->fetch_assoc()) {
		$name = $row["Name"];
		$userId = $row["Id"];
	}
	
	# Generate GUID
	$id = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	$restoreLink = $site_url . 'restore_password.php?restoreId=' . $id . '&id=' . $userId;
	$sql="UPDATE $table_users SET RestorePasswordGUID  = '$id' WHERE login='$myusername'";
	$result = $conn->query($sql);
	
	# Send Email
	include 'Mail.php';
	$from = '<'. $send_email_from . '>';
	$to = '<' . $myusername . '>';
	$subject = 'Сервис «Еда»: Восстановление пароля';
	$subject = "=?UTF-8?B?" . base64_encode(html_entity_decode($subject, ENT_COMPAT, 'UTF-8')) . "?=";
	$body = '<html><head><meta charset=\"UTF-8\"></head><body>' . $name . ',<br><br>Для восстановления пароля перейдите, пожалуйста, по ссылке: <a href=' . $restoreLink . '> ' . $restoreLink . '</a><br><br><h6>Не отвечайте на это письмо! Оно было отправлено роботом. По всем вопросам пишите на aananyev@adalisk.com<h6></body></html>';

	$headers = array(
		'From' => $from,
		'To' => $to,
		'Subject' => $subject,
		'Content-Type' => 'text/html; charset=UTF-8'
	);

	$smtp = Mail::factory('smtp', array(
			'host' => 'ssl://smtp.yandex.ru',
			'port' => '465',
			'auth' => true,
			'username' => $send_email_from,
			'password' => $send_email_from_pass
		));

	$mail = $smtp->send($to, $headers, $body);

	$messageType = 'success';
	$messageText = 'На указанный Вами адрес электронной почты отправлено письмо с инструкцией по восстановлению пароля.';
	
	if (PEAR::isError($mail)) {
		$messageType = 'danger';
		$messageText = 'Ошибка отправки сообщения: ' . $mail->getMessage();
	}
		
	# Show message
	require_once "header.php";
	print '	<head>
				<link href="signin.css" rel="stylesheet">
			</head>
			<body>
				<div class="container">
					<div align="center">
						<h2 class="form-signin-heading">Восстановление пароля</h2>
					</div>
					<br>
					<br>
					<div align="center" class="alert alert-' . $messageType . '">' . $messageText . '</div>
				</div>';

	require_once "footer.php";
}
else {
	$_SESSION['errorMessage'] = 'Введенный Вами адрес электронной почты не найден';
	header("location:ForgotPassword.php");
}
?>