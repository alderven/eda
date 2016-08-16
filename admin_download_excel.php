<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
?>
<body>
<div class="container">

<!-- Static navbar -->
<nav class="navbar navbar-default">
<div class="container-fluid">
  <div class="navbar-header">
	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
	  <span class="sr-only">Toggle navigation</span>
	  <span class="icon-bar"></span>
	  <span class="icon-bar"></span>
	  <span class="icon-bar"></span>
	</button>
	<a class="navbar-brand" href="menu.php"><span class="glyphicon glyphicon-cutlery"></span> Сервис "Еда"</a>
  </div>
  <div id="navbar" class="navbar-collapse collapse">
	<ul class="nav navbar-nav">
		<li><a href="menu.php"><span class="glyphicon glyphicon-list-alt"></span> Меню</a></li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Настройки<span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="presettings.php"><span class="glyphicon glyphicon-filter"></span> Ручной фильтр</a></li>
				<li><a href="settings_autofill.php"><span class="glyphicon glyphicon-random"></span> Автозаполнение</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Статистика<span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="stat.php"><span class="glyphicon glyphicon-stats"></span> Общая статистика</a></li>
				<li><a href="stat_orders_history.php"><span class="glyphicon glyphicon-calendar"></span> История заказов</a></li>
				<li><a href="stat_favorite_dishes.php"><span class="glyphicon glyphicon-heart"></span> Любимые блюда</a></li>
			</ul>
		</li>
		<li><a href="android.php"><span class="glyphicon glyphicon-phone"></span> Android</a></li>
		<li><a href="api.php"><span class="glyphicon glyphicon-transfer"></span> API</a></li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Администрирование<span class="caret"></span></a>
			<ul class="dropdown-menu">
			<li><a href="admin_upload_excel.php"><span class="glyphicon glyphicon-cloud-upload"></span> Загрузка Меню на сервер</a></li>
			<li><a href="admin_download_excel.php"><span class="glyphicon glyphicon-cloud-download"></span> Скачивание заполненного Меню</a></li>
			</ul>
		</li>
	</ul>
	<ul class="nav navbar-nav navbar-right">

<?php
// Set correct DB encoding.
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);

$sql = "Select Name, Surname FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
$result = $conn->query($sql);
$userName = NULL;
while ($row = $result->fetch_assoc()) {
	$userName = $row["Name"] . " " . $row["Surname"];
}
print  '<li class="dropdown">
			<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> ' . $userName . ' <span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="change_password.php"><span class="glyphicon glyphicon-cog"></span> Сменить пароль</a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
			</ul>
		</li>';
?>
	  
	</ul>
  </div><!--/.nav-collapse -->
</div><!--/.container-fluid -->
</nav>

<div align="center"><h1>Скачивание заполненного Меню</h1></div>
<br>
<div class="row">
	<form action="download_aggregated_excel.php" target="_blank" method="post">
		<div align="right" class="col-sm-6"><button type="submit" name="excel_file" class="btn btn-primary"><span class="glyphicon glyphicon-cloud-download"></span> Скачать Excel</button></div>
		<div align="left" class="col-sm-6"><button type="submit" formaction="ludmila.php" name="excel_file" class="btn btn-warning"><span class="glyphicon glyphicon-print"></span> Создать распечатку для Людмилы</button></div>
</div>
<br>
<!--
<form action="ludmila.php" method="post">
    <div align="right"><button type="submit" name="excel_file" class="btn btn-success"><span class="glyphicon glyphicon-cloud-download"></span> Сагрегировать и скачать</button>
<br><br>
-->
<table class="table table-hover">
	<tr>
	<td align="center"><strong>Даты</strong></td>
	<td align="center"><strong>Заказ сделали</strong></td>
	<td align="center"><strong>Компания</strong></td>
	<td align="center"><strong>Выберите файл</strong></td>
</tr>
<?php
$sql = "SELECT DISTINCT ExcelId FROM $table_food ORDER BY Date DESC";
$excel_ids = $conn->query($sql);
 while ($row = $excel_ids->fetch_assoc()) {
	$excel_id = $row["ExcelId"];
	
	// Find Dates.
	$sql = "SELECT DISTINCT Date FROM $table_food WHERE ExcelId = '" . $excel_id . "'";
	$dates = $conn->query($sql);
	$date = array();
	while ($row = $dates->fetch_assoc()) {
		array_push($date, $row["Date"]);
	}
		
	// Find Company Name.
	$sql = "SELECT DISTINCT Company FROM $table_food WHERE ExcelId = '" . $excel_id . "'";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$company = $row["Company"];
	}
		
	// Find People.
	$sql = "SELECT DISTINCT $table_users.Surname FROM $table_users
			JOIN $table_orders ON $table_orders.UserId = $table_users.Id
			JOIN $table_food ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_food.ExcelId = '" . $excel_id . "' ORDER BY $table_users.Surname ASC";
	$result = $conn->query($sql);
	$surname = '';
	while ($row = $result->fetch_assoc()) {
		
		$surname .= $row["Surname"] . "<br>";
	}	
	
	// Create Table content.
	print '	<tr>
			<td class="vert-align"><div align="center">' . $date[0] . ' — ' . $date[count($date)-1] . '</td></div>
			<td class="vert-align">' . $surname . '</td>
			<td class="vert-align"><div align="center">' . $company . '</td></div>
			<td class="vert-align"><div align="center"><div class="radio"><label><input type="radio" checked="checked" value="' . $excel_id . '"name="optradio"></label></div></div></td>
			<!--<td class="vert-align"><div align="center"><input type="submit" name="excel_file" class="btn btn-success" value="' . $excel_id . '" /></div></td>-->
			</tr>';
 }
print '</table></form>';
$conn->close();

require_once "footer.php";
?>