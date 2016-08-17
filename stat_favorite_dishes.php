<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
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
		"order": [[ 4, 'desc' ]]
    } );
} );
</script>

<body ng-app="app" ng-controller="menuCtrl as vm">
<div class="container">

 <?php
# 1. Display Navigation Bar
print $navigationBar;

print '<div align="center"><h1>Любимые блюда</h1></div><br><br>';

// 5. Generate table header
print '
<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>Категория</th>
			<th>Блюдо</th>
			<th>Цена</th>
			<th>Компания</th>
			<th>Количество заказов</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Категория</th>
			<th>Блюдо</th>
			<th>Цена</th>
			<th>Компания</th>
			<th>Количество заказов</th>
		</tr>
	</tfoot>
	<tbody>';

// 6. Get all orders
$sql = "SELECT Type, Name, Price, Company, COUNT(*) AS Total FROM $table_food
		JOIN $table_orders ON food.Id = orders.MenuItemId
		WHERE orders.UserId = $user_id
        GROUP BY Name
		HAVING ( COUNT(Name) > 2 )";
if ($result = $conn->query($sql)) {
	while ($row = $result->fetch_assoc()) {
	
	// 7. Generate table body
	print '<tr>
		<td>' . $row["Type"] . '</td>
		<td>' . $row["Name"] . '</td>
		<td>' . $row["Price"] . '</td>
		<td>' . $row["Company"] . '</td>
		<td>' . $row["Total"] . '</td>
	</tr>';
		
	}
}

// 8. Generate table footer
print '</tbody>
</table>';

require_once "footer.php";
 ?>