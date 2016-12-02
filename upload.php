<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

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

# Display Alert on Excel Page
function alert($type, $text) {
	$_SESSION['alert_type'] = $type;
	$_SESSION['alert_text'] = $text;
	header("location:excel.php");
}

# Get Temp File lolocation
$file_tmp_location = $_FILES['uploadfile']['tmp_name'];

# Calculate File Checksum
$checksum = md5_file($file_tmp_location);

$sql = "SELECT Id FROM excel WHERE Checksum = \"$checksum\"";
$result = $conn->query($sql);
if ($result->num_rows == 0) {

	// Каталог, в который мы будем принимать файл:
	$uploaddir = './upload/';
	$original_filename = $_FILES['uploadfile']['name'];
	$uploadfile_cyr = $uploaddir.basename($original_filename);
	$uploadfile_trnslt = rus2translit($uploadfile_cyr);
	$uploadfile = str_replace('.xls', '', $uploadfile_trnslt) . '_' .  date('YmdHis') . '.xls';
	if (filesize($_FILES['uploadfile']['tmp_name']) == 0) {
		# print '<div class="alert alert-danger" role="alert">Вы пытаетесь загрузить пустой файл.</div>';
		alert("danger", "Ошибка загрузки Excel файла. Вы пытаетесь загрузить пустой файл");
	}

	// Копируем файл из каталога для временного хранения файлов:
	else if (copy($file_tmp_location, $uploadfile)) {
			
	
		# Add data into Excel table
		$sql = "INSERT INTO excel (OriginalFileName, FileLocation, Checksum, UploadedBy) VALUES	(\"$original_filename\", \"$uploadfile\", \"$checksum\", $user_id)";
		$result = $conn->query($sql);
		
		# Get Excel Id
		$excel_id = Null;
		$sql = "SELECT Id FROM excel WHERE FileLocation = \"$uploadfile\"";
		$result = $conn->query($sql);
		while ($row = $result->fetch_assoc()) {
			$excel_id = $row["Id"];
		}
		
		# Parse Excel
		exec("C:/Python34/python ../cgi-bin/ParseAndAggregate.cgi parse " . "\"" . $uploadfile . "\" " . $excel_id);
		
		# Get Added Excel info
		$sql = "SELECT * FROM excel WHERE Id = '$excel_id'";
		$result = $conn->query($sql);
		if ($result = $conn->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				alert("success", "Файл успешно загружен на сервер!<br>Компания: " . $row["Company"] . '<br>Количество блюд: ' . $row["DishesCount"] . '<br>Количество дней: ' . $row["DaysCount"] . '<br>Даты: ' . $row["DateFirst"] . ' - ' . $row["DateLast"]);
			}
		}
		else {
			# print '<div class="alert alert-danger" role="alert">В базе данных не обнаружено новых блюд.</div>';
			alert("danger", "Ошибка. В базе данных не обнаружено новых блюд");
		}
	}
	else {
		# print '<div class="alert alert-danger" role="alert">Ошибка копирования файла из временной директории.</div>';
		alert("danger", "Ошибка копирования файла из временной директории");
	}
}
else {
	# print  '<div align="center" class="alert alert-danger" role="alert">Файл не загружен! Ранее уже был загружен файл с такой же контрольной суммой.</div>';
	alert("danger", "Ошибка загрузки. Ранее уже был загружен файл с такой же контрольной суммой");
}
?>