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
		"order": [[ 3, 'desc' ]]
    } );
} );
</script>

<body ng-app="app" ng-controller="menuCtrl as vm">
<div class="container">

<?php
# 1. Display Navigation Bar
print $navigationBar;

print '<div align="center"><h1>Статистика по стоимости заказов</h1></div><br><br>';

# 2. Generate table header
print '
<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th><div align="center">Сотрудник</div></th>
			<th><div align="center">Количество дней</div></th>
			<th><div align="center">Стоимость всех заказов (руб.)</div></th>
			<th><div align="center">Средняя стоимость заказа в день (руб.)</div></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><div align="center">Сотрудник</div></th>
			<th><div align="center">Количество дней</div></th>
			<th><div align="center">Стоимость всех заказов (руб.)</div></th>
			<th><div align="center">Средняя стоимость заказа в день (руб.)</div></th>
		</tr>
	</tfoot>
	<tbody>';

# 3. Get all users Id's
$ids = array();
$sql = "SELECT Id FROM users";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$ids[] = $row["Id"];	
}

# 4. Get raw stat from SQL for every user
foreach ($ids as &$id) {
    $sql = "SELECT users.Name as Name, users.Surname as Surname, COUNT(DISTINCT food.Date) AS Days, SUM(food.Price) as Sum
	FROM food
	INNER JOIN orders
	ON food.Id = orders.MenuItemId
	INNER JOIN users
	ON users.Id = orders.UserId
	WHERE users.Id = $id";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
	
		$average_sum = $row["Days"] == 0 ? 0 : (round($row["Sum"] / $row["Days"]));

		# 5. Generate HTML table
		print '<tr>
			<td align="right">' . $row["Name"] . ' ' . $row["Surname"] . '</td>
			<td align="right">' . $row["Days"] . '</td>
			<td align="right">' . $row["Sum"] . '</td>
			<td align="right">' . $average_sum . '</td>
		</tr>';
		
	}
}

# 6. Generate HTML table footer
print '</tbody>
</table>';

require_once "footer.php";
 ?>