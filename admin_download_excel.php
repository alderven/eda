<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

print '<body>';

# 1. Display Navigation Bar
print $navigationBar;
?>

<div align="center"><h1>Скачивание заполненного Меню</h1></div>
<br>
<div class="row">
	<form action="download_aggregated_excel.php" target="_blank" method="post">
		<div align="right" class="col-sm-6"><button type="submit" name="excel_file" class="btn btn-primary"><span class="glyphicon glyphicon-cloud-download"></span> Скачать Excel</button></div>
		<div align="left" class="col-sm-6"><button type="submit" formaction="ludmila.php" name="excel_file" class="btn btn-warning"><span class="glyphicon glyphicon-print"></span> Создать распечатку для Людмилы</button></div>
</div>
<br>
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
	
	// Find Dates
	$sql = "SELECT DISTINCT Date FROM $table_food WHERE ExcelId = '" . $excel_id . "'";
	$dates = $conn->query($sql);
	$date = array();
	while ($row = $dates->fetch_assoc()) {
		array_push($date, $row["Date"]);
	}
		
	// Find Company Name
	$sql = "SELECT DISTINCT Company FROM $table_food WHERE ExcelId = '" . $excel_id . "'";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$company = $row["Company"];
	}
		
	// Find People
	$sql = "SELECT DISTINCT $table_users.Surname FROM $table_users
			JOIN $table_orders ON $table_orders.UserId = $table_users.Id
			JOIN $table_food ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_food.ExcelId = '" . $excel_id . "' ORDER BY $table_users.Surname ASC";
	$result = $conn->query($sql);
	$surname = '';
	while ($row = $result->fetch_assoc()) {
		
		$surname .= $row["Surname"] . "<br>";
	}	
	
	// Create Table content
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