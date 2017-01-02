<?php


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

 ?>