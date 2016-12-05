<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
$_SESSION['ReferURL'] = $_SERVER['REQUEST_URI'];
header("location:index.php");
}
require_once "config.php";

# Check for Admin priveleges
if ($role_id > 1) {
	header("location:menu.php");
}

function customers_count($conn, $excel_id) {
	$sql = "SELECT DISTINCT users.Surname FROM users
			JOIN orders ON orders.UserId = users.Id
			JOIN food ON food.Id = orders.MenuItemId
			WHERE food.ExcelId = " . $excel_id . " ORDER BY users.Surname ASC";
	$result = $conn->query($sql);
	$customers = array();
	while ($row = $result->fetch_assoc()) {
		array_push($customers, $row["Surname"]);
	}
	
	return $customers;
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
	
	return $dishes_count;
}

function total_price($conn, $excel_id) {
	$sql = "SELECT SUM(food.Price * orders.Count) as Sum FROM food
			INNER JOIN orders
			ON food.Id = orders.MenuItemId
			WHERE food.ExcelId = $excel_id";
	$result = $conn->query($sql);
	$sum = 0;
	while ($row = $result->fetch_assoc()) {
		$sum  = $row["Sum"];
	}
	
	return $sum;
}

?>

<!-- https://datatables.net/ -->
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css">
<script>
$(document).ready(function() {
    $('#example').DataTable( {
        "pagingType": "full_numbers",
		"language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.11/i18n/Russian.json"
            },
		"order": [[ 0, 'desc' ]]
    } );
} );
</script>

<body ng-app="app" ng-controller="menuCtrl as vm">
<div class="container">

<?php
# 1. Display Navigation Bar
print $navigationBar;

print '<div align="center"><h1>Управление Excel файлами</h1></div><br><br>';

# 2. Display Upload Excel status
# print $_SESSION['alert_type'] . $_SESSION['alert_text'];
if (isset($_SESSION['alert_type']) and isset($_SESSION['alert_text'])) {
	print '<div align="center" class="alert alert-' . $_SESSION['alert_type'] . '">' . $_SESSION['alert_text'] . '</div>';
	unset($_SESSION['alert_type']);
	unset($_SESSION['alert_text']);
}

print '
<div class="row">

	<div class="col-sm-3" align="center">
		<button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-cloud-upload"></span> Загрузить Excel</button>
		
		<!-- Modal -->
		<div id="myModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Загрузить Excel файл</h4>
					</div>
					<div class="modal-body" align="center">
						<form action="upload.php" method="post" enctype="multipart/form-data">
							<table>
								<tr>
									<td><input type="file" class="btn btn btn-default" name="uploadfile"></td>
									<td><button type="submit" class="btn btn btn-success"><span class="glyphicon glyphicon-cloud-upload"></span> Загрузить Excel</button></td>
								</tr>
							</table>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	
	<form action="download_aggregated_excel.php" target="_blank" method="post">
	
	<div class="col-sm-3" align="center">
		<button type="submit" name="excel_file" class="btn btn-primary" disabled><span class="glyphicon glyphicon-cloud-download"></span> Скачать агрегированный Excel</button>
	</div>
	
	<div class="col-sm-3" align="center">
		<button type="submit" formaction="ludmila.php" name="excel_file" class="btn btn-primary" disabled><span class="glyphicon glyphicon-print"></span> Распечатка для Людмилы</button>
	</div>
	
	<div class="col-sm-3" align="center">
		<button type="submit" formaction="excel_delete.php" name="excel_file" class="btn btn-danger" disabled><span class="glyphicon glyphicon-trash"></span> Удалить Excel</button>
	</div>
	
<br><br><hr>';

# 2. Generate table header
print '
<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th><div align="center">Даты</div></th>
			<th><div align="center">Компания</div></th>
			<th><div align="center">Стоимость заказа</div></th>
			<th><div align="center">Всего блюд заказано</div></th>
			<th><div align="center">Количество заказчиков</div></th>
			<th><div align="center">Заказчики</div></th>
			<th><div align="center">Дата загрузки файла</div></th>
			<th><div align="center">Загрузчик файла</div></th>
			<th><div align="center">Выбор файла</div></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><div align="center">Даты</div></th>
			<th><div align="center">Компания</div></th>
			<th><div align="center">Стоимость заказа</div></th>
			<th><div align="center">Всего блюд заказано</div></th>
			<th><div align="center">Количество заказчиков</div></th>
			<th><div align="center">Заказчики</div></th>
			<th><div align="center">Дата загрузки файла</div></th>
			<th><div align="center">Загрузчик файла</div></th>
			<th><div align="center">Выбор файла</div></th>
		</tr>
	</tfoot>
	<tbody>';


$sql = "SELECT excel.Id, excel.Dates, excel.Company, excel.UploadDate, users.Name, users.Surname
		FROM excel
		INNER JOIN users
		ON excel.UploadedBy = users.Id";
$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	
	$price = total_price($conn, $row["Id"]);
	$customers = customers_count($conn, $row["Id"]);
	$dishes_count = ordered_dishes_count($conn, $row["Id"]);
	
	print '<tr>
		<td align="center" width="80">' . str_replace(",", "<br>", $row["Dates"]) . '</td>
		<td align="center">' . $row["Company"] . '</td>
		<td align="center">' . $price . '</td>
		<td align="center">' . $dishes_count . '</td>
		<td align="center">' . count($customers) . '</td>
		<td align="left">' . implode("<br>", $customers) . '</td>
		<td align="center">' . $row["UploadDate"] . '</td>
		<td align="center">' . $row["Name"] . ' ' . $row["Surname"] . '</td>
		<td class="vert-align"><div align="center"><div class="radio"><label><input type="radio" checked="checked" value="' . $row["Id"] . '"name="ExcelId"></label></div></div></td>
	</tr>';
 }
	

# 6. Generate HTML table footer
print '</tbody>
		</table>
	</div>
</form>';

require_once "footer.php";
 ?>