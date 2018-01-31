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

function day_of_week($date, $short=false) {

	$dayofweek = date('w', strtotime($date));
	if($dayofweek == 1) {
		$dayofweek = ($short) ? 'ПН' : 'Понедельник';
	} elseif($dayofweek == 2) {
		$dayofweek = ($short) ? 'ВТ' : 'Вторник';
	} elseif($dayofweek == 3) {
		$dayofweek = ($short) ? 'СР' : 'Среда';
	} elseif($dayofweek == 4) {
		$dayofweek = ($short) ? 'ЧТ' : 'Четверг';
	} elseif($dayofweek == 5) {
		$dayofweek = ($short) ? 'ПТ' : 'Пятница';
	} elseif($dayofweek == 6) {
		$dayofweek = ($short) ? 'СБ' : 'Суббота';
	} elseif($dayofweek == 7) {
		$dayofweek = ($short) ? 'ВС' : 'Воскресенье';
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

function mobile() {
	$useragent=$_SERVER['HTTP_USER_AGENT'];
	if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
	{
		return true;
	}
	return false;
}

 ?>