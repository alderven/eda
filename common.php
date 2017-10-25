<?php
include 'Mail.php';

function is_admin($role_id) {
	if ($role_id > 1) {
		header("location:menu.php");
	}
}

function ordered_dishes_count($conn, $excel_id) {
	$sql = "SELECT SUM(orders.Count) as Sum FROM orders
			INNER JOIN food
			ON food.Id = orders.MenuItemId
			WHERE food.ExcelId = $excel_id";
	$result = $conn->query($sql);
	$dishes_count = 0;
	while ($row = $result->fetch_assoc()) {
		$dishes_count = $row["Sum"];
	}
	$dishes_count = (is_null($dishes_count)) ? 0 : $dishes_count;
	
	return $dishes_count;
}

function alert($type, $text, $location) {
	$_SESSION['alert_type'] = $type;
	$_SESSION['alert_text'] = $text;
	header("location:" . $location);
	exit();
}

function modal($title, $text, $location) {
	$_SESSION['modal_title'] = $title;
	$_SESSION['modal_text'] = $text;
	header("location:$location");
}

function day_of_week($date) {

	$dayofweek = date('w', strtotime($date));
	if($dayofweek == 1) {
		$dayofweek = 'Понедельник';
	} elseif($dayofweek == 2) {
		$dayofweek = 'Вторник';
	} elseif($dayofweek == 3) {
		$dayofweek = 'Среда';
	} elseif($dayofweek == 4) {
		$dayofweek = 'Четверг';
	} elseif($dayofweek == 5) {
		$dayofweek = 'Пятница';
	} elseif($dayofweek == 6) {
		$dayofweek = 'Суббота';
	} elseif($dayofweek == 7) {
		$dayofweek = 'Воскресенье';
	} else {
		$dayofweek = '';
	};
	
	return $dayofweek;
}

function send_email($subject, $from_login, $from_pass, $to, $body) {

	$to = '<' . implode('>, <', $to). '>';
	error_log("Send email with Subject [$subject] to [$to]", 0);
	error_log("Email body: $body", 0);
	
	$headers = array(
		'From' => '<'. $from_login . '>',
		'To' => $to,
		'Subject' => "=?UTF-8?B?" . base64_encode(html_entity_decode($subject, ENT_COMPAT, 'UTF-8')) . "?=",
		'Content-Type' => 'text/html; charset=UTF-8'
	);
	
	$smtp = Mail::factory('smtp', array(
			'host' => 'ssl://smtp.yandex.ru',
			'port' => '465',
			'auth' => true,
			'username' => $from_login,
			'password' => $from_pass
		));

	$mail = $smtp->send($to, $headers, $body);
	$result = null;
	
	if (PEAR::isError($mail)) {
		$result = $mail->getMessage();
		error_log('Ошибка отправки сообщения: ' . $result, 0);
	}
	
	error_log("Sending email finished with the result: [$result]", 0);
	return $result;
}

function get_version() {
	$version = '';
	if (strpos(php_uname(), 'Windows 7') == true) {
		$version = 'Debug';	}
	return $version;
}
 ?>