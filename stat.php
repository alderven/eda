<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

print '<body ng-app="app" ng-controller="menuCtrl as vm">';

# 1. Display Navigation Bar
print $navigationBar;

print '<div align="center"><h1>Статистика</h1></div><br><br>';

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