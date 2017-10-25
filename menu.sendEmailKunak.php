<?php
session_start();
require_once "config.php";
require_once "common.php";
	
# Send Email
$subject = 'Сервис «Еда»: ' . $name . ' ' . $surname . ' обедает в «Кунаке»';
$body = '<html><head><meta charset=\"UTF-8\"></head><body>' . $name . ' ' .  $surname . ' обедает в «Кунаке»<br><br>
<br><h6>Не отвечайте на это письмо! Оно было отправлено роботом. По всем вопросам пишите на aananyev@adalisk.com<h6></body></html>';

$result = send_email($subject, $send_email_from, $send_email_from_pass, $recipients_kunak, $body);

if ($result) {
	$title = "Cообщение не отправлено";
	$text = "Попробуйте еще раз. Ошибка: $result";
	}
else {
	$title = "Сообщение отправлено";
	$text = "Приятного аппетита в «Кунаке»!";
}
modal($title, $text, "menu.php?date=$date");
?>