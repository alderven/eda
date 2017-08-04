<?php
session_start();
require_once "config.php";
require_once "common.php";

function generate_dishes_list($conn, $user_id, $excel_id) {
	
	$sql = "SELECT food.Date, food.Name, orders.Count
		FROM users
		JOIN orders
		ON orders.UserId = users.Id
		JOIN food ON food.Id = orders.MenuItemId
		WHERE orders.UserId = $user_id
		AND food.ExcelId = $excel_id
		ORDER BY food.Date";
		
	$html = '';
	$error = '<br>Не удалось сгенеририровать список заказов</br>';
	if ($result = $conn->query($sql)) {
		
		if ($result->num_rows > 0) {
		
			setlocale( LC_TIME, 'ru_RU', 'russian' );
			
			$previous_date = Null;
			
			while ($row = $result->fetch_assoc()) {
							
				if ($previous_date != $row["Date"]) {
					
					$dayofweek = day_of_week($row["Date"]);
					
					$months = array( '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
					$month = $months[intval(date("m", strtotime($row["Date"])))];
					
					$html .= '<br>
								<h4>
									<b>' . $dayofweek . ' ' . date("d", strtotime($row["Date"])) . ' ' . $month . '
									</b>
								</h4>
								'; # next line made for fixing the issue with HTML formatting
				}
				$html .= ' - '. $row["Name"] . ' ' . $row["Count"] . ' шт.<br>
				';  # next line made for fixing the issue with HTML formatting
				
				$previous_date =  $row["Date"];
			}
		}
		else
		{
			$html = $error;
		}
	}
	else {
		$html = $error;
	}
	
	return $html;
}

# Transliterate (for attachment file name)
function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}

# Compose email
function compose_email($company, $name, $surname, $dates, $user_email, $week_number, $send_email_from, $send_email_from_pass, $user_id, $excel_id, $conn)
{
	##############################################################################################
	# DON'T FORGET TO DISABLE ADAM'S AND SERGEY'S REAL EMAILS WHEN DEBUG
	##############################################################################################
	
	# $mail_to = ["spetrochenkov@adalisk.com", "aananyev@adalisk.com", "$user_email"]; // Release
	# $mail_to = ["spetrochenkov@adalisk.com", "aananyev@adalisk.com", "vvatulin@adalisk.com", "$user_email"]; // Include V.Vatulin
	# $mail_to = ["aananyev@adalisk.com", "eda@adalisk.com"]; // Debug
	$mail_to = ["eda@adalisk.com", "$user_email"]; // Debug email notifications
	if ($company === 'Адам')
	{
		# $mail_to = ["adoskhoev@adalisk.com", "aananyev@adalisk.com", "$user_email"]; // Release
		# $mail_to = ["aananyev@adalisk.com"]; // Debug
		$mail_to = ["eda@adalisk.com", "alexander.ananyev@glidewelldental.com", "$user_email"]; // Debug email notifications
	}
	
	$subject = $week_number . ' неделя - ' . $company . ' - ' . $dates[0] . '-' . $dates[count($dates)-1];
	if ("$week_number" === '0')
	{
		$subject = $company . ' - ' . $dates[0] . '-' . $dates[count($dates)-1];
	}
			
	$body = "<html>
				<head>
					<meta charset=\"UTF-8\">
				</head>
				<body>
					Заказ сделал(а): <b>" . $name . " " . $surname . "</b>.
					<br>
					<br>
					Состав заказа:
					<br>
					" . generate_dishes_list($conn, $user_id, $excel_id) . "
					<br>
					<h6>Не отвечайте на это письмо! Оно было отправлено роботом. По всем вопросам пишите на aananyev@adalisk.com<h6>
				</body>
			</html>";

	$result = send_email($subject, $send_email_from, $send_email_from_pass, $mail_to, $body);
	return $result;
}


# 1. Get Date
$date = $_GET['date'];

