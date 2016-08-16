<?php
session_start();
require_once "config.php";

##############################################################################################
# DON'T FORGET TO DISABLE ADAM'S AND SERGEY'S EMAILS WHEN DEBUG
##############################################################################################

include 'Mail.php';
include 'Mail/mime.php';

// 1. Get Date.
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Display popup if email successfully sent.
function popup_success($email, $date) {
	$_SESSION['modal_title'] = 'Письмо успешно отправлено';
	$_SESSION['modal_text'] = 'Проверьте свой ящик ' . $email . ', вы должны получить копию письма.';
	header("location:menu.php?date=$date");
}

// Transliterate (for attachment file name)
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

// Send Email
function send_email($company, $file, $name, $surname, $dates, $user_email, $week_number)
{
	$mail_to = 'spetrochenkov@adalisk.com, aananyev@adalisk.com, ' . $user_email; // Release
	//$mail_to = 'aananyev@adalisk.com, eda@adalisk.com'; // Debug
	if ($company === 'Адам')
	{
		$mail_to = 'adoskhoev@adalisk.com, aananyev@adalisk.com, ' . $user_email; // Release
		//$mail_to = 'aananyev@adalisk.com'; // Debug
	}
	
	$subject = $week_number . ' неделя - ' . $company . ' - ' . $dates[0] . '-' . $dates[count($dates)-1];
	$attachment_name = rus2translit($surname) . '_' . $week_number . '.xls';
	if ("$week_number" === '0')
	{
		$subject = $company . ' - ' . $dates[0] . '-' . $dates[count($dates)-1];
		$attachment_name = rus2translit($surname) . '.xls';
	}
	
	$subject = "=?UTF-8?B?" . base64_encode(html_entity_decode($subject, ENT_COMPAT, 'UTF-8')) . "?=";
		
	$html = "<html><head><meta charset=\"UTF-8\"></head><body>Обед заказал(а): <b>" . $name . " " . $surname . "</b>.<br><br>Спасибо!<br><br><h6>Не отвечайте на это письмо! Оно было отправлено роботом. По всем вопросам пишите на aananyev@adalisk.com<h6></body></html>";
	$crlf = "\n";
	$hdrs = array(
				  'From'    => $GLOBALS['send_email_from'],
				  'To'    => $mail_to,
				  'Subject' => $subject,
				  'Content-Type'  => 'text/html; charset=UTF-8'
				  );

	$mime_params = array(
	  'text_encoding' => '7bit',
	  'text_charset'  => 'UTF-8',
	  'html_charset'  => 'UTF-8',
	  'head_charset'  => 'UTF-8'
	);
				  
	$mime = new Mail_mime(array('eol' => $crlf));
	$mime->setHTMLBody($html);	
	$mime->addAttachment($file, 'application/octet-stream', $name = $attachment_name, $charset = 'UTF-8', $encoding = 'base64');

	#$body = $mime->get();
	$body = $mime->get($mime_params);
	$hdrs = $mime->headers($hdrs);

	$smtp = Mail::factory('smtp', array(
		/*
		'host' => 'ssl://smtp.gmail.com',
		'port' => '465',
		'auth' => true,
		'username' => 'eda.adalisk@gmail.com',
		'password' => 'adalisk.eda'
		*/
		
		'host' => 'ssl://smtp.yandex.ru',
		'port' => '465',
		'auth' => true,
		'username' => $GLOBALS['send_email_from'],
		'password' => $GLOBALS['send_email_from_pass']

	));
	
	$mail = $smtp->send($mail_to, $hdrs, $body);
}

try_again:

// 1. Get Date.
$date = isset($_GET['date']) ? $_GET['date'] : '';

// 2. Get the Current date if date is empty.
if ($date === '')
{
	$date = date("Y-m-d");
}

// 3. Get UserId.
$user_id = isset($_GET['userId']) ? $_GET['userId'] : '';

// 4. Set correct DB encoding.
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);

// 5. Get User Surname.
$sql = "SELECT Name, Surname FROM $table_users WHERE Id = $user_id";
$result = $conn->query($sql);
$name = '';
$surname = '';
 while ($row = $result->fetch_assoc()) {
	$name = $row["Name"];
	$surname = $row["Surname"];
}

