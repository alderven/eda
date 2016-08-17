<?php
require_once "header.php";
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
?>

<body>
<?php
# 1. Display Navigation Bar
print $navigationBar;

print '<div align="center"><h1>Загрузка Меню на сервер</h1></div><br><br>';

// Каталог, в который мы будем принимать файл:
$uploaddir = './upload/';
$uploadfile_cyr = $uploaddir.basename($_FILES['uploadfile']['name']);
$uploadfile_trnslt = rus2translit($uploadfile_cyr);
$uploadfile = str_replace('.xls', '', $uploadfile_trnslt) . '_' .  date('YmdHis') . '.xls';
if (filesize($_FILES['uploadfile']['tmp_name']) == 0) {
	print '<div class="alert alert-danger" role="alert">Вы пытаетесь загрузить пустой файл.</div>';
}

// Копируем файл из каталога для временного хранения файлов:
else if (copy($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
	exec("C:/Python34/python ../cgi-bin/ParseAndAggregate.cgi parse " . "\"" . $uploadfile . "\"");
	
	// Get Total Dishes
	$sql = "SELECT COUNT(Id) as Total FROM food WHERE ExcelId = '$uploadfile'";
	if ($result = $conn->query($sql)) {
		
		print '<div class="alert alert-success" role="alert">Файл успешно загружен на сервер!</div>';
		
		$total_dishes = 0;
		while ($row = $result->fetch_assoc()) {
			$total_dishes = $row["Total"];
		}
		
		// Get Company Names
		$sql = "SELECT DISTINCT Company FROM food WHERE ExcelId = '$uploadfile'";
		$result = $conn->query($sql);
		$company = 0;
		while ($row = $result->fetch_assoc()) {
			$company = $row["Company"];
		}

		// Get Dates range
		$sql = "SELECT DISTINCT Date FROM food WHERE ExcelId = '$uploadfile' ORDER BY Date";
		$result = $conn->query($sql);
		$dates = array();
		while ($row = $result->fetch_assoc()) {
			array_push($dates, $row["Date"]);
		}

		// Print upload results
		print '<p><b>Компания: </b>' . $company . '</p>';
		print '<p><b>Всего блюд: </b>' . $total_dishes . '</p>';
		print '<p><b>Даты: </b></p><pre>';
		print_r($dates);
		print '</pre>';
	}
	else {
		print '<div class="alert alert-danger" role="alert">В базе данных не обнаружено новых блюд.</div>';
	}
}
else {
	print '<div class="alert alert-danger" role="alert">Ошибка копирования файла из временной директории.</div>';
}

require_once "footer.php";
?>