<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
require_once "common.php";

# Check for Admin priveleges
is_admin($role_id);


function user_stats($conn, $user_id) {
	$sql = "SELECT COUNT(DISTINCT food.Date) AS DaysCount, SUM(food.Price) as Sum
			FROM food
			INNER JOIN orders
			ON food.Id = orders.MenuItemId
			INNER JOIN users
			ON users.Id = orders.UserId
			WHERE users.Id = $user_id";
	$result = $conn->query($sql);
	$dishes_count = 0;
	while ($row = $result->fetch_assoc()) {
		$days_count = $row["DaysCount"];
		$sum = $row["Sum"];
		$sum_average = $row["DaysCount"] == 0 ? 0 : (round($row["Sum"] / $row["DaysCount"]));
	}
	
	return array($days_count, $sum, $sum_average);
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
		//"order": [[ 0, 'asc' ]]
		   'aoColumnDefs': [{
				'bSortable': false,
				'aTargets': [-1] /* 1st one, start by the right */
			}]
    } );
} );
</script>

<body ng-app="app" ng-controller="Users as vm">
<div class="container">

<?php

# Display Navigation Bar
print $navigationBar;

# Display Page Title
print '<div align="center"><h1>Управление пользователями</h1></div><br><br>';

# Display Alerts
if (isset($_SESSION['alert_type']) and isset($_SESSION['alert_text'])) {
	print '<div align="center" class="alert alert-' . $_SESSION['alert_type'] . '">' . $_SESSION['alert_text'] . '</div>';
	unset($_SESSION['alert_type']);
	unset($_SESSION['alert_text']);
}

# Display Buttons
print '
<div class="row">

	<div class="col-sm-4" align="center">
		<button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus"></span> Добавить нового пользователя</button>
		
		<!-- Modal -->
		<div id="myModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Добавление нового пользователя</h4>
					</div>
					<div class="modal-body" align="center">
						<form class="form-signin" action="admin.user.add.php" method="post">
						
							<label for="email" class="sr-only">Email address</label>
							<input type="email" name="email" id="email" class="form-control" placeholder="Адрес электронной почты" required autofocus>
							
							<label for="name" class="sr-only">Имя</label>
							<input type="string" name="name" id="name" class="form-control" placeholder="Имя" required autofocus>

							<label for="surname" class="sr-only">Фамилия</label>
							<input type="string" name="surname" id="surname" class="form-control" placeholder="Фамилия" required autofocus>
														
							<button class="btn btn btn-success btn-block" type="submit"><span class="glyphicon glyphicon-plus"></span> Добавить нового пользователя</button>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	
	<form action="admin.user.login_as.php" method="post">
		<div class="col-sm-4" align="center">
			<button type="submit" formaction="admin.user.login_as.php" name="UserId" class="btn btn-info"><span class="glyphicon glyphicon-user"></span> Войти как пользователь</button>
			</div>
		<div class="col-sm-4" align="center">
			<button type="submit" formaction="admin.user.activate.php" name="UserId" class="btn btn-warning"><span class="glyphicon glyphicon-off"></span> Активировать\деактивировать пользователя</button>
	</div>
	
<br><br><hr>';

# Display Table header
print '
<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>Имя</th>
			<th>Электронный адрес</th>
			<th>Роль</th>
			<th>Состояние</th>
			<th>Активность<br>(дней)</th>
			<th>Стоимость<br>всех заказов<br>(руб.)</th>
			<th>Средняя<br>стоимость<br>заказа<br>(руб./день)</th>
			<th>Выбрать</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Имя</th>
			<th>Электронный адрес</th>
			<th>Роль</th>
			<th>Состояние</th>
			<th>Активность<br>(дней)</th>
			<th>Стоимость<br>всех заказов<br>(руб.)</th>
			<th>Средняя<br>стоимость<br>заказа<br>(руб./день)</th>
			<th>Выбрать</th>
		</tr>
	</tfoot>
	<tbody>';

# Display Table content
$sql = "SELECT users.Id, users.Login, users.isActive, users.Name, users.Surname, roles.Name as RoleName FROM users
		INNER JOIN roles ON
		users.roleId = roles.Id
		WHERE users.CompanyId = $company_id
		ORDER BY Surname DESC";

$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	
	$status = ($row["isActive"] == 1) ? 'Активный' : 'Деактивирован';
	$stat = user_stats($conn, $row["Id"]);
	
	print '<tr>
		<td align="left">' . $row["Surname"] . ' ' . $row["Name"]  . '</td>
		<td align="left">' . $row["Login"]  . '</td>
		<td align="left">' . $row["RoleName"]  . '</td>
		<td align="left">' . $status  . '</td>
		<td align="right">' . $stat[0] . '</td>
		<td align="right">' . $stat[1] . '</td>
		<td align="right">' . $stat[2] . '</td>
		<td align="center"><label><input type="radio" checked="checked" value="' . $row["Id"] . '"name="UserId"></label></td>
	</tr>';
 }
	
# Display Table footer
print '</tbody>
		</table>
	</div>
</form>';

require_once "footer.php";
 ?>