# 2. Get nearest date if date not found
if ($date == '') {
	$sql = "SELECT DISTINCT Date FROM food WHERE DATE >= CURDATE() ORDER BY Date ASC LIMIT 1";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		 while($row = $result->fetch_assoc()) {
			$date = $row["Date"];
		 }
	}
}
error_log('SendEmail: Date: "' . $date . '"', 0);

# 3. Get ExcelId by Date (Date is equal to date of opened UI tab by User)
$sql = "SELECT DISTINCT ExcelId FROM $table_food WHERE Date = '$date'";
$result = $conn->query($sql);
$excel_id = null;
 while ($row = $result->fetch_assoc()) {
	$excel_id = $row["ExcelId"];
	# error_log('SendEmail: Excel Id: ' . $excel_id, 0);
}

# 4. Get all Dates by ExcelId
$sql = "SELECT DISTINCT Date FROM $table_food WHERE ExcelId = '$excel_id'";
$result = $conn->query($sql);
$dates = '';
 while ($row = $result->fetch_assoc()) {
	$dates .= '"' . $row["Date"] . '", ';
	# error_log('SendEmail: Date: ' . $row["Date"], 0);
}
$dates = trim($dates); # remove space at the end of string
$dates = trim($dates, ",");  # remove comma at the end of string

if ($dates == '') {
	$title = 'Ошибка';
	$text = 'Ошибка. Корректная дата не найдена.';
	modal($title, $text, 'menu.php');
}

# 5. Find all ExcelId's by Date's
$sql = "SELECT DISTINCT ExcelId FROM $table_food WHERE Date IN ($dates)";
$result = $conn->query($sql);
$excel_ids = array();
 while ($row = $result->fetch_assoc()) {
	 
	 # 5.1 Check if User order anything in this Excel
	$sql1 = "SELECT $table_orders.MenuItemId FROM $table_orders
			JOIN $table_users ON $table_orders.UserId = $table_users.Id
			JOIN $table_food ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_users.Id = $user_id AND $table_food.ExcelId = '" . $row["ExcelId"] . "'";
	$result1 = $conn->query($sql1);	
	
	if ($result1->num_rows > 0)
	{
		array_push($excel_ids, $row["ExcelId"]);
	}
}

# 6. If User do not make order at all.
$location = "menu.php?date=$date";
if (count($excel_ids) == 0)
{
	$title = 'Письмо не отправлено';
	$text =  'Письмо не отправлено. Вы пока ничего не заказали.';
	modal($title, $text, $location);
}
else {
	# 7. Loop over ExcelId's.
	$title = 'Статус оправки письма';
	$text = [];
	
	foreach ($excel_ids as $excel_id) {

		# 8. Get Dates range.
		$dates = array();
		$sql = "SELECT DISTINCT Date FROM $table_food WHERE ExcelId = '$excel_id' ORDER BY Date ASC";
		$result = $conn->query($sql);
		while ($row = $result->fetch_assoc()) {
			array_push($dates, $row["Date"]);
		}
			
		// 11. Get Company Name.
		$company = '';
		$sql = "SELECT DISTINCT Company FROM $table_food WHERE ExcelId = '$excel_id'";
		$result = $conn->query($sql);
		while ($row = $result->fetch_assoc()) {
			$company =  $row["Company"];
		}
		
		// 12. Get Week Number
		$week_number = '';
		$sql = "SELECT DISTINCT WeekNumber FROM $table_food WHERE ExcelId = '$excel_id'";
		$result = $conn->query($sql);
		while ($row = $result->fetch_assoc()) {
			$week_number =  $row["WeekNumber"];
		}
		
		$result = compose_email($company, $name, $surname, $dates, $email, $week_number, $send_email_from, $send_email_from_pass, $user_id, $excel_id, $conn);
		
		if ($result) {
			array_push($text, "Ошибка при отправке письма для компании <b>$company</b>. Сообщение от почтового клиента: $result");
			}
		else {
			array_push($text, "Письмо о заказе в компании <b>$company</b> отправлено на ваш адрес <b>$email</b>.");
		}
	}
	
	// 18. Display success popup
	modal($title, implode("<br>", $text), $location);
}

// 20. Close DB connection.
$conn->close();

?>