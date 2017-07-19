<?php
require_once "config.php";

session_start();

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

try_again:

// 1. Get Date.
$date = isset($_GET['date']) ? $_GET['date'] : '';

// 2. Get the Current date if date is empty.
if ($date === '')
{
	$date = date("Y-m-d");
}

// 5. Get ExcelId by Date.
$sql = "SELECT DISTINCT ExcelId FROM $table_food WHERE Date = '$date'";
$result = $conn->query($sql);
$excel_ids = array();
 while ($row = $result->fetch_assoc()) {
	 
	 // 6. Check if User order anything in this Excel.
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

// 6. If User do not make order at all.
if (count($excel_ids) == 0)
{
	//print 'Вы ничего пока не заказали.';
	$_SESSION['modal_title'] = 'Скачивание Excel файла';
	$_SESSION['modal_text'] = 'Вы пока ничего не заказали.';
	header("location:menu.php?date=$date");
}
else {
	// 6. Loop over ExcelId's.
	foreach ($excel_ids as $excel_id) {

		// 7. Get Dates range.
		$dates = array();
		$sql = "SELECT DISTINCT Date FROM $table_food WHERE ExcelId = '$excel_id' ORDER BY Date ASC";
		$result = $conn->query($sql);
		while ($row = $result->fetch_assoc()) {
			array_push($dates, $row["Date"]);
		}
		
		// 8. Get Company Name.
		$company = '';
		$sql = "SELECT DISTINCT Company FROM $table_food WHERE ExcelId = '$excel_id'";
		$result = $conn->query($sql);
		$company = '';
		while ($row = $result->fetch_assoc()) {
			$company =  $row["Company"];
		}
		
		// 9. Generate new File Name.
		$newfile = './upload/' . $surname . '_' . $company . '_' . $dates[0] . '_' . $dates[count($dates)-1] . '.xls';
		array_push($files_inside_zip, $newfile);
		
		// 10. Call Python Script.
		exec("\"C:/Program Files/Python36/python.exe\" ../cgi-bin/ParseAndAggregate.cgi aggregate " . $excel_id . " " . $user_id);
		
		$tmp_file = $excel_id . '.xls';
		
		// 12. Copy Excel File.
		if (file_exists($tmp_file)) {
			if (!copy($tmp_file, $newfile)) {
				echo "Ошибка копирования файла $excel_id...\n";
			}
			else
			{
				// 13. Send file for Download.
				if (file_exists($newfile) and count($excel_ids) == 1) {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="'.basename($newfile).'"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($newfile));
					readfile($newfile);
					exit;
				}
			}
		}
		else {
			goto try_again;
			//print 'Не удалось сгенерировать Excel файл. Попробуйте еще раз.';
		}
	}
}

// 14. Close DB connection.
$conn->close();

// 15. If Users order in the two Companies at the same time.
if (count($excel_ids) == 2) {
	
	// 15. 1. Delete file if it already exist.
	$zip_file = './upload/' . $surname . '.zip';
	if (file_exists($zip_file)) {
		unlink($zip_file);
	}
	
	// 15.2 Generate ZIP File.
	$zip = new ZipArchive();
	if ($zip->open($zip_file, ZipArchive::CREATE)!==TRUE) {
		exit("Невозможно открыть <$zip_file>\n");
	}

	# Latin names in attachment.
	$filename_0 = rus2translit(basename($files_inside_zip[0])) . '.xls';
	$filename_1 = rus2translit(basename($files_inside_zip[1])) . '.xls';
	$zip->addFile($files_inside_zip[0], $filename_0);
	$zip->addFile($files_inside_zip[1], $filename_1);

	# Cyryllic names in attachment. Bug in this solution.
	#$zip->addFile($files_inside_zip[0], basename($files_inside_zip[0]));
	#$zip->addFile($files_inside_zip[1], basename($files_inside_zip[1]));


	$zip->close();
	
	// 15.3 Prepare ZIP File for downloading.
	if (file_exists($zip_file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($zip_file).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($zip_file));
		readfile($zip_file);
		exit;
	}
	else {
		print 'ZIP файл не найден.';
	}
}

?>