// 5. Get User Email.
$sql = "SELECT Login FROM $table_users WHERE Id = $user_id";
$result = $conn->query($sql);
$email = '';
 while ($row = $result->fetch_assoc()) {
	$email = $row["Login"];
}

// 6. Get ExcelId by Date (Date is equal to date of opened UI tab by User)
$sql = "SELECT DISTINCT ExcelId FROM $table_food WHERE Date = '$date'";
$result = $conn->query($sql);
$excel_id = null;
 while ($row = $result->fetch_assoc()) {
	$excel_id = $row["ExcelId"];
}
// 7. Get all Dates by ExcelId
$sql = "SELECT DISTINCT Date FROM $table_food WHERE ExcelId = '$excel_id'";
$result = $conn->query($sql);
$dates = '';
 while ($row = $result->fetch_assoc()) {
	$dates .= '"' . $row["Date"] . '", ';
}
$dates = trim($dates); # remove space at the end of string
$dates = trim($dates, ",");  # remove comma at the end of string

// 8. Find all ExcelId's by Date's
$sql = "SELECT DISTINCT ExcelId FROM $table_food WHERE Date IN ($dates)";
$result = $conn->query($sql);
$excel_ids = array();
 while ($row = $result->fetch_assoc()) {
	 
	 // 7. Check if User order anything in this Excel.
	$sql1 = "SELECT $table_orders.MenuItemId FROM $table_orders
			JOIN $table_users ON $table_orders.UserId = $table_users.Id
			JOIN $table_food ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_users.Id = $user_id AND $table_food.ExcelId = '" . $row["ExcelId"] . "'";
	//print $sql1;
	$result1 = $conn->query($sql1);	
	
	if ($result1->num_rows > 0)
	{
		array_push($excel_ids, $row["ExcelId"]);
	}
}

$files_inside_zip = array();

// 8. If User do not make order at all.
if (count($excel_ids) == 0)
{
	$_SESSION['modal_title'] = 'Письмо не отправлено';
	$_SESSION['modal_text'] = 'Письмо не отправлено. Вы пока ничего не заказали.';
	header("location:menu.php?date=$date");
}
else {
	// 9. Loop over ExcelId's.
	foreach ($excel_ids as $excel_id) {

		// 10. Get Dates range.
		$dates = array();
		$sql = "SELECT DISTINCT Date FROM $table_food WHERE ExcelId = '$excel_id' ORDER BY Date ASC";
		$result = $conn->query($sql);
		while ($row = $result->fetch_assoc()) {
			array_push($dates, $row["Date"]);
		}
		
		/*
		print ($sql);
		print('<br>');
		print_r ($dates);
		exit();
		*/
		
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
		
		// 13. Generate new file name
		$newfile = './upload/' . rus2translit($surname) . '_' . rus2translit($company) . '_' . $week_number . '.xls';
		array_push($files_inside_zip, $newfile);
		
		// 14. Delete new file if exist
		if (file_exists($newfile)) {
				unlink($newfile);
		}
		
		// 15. Create new Excel File
		$excel_id = mb_convert_encoding($excel_id, "Windows-1251");
		if (!copy($excel_id, $newfile)) {
			echo "Ошибка копирования файла $excel_id в файл $newfile";
		}
		else {
			
			// 16. Generate Excel File with Python Script
			exec("C:/Python34/python ../cgi-bin/ParseAndAggregate.cgi aggregate \"" . $excel_id . "\" " . $user_id . " \"" . $newfile . "\"");	
				
			// 17. Send Email
			if (file_exists($newfile)) {
				send_email($company, $newfile, $name, $surname, $dates, $email, $week_number);
			
				// 18. Display success popup
				popup_success($email, $date);
			}
			else {
				print 'Файл "' . $newfile . '" не отправлен! Количество Excel Ids:"' . count($excel_ids) . '"<br>';
				goto try_again;
			}
		}
	}
}

// 20. Close DB connection.
$conn->close();

?>