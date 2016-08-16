<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
?>

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

<div align="center"><h1>Статистика</h1></div>

<br><br>

 <?php

// 4. Get UserId.
$sql = "SELECT Id FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$user_id = $row["Id"];
}

// 5. Get the orders count.
$sql = "SELECT * FROM $table_orders	WHERE UserId = $user_id";
$orders_count = 0;
if ($result = $conn->query($sql)) {
    $orders_count = $result->num_rows;
}

// 6. Get the Days Count.
$sql = "SELECT DISTINCT Date FROM $table_food
		JOIN $table_orders ON $table_food.Id = $table_orders.MenuItemId
		WHERE UserId = $user_id";
$days_count = 0;
if ($result = $conn->query($sql)) {
	$days_count = $result->num_rows;
}

if ($orders_count == 0 or $days_count === 0) {
	print 'К сожалению, Вы ничего не заказывали через наш Сервис, поэтому у нас нет статистики по Вашим заказам.';
}
else
{
	// 7. Get the First Order date.
	$sql = "SELECT Date FROM $table_food
			JOIN $table_orders ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_orders.UserId = $user_id
			ORDER BY Date ASC LIMIT 1";
	$first_order_date = '';
	if ($result = $conn->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$first_order_date = $row["Date"];
		}
	}
	
	// 8. Get the Last Order date.
	$sql = "SELECT Date FROM $table_food
			JOIN $table_orders ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_orders.UserId = $user_id
			ORDER BY Date DESC LIMIT 1";
	$last_order_date = '';
	if ($result = $conn->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$last_order_date = $row["Date"];
		}
	}
	
	// 9. Get the Sum of all orders.
	$sql = "SELECT SUM($table_food.Price) AS Sum FROM $table_food
			JOIN $table_orders ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_orders.UserId = $user_id";
	$sum = 0;
	if ($result = $conn->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$sum = $row["Sum"];
		}
	}
	
	// 10. Calculate average amount of each dish.
	$average_sum_amount = intval($sum / $orders_count);
		
	// 11. Average amount of dishes per day.
	$average_dishes_amount_per_day = intval($orders_count / $days_count);
	
	// 12. Get the Total Weight of all orders.
	$sql = "SELECT SUM($table_food.Weight) AS Sum FROM $table_food
			JOIN $table_orders ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_orders.UserId = $user_id";
	$total_weight = 0;
	if ($result = $conn->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$total_weight = $row["Sum"];
		}
	}
	
	// 10. Find preferable company.
	// 10.1. Calculate dishes ordered in Cimus Company.
	$sql = "SELECT * FROM $table_food
		JOIN $table_orders ON $table_food.Id = $table_orders.MenuItemId
		WHERE UserId = $user_id AND $table_food.Company = 'Цимус'";
	$orders_count_by_company_cimus = 0;
	if ($result = $conn->query($sql)) {
		$orders_count_by_company_cimus = $result->num_rows;
	}
	
	// 10.2. Calculate the percentage.
	$orders_count_by_company_cimus_percents = intval($orders_count_by_company_cimus * 100 / $orders_count);
	$orders_count_by_company_adam_percents = 100 - $orders_count_by_company_cimus_percents;
	
	// 10.3. Calculate the winner Company.
	$preferable_company = '';
	if ($orders_count_by_company_cimus_percents > $orders_count_by_company_adam_percents) {
		$preferable_company = 'Цимус (' . $orders_count_by_company_cimus_percents . '%)';
	}
	else if ($orders_count_by_company_cimus_percents < $orders_count_by_company_adam_percents) {
		$preferable_company = 'Адам (' . $orders_count_by_company_adam_percents . '%)';
	}
	else {
		$preferable_company = 'Вы заказываете в обеих компаниях одинаково.';
	}
	
	// 11. Preferable food type.
	$sql = "SELECT $table_food.Type, count($table_food.Type) AS TypeCount FROM $table_food
			JOIN $table_orders ON $table_food.Id = $table_orders.MenuItemId
			WHERE $table_orders.UserId = $user_id
			GROUP by $table_food.Type
			ORDER BY TypeCount DESC LIMIT 1";
	$most_preferable_food_type = '';
	if ($result = $conn->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$most_preferable_food_type = $row["Type"];
		}
	}
	
	// 12. Print Table with Stat.
	print '
			<table class="table table-hover">
				<tbody>
					<tr>
					  <th>Всего блюд заказано:</th>
						<td>' . $orders_count . '</td>
					</tr>
					<tr>
					  <th>Стоимость всех заказанных блюд (руб.):</th>
						<td>' . $sum . ' </td>
					</tr>
					<tr>
					  <th>Средняя стоимость блюда (руб.):</th>
						<td>' . $average_sum_amount . '</td>
					</tr>
					<tr>
					  <th>Дата первого заказа:</th>
						<td>' . $first_order_date . '</td>
					</tr>
					<tr>
					  <th>Дата последнего заказа:</th>
						<td>' . $last_order_date . '</td>
					</tr>
					<tr>
					  <th>Сколько дней Вы делали заказы:</th>
						<td>' . $days_count . '</td>
					</tr>
					<tr class>
					  <th>Среднее количество заказываемых блюд в день:</th>
						<td>' . $average_dishes_amount_per_day . '</td>
					</tr>
					<tr class>
					  <th>Общий вес всех блюд (без учета блюд от Адама) (гр):</th>
						<td>' . $total_weight . '</td>
					</tr>
					<tr class>
					  <th>Предпочитаемый поставщик:</th>
						<td>' . $preferable_company . '</td>
					</tr>
					<tr class>
					  <th>Самый заказываемый тип блюда:</th>
					  <td>' . $most_preferable_food_type . '</td>
					</tr>
				</tbody>
			</table>';
}
require_once "footer.php";
 ?>