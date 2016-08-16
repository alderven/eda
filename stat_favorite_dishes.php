<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
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
		"order": [[ 4, 'desc' ]]
    } );
} );
</script>

<body ng-app="app" ng-controller="menuCtrl as vm">
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
<?php

// 1. Find User RoleId.
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);
$sql = "SELECT RoleId FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	$role_id = $row["RoleId"];
    }

// 2. Display Admin Options depending on the User RoleId.
if ($role_id == 0 or $role_id == 1) {
	print  '<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Администрирование<span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="admin_upload_excel.php"><span class="glyphicon glyphicon-cloud-upload"></span> Загрузка Меню на сервер</a></li>
					<li><a href="admin_download_excel.php"><span class="glyphicon glyphicon-cloud-download"></span> Скачивание заполненного Меню</a></li>
				</ul>
			</li>';
}
?>
	</ul>
	<ul class="nav navbar-nav navbar-right">

<?php
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

<div align="center"><h1>Любимые блюда</h1></div>

<br><br>

 <?php

// 4. Get UserId.
$sql = "SELECT Id FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$user_id = $row["Id"];
}

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