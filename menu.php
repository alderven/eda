<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
require_once "common.php";
?>

<style>

.food-table.table>tbody>tr>td {
	vertical-align: middle;
	padding: 8px;
}

.food-counter {
	width: 20px;
	height: 20px;
	background: #f95050;
	border-radius: 50%;
	text-align: center;
	color: white;
	-webkit-box-shadow: 0 0 1px 1px #af6161;  /* Safari 3-4, iOS 4.0.2 - 4.2, Android 2.3+ */
	-moz-box-shadow:    0 0 1px 1px #af6161;  /* Firefox 3.5 - 3.6 */
	box-shadow:         0 0 1px 1px #af6161;
}



/*
.food-table.table {
	padding: 2px;
	border-collapse: separate;
	border-spacing: 0 1px;
}

.food-table.table>tbody>tr.row-active {
	outline: 2px solid #eca013;
}
*/




</style>

<script>
angular.module('app', [])
	.controller('menuCtrl', function($scope, $http) {
		var vm = this;
		vm.test = "this is a test";
		
		vm.cart = {};

		// Autofill
		vm.autofill = function() {
			var url = 'autofill.php';
			$http.get(url)
				.success(function (response) {
					//alert(response);
					if (response == 0)
					{
						$('#myModal').modal('show');
					}
					else if (response == 1)
					{
						// Update page.
						location.reload();
						//window.location.replace("menu.php");
					}
				})
				.error(function(){
					//console.log("E R R O R");
				});
		};
		
		// Cleanup Order
		vm.cleanupOrders = function() {
			var url = "cleanup_orders.php";
			$http.get(url)
				.success(function (response) {
					location.reload();
				})
				.error(function(){
					//console.log("E R R O R");
				});
		};
		
		// Calculate Price Sum.
		vm.calculatePriceSum = function(date) {
			var url = "CalculatePriceSum.php?date=" + date;
			$http.get(url)
				.success(function (response) {

					result = JSON.stringify(response);
					//alert(result);
					result = JSON.parse(result);
					
					// Update Price Sum.
					document.getElementById("priceSum" + date).innerHTML = result.day_sum + ' руб.';
					
					// Update Total Price Sum.
					document.getElementById("totalPriceForWeek" + result.week_number).innerHTML = 'Всего: ' + result.week_sum + ' руб.';
										
					// Update Average Price Sum.
					document.getElementById("averagePriceForWeek" +  + result.week_number).innerHTML = 'Средн.: ' + result.average + ' руб.';
					
				})
				.error(function(){
					//console.log("E R R O R");
				});
		};
		
		
		
		vm.addToCart = function(id, date) {
			
			vm.cart[id] = vm.cart[id] ? ++vm.cart[id] : 1;
			
			var url = "AddToCart.php?dishId=" + id;
			$http.post(url)
				.success(function (response) {
					
					$("#menuItemCounter" + id).animateCss('bounceIn');
					
					// Update Items Count.
					document.getElementById("menuItemCount" + id).innerHTML = response;

					// Update Price Sum.
					vm.calculatePriceSum(date);
					/*
					var container = document.getElementById("datesPaginator");
					var content = container.innerHTML;
					container.innerHTML= content;
					*/
				})
				.error(function(){
					//console.log("E R R O R");
				});
		};
		
		vm.RemoveFromCart = function(id, buttonColour, date, filter) {
				
			vm.cart[id] = vm.cart[id] ? --vm.cart[id] : 0;
			
			var url = "RemoveFromCart.php?dishId=" + id;
			$http.post(url)
				.success(function (itemsCount) {
										
					// Update Items Count.
					document.getElementById("menuItemCount" + id).innerHTML = itemsCount;
					
					// Update Price Sum.
					vm.calculatePriceSum(date);
					
					if (itemsCount == 0 && filter)
					{
						location.reload();
					}
					
					// Update Price Sum.
					/*
					var container = document.getElementById("datesPaginator");
					var content = container.innerHTML;
					container.innerHTML= content;
					*/
				})
				.error(function(){
					//console.log("E R R O R");
				});
		};	
	});

$('form').submit(function(){
	alert($(this["options"]).val());
    return false;
});

$(window).load(function(){
        $('#ModalNotification').modal('show');
    });

// Tooltip: http://www.w3schools.com/bootstrap/tryit.asp?filename=trybs_tooltip&stacked=h
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});	
</script>

<body ng-app="app" ng-controller="menuCtrl as vm">

<!-- Display Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Автозаполнение</h4>
      </div>
      <div class="modal-body">
        <p>В Вашем меню нет незаполненных дней.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>

  </div>
</div>
<?php

# 1. Display Navigation Bar
print $navigationBar;

# 2. Display Modal if necessary
if (isset($_SESSION['modal_title']) and isset($_SESSION['modal_text'])) {
	print '	<!--<br>modal_title: ' . $_SESSION['modal_title'] . '<br>modal_text:' . $_SESSION['modal_text'] . '<br>-->
			<div id="Modal" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">' . $_SESSION['modal_title'] . '</h4>
						</div>
						<div class="modal-body">
							<p>' . $_SESSION['modal_text'] . '</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				<!--alert("Вы ничего пока не заказали.");-->
				<!--alert(' . $_SESSION['modal_text'] . ');-->
				$("#Modal").modal("show");
			</script>';
unset($_SESSION['modal_title']);
unset($_SESSION['modal_text']);			
}

# 3. Initialize Page title	  
print '<div align="center"><h1>Меню</h1></div>';

# 4. Show Modal with Notifications
$sql = "SELECT ShowNotification FROM $table_users WHERE Id = $user_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$showNotification = $row["ShowNotification"];
}
if ($showNotification == 1) {
	
	# 4.1 Disable displaying notification
	$sql = "UPDATE $table_users SET ShowNotification = 0 WHERE Id = $user_id";
	$result = $conn->query($sql);
	
	# 4.2 Display notification
	print '	<div class="container">
				<div class="modal fade" id="ModalNotification" role="dialog">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Что нового...</h4>
							</div>
							<div class="modal-body">
								<!-- MESSAGE START -->
								<!-- Update 30.10.2016-->
								<div><h3><table class="table"><tr class="info"><td align="center">Версия от 30.10.2016</td></tr></tbody></table></h3></div>
								На странице <a href="menu.php" target="_blank">«Меню»</a> добавлена кнопка «Распечатать заказ»:
								<div align="center"><a href="menu.php" target="_blank"><img src="img\update20161030_printButton.png"></a></div>
								<hr>
								<p>На странице авторизации теперь отображается общее количество заказов:
								<div align="center"><img src="img\update20161030_loginPage.png"></div>
								<hr>
								<!-- MESSAGE END -->
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
							</div>
						</div>
					</div>
				</div>
			</div>';
}

# 5. Get Current Page Date.
$date = isset($_GET['date']) ? $_GET['date'] : '';
error_log('menu.php: date: ' . $date, 0);

# 6. Check, if Filter by Order applied.
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$filter_btn_all_state = '';
$filter_btn_filtered_state = '';

# 7. Check, if Filter by Company applied.
$company = isset($_GET['company']) ? $_GET['company'] : '';
$filter_by_company_cimus = '';
$filter_by_company_adam = '';
$filter_custom = '';

# 8. Set Filter button properties depending on filter
if ($filter === '' and $company === '') {
	$filter_btn_all_state = 'info active';
	$filter_btn_filtered_state = 'default';
	$filter_by_company_cimus = 'default';
	$filter_by_company_adam = 'default';
	$filter_custom = 'default';
}
else if ($filter === 'filtered') {
	$filter_btn_all_state = 'default';
	$filter_btn_filtered_state = 'info active';
	$filter_by_company_cimus = 'default';
	$filter_by_company_adam = 'default';
	$filter_custom = 'default';
}
else if ($filter === 'custom') {
	$filter_btn_all_state = 'default';
	$filter_btn_filtered_state = 'default';
	$filter_by_company_cimus = 'default';
	$filter_by_company_adam = 'default';
	$filter_custom = 'info active';
}
else if ($company === 'Цимус') {
	$filter_btn_all_state = 'default';
	$filter_btn_filtered_state = 'default';
	$filter_by_company_cimus = 'info active';
	$filter_by_company_adam = 'default';
	$filter_custom = 'default';
}
else if ($company === 'Адам') {
	$filter_btn_all_state = 'default';
	$filter_btn_filtered_state = 'default';
	$filter_by_company_cimus = 'default';
	$filter_by_company_adam = 'info active';
	$filter_custom = 'default';
}

# 9. Disable Send button for Test User
$send_button_type = "";
if ($role_id == 3) {
	$send_button_type = ' disabled="disabled"';
}

# 10. Create Buttons Panel.
print  '<div class="container-fluid">
			<div class="panel-footer row">
				<div class="col-sm-7 text-left">
					<form action="?filter=&date=&company=" method="get">
						<div class="btn-group">
							<button type="submit" name="filter" value="" class="btn btn-' . $filter_btn_all_state . '"><span class="glyphicon glyphicon-filter"></span> Без фильтров</button>
							<button type="submit" name="company" value="Цимус" class="btn btn-' . $filter_by_company_cimus . '"><span class="glyphicon glyphicon-filter"></span> Цимус</button>
							<button type="submit" name="company" value="Адам" class="btn btn-' . $filter_by_company_adam . '"><span class="glyphicon glyphicon-filter"></span> Адам</button>
							<button type="submit" name="filter" value="filtered" class="btn btn-' . $filter_btn_filtered_state . '"><span class="glyphicon glyphicon-filter"></span> Мой заказ</button>
							<button type="submit" name="filter" value="custom" class="btn btn-' . $filter_custom . '"><span class="glyphicon glyphicon-filter"></span> Ручной фильтр</button>
							<input type="hidden" value="' . $date . '" name="date">
						</div>
					</form>
				</div>
				<div class="col-sm-2 text-center">
					<a href="#" data-toggle="tooltip" title="Автозаполнить заказ"><button ng-click="vm.autofill()" type="submit" class="btn btn btn-warning"><span class="glyphicon glyphicon-flash"></span> </button></a>
					<a href="#" data-toggle="tooltip" title="Очистить заказ"><button ng-click="vm.cleanupOrders()" type="submit" class="btn btn btn-danger"><span class="glyphicon glyphicon-trash"></span> </button></a>
					<a href="print.php" target="_blank" data-toggle="tooltip" title="Распечатать заказ"><button type="submit" class="btn btn btn-info"><span class="glyphicon glyphicon-print"></span> </button></a>
				</div>
				<div class="col-sm-2 text-right">					
					<form ng-submit="excelSubmitted=true" action="SendEmail.php" method="get">
						<input type="hidden" name="date" value="' . $date . '"/>
						<button ng-disabled="excelSubmitted" type="submit" class="btn btn-success"' . $send_button_type . '"><span class="glyphicon glyphicon-send"></span> Отправить Excel Сергею/Адаму</button>
					</form>
				</div>
			</div>
		</div>';

		

$food_table_rows_colours = array('success', 'info', 'warning'); 


$sql = "SELECT DISTINCT Date FROM $table_food WHERE DATE >= CURDATE() ORDER BY Date ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0)
{
	$paging_date = isset($_GET['date']) ? $_GET['date'] : '';
	$_SESSION['paging_date'] = $paging_date;

	# 11. Create Pagination.
	$total_price = 0;
	$average = 0;
	$days_count = 0;
	$week_number = 0;
	$last_day = '';
	
	print '<form action="" method="post">
			<div class="container" align="center">
			  <ul class="pagination pagination-sm">';
	
	$daysofweek = array();
	
    while($row = $result->fetch_assoc()) {
		
		# 11.1. Get Price Sum for Pagination.
		$sum = 0;
		$sql_price_sum = "SELECT SUM($table_food.Price * $table_orders.Count) AS TotalPrice FROM $table_food LEFT OUTER JOIN $table_orders ON $table_food.Id=$table_orders.MenuItemId WHERE $table_food.Date = '" . $row["Date"] . "' AND $table_orders.UserId = $user_id";
		$result_price_sum = $conn->query($sql_price_sum);
		while($row_price_sum = $result_price_sum->fetch_assoc()) {
			$sum = $row_price_sum["TotalPrice"];
			}
		if ($sum == Null)
		{
			$sum = 0;
		}
		
		$dayofweek = day_of_week($row["Date"]);
		
		# 11.2. Add Pagination Splitter if new week is started.
		$dayofweek_dgt = date('w', strtotime($row["Date"]));
		foreach ($daysofweek as &$day_nmbr) {
			if ($dayofweek_dgt == $day_nmbr or $dayofweek_dgt <= $day_nmbr)
			{
				# 11.3 Add Total Price Pagination.
				$average = intval($total_price / $days_count);
				print '<li class="disabled"><a href="#"><div id="totalPriceForWeek' . $week_number . '">Всего: ' . $total_price . ' руб.</div><br><div id="averagePriceForWeek' . $week_number . '">Средн.: ' . $average .  ' руб.</div></a></li>';
				$total_price = 0;
				$days_count = 0;
				$week_number++;
				
				# 11.4. Add Pagination Splitter if new week is started.
				print '</ul>
				   <ul class="pagination pagination-sm">';
				 
				// Cleanup the array.
				$daysofweek = array();
				break;
			}
		}
		array_push($daysofweek, $dayofweek_dgt);
				
		// Show first table when open Menu page.
		if ($paging_date === '')
		{
			$paging_date = $row["Date"];
		}

		// Make selected page active.
		$page_active = '';
		if ($row["Date"] === $paging_date)
		{
			$page_active = ' class="active"';
		}
		
		print '<li' . $page_active . '><a href="?date=' . $row["Date"] . '&filter=' . $filter . '&company=' . $company . '">' . $dayofweek . "<br>" . $row["Date"] . '<div id="priceSum' . $row["Date"] . '">' . $sum .  ' руб.</div></a></li>';
		
		// Calculate total price.
		$total_price = $total_price + $sum;
		
		// Days Count.
		$days_count++;
		$last_day = $row["Date"];
	}
	
	# 11.5 Add Total Price Pagination.
	$average = intval($total_price / $days_count);
	print '<li class="disabled"><a href="#"><div id="totalPriceForWeek' . $week_number . '">Всего: ' . $total_price . ' руб.</div><br><div id="averagePriceForWeek' . $week_number . '">Средн.: ' . $average .  ' руб.</div></a></li>';
	
	print '	  </ul>
			</div>
			</form>';
	
	# 12.1. Create Table with Dishes.
	print '<table class="table table-hover"
					<thead>
						<tr>
							<th width="30"></th>
							<th width="100"><div class="text-center">Категория</div></th>
							<th width="500"><div class="text-center">Блюдо</div></th>
							<th width="50"><div class="text-center">Вес (гр.)</div></th>
							<th width="50"><div class="text-center">Цена (р.)</div></th>
							<th width="100"><div class="text-center">Поставщик</div></th>
							<th width="80"><div class="text-center">Добавить / удалить</div></th>
						</tr>
					</thead>
					<tbody><tbody></table>';
	
	# 12.2 Add Scrollbar (in case of displaying full menu).
	if ($filter === '' or $filter === 'custom') {
		print '<div class="scrollit">';
	}
	
	print '<table class="food-table table table-hover">
					<thead></thead>
					<tbody>';
	
	# 12.3. Query DB for Dishes depending on Filter.
	$sql1 = '';
	if ($filter === '') {
		if ($company === '') {
			$sql1 = "SELECT * FROM $table_food WHERE Date = '$paging_date' ORDER BY Company DESC, Id ASC";
		}
		else {
			$sql1 = "SELECT * FROM $table_food WHERE Date = '$paging_date' AND Company = \"" . $company . "\" ORDER BY Id ASC";
		}
	}
	else if ($filter === 'filtered') {
		$sql1 = "SELECT * FROM $table_food
			LEFT OUTER JOIN $table_orders ON $table_food.Id=$table_orders.MenuItemId
			WHERE $table_food.Date = '$paging_date' AND $table_orders.UserId = $user_id
			ORDER BY $table_food.Id";
	}
	else if ($filter === 'custom') {
		$sql2 = "SELECT FoodType, Company, Priority FROM $table_filters WHERE UserId = $user_id AND Enabled = 1 ORDER BY Priority ASC";
		$result2 = $conn->query($sql2);
		$sql_where = '';
		$sql_case = 'ORDER BY CASE ';
		while($row2 = $result2->fetch_assoc()) {
			$sql_where .= '(Type = "' . $row2["FoodType"] . '" AND Company = "' . $row2["Company"] . '") OR ';
			$sql_case .=  'WHEN TYPE = "' . $row2["FoodType"] . '" THEN ' . $row2["Priority"] . ' ';
		}
		
		if ($sql_where === '') {
			$sql1 = "";
		}
		else {
			$sql_where = substr($sql_where, 0, -3);
			$sql1 = "SELECT * FROM food WHERE Date = '$paging_date' AND ($sql_where) $sql_case END ASC";
		}
	}

	if ($sql1 === "") {
	}
	else {
		$result1 = $conn->query($sql1);
		$i = 0;
		$previous_food_type = '';
		while($row1 = $result1->fetch_assoc()) {
			
			if ($previous_food_type === '' or $previous_food_type === $row1["Type"])
				{
					
				}
				else
				{
					$i++;
				}
				if ($i >= count($food_table_rows_colours))
				{
					$i = 0;
				}
				
				# 12.4 Get the Items Count.
				$itemsCount = 0;
				$sql2 = "SELECT * FROM $table_orders WHERE UserId = $user_id AND MenuItemId = " . $row1["Id"];
				$result2 = $conn->query($sql2);
				while($row2 = $result2->fetch_assoc()) {
					$itemsCount = $row2["Count"];
				}
				
				print '<tr ng-init="vm.cart[' . $row1["Id"] . ']=' . $itemsCount . '" ng-class="{\'row-active\': vm.cart[' . $row1["Id"] . '] > 0}" class="' . $food_table_rows_colours[$i] . '">
						<td width="35" style="padding:8px 0 0 8px;vertical-align:top;"><div id="menuItemCounter' . $row1["Id"] .  '" ng-show="vm.cart[' . $row1["Id"] . ']>0 && \''.$filter.'\'!==
						\'filtered\'" class="menu-item-counter ng-cloak food-counter">{{vm.cart[' . $row1["Id"] . ']}}</div></td>
						<td width="100" align="center">' . $row1["Type"] . '</td>
						<td width="500">' . $row1["Name"] . '<br>' . $row1["Contain"] . '</td>
						<td width="50" align="center">' . $row1["Weight"] . '</td>
						<td width="50" align="center">' . $row1["Price"] . '</td>
						<td width="100" align="center">' . $row1["Company"] . '</td>
						<td width="80" align="center"><button ng-click="vm.addToCart(' . $row1["Id"] . ', \'' . $paging_date . '\')" type="submit" name="' . $row1["Id"] .  '" value="' . $row1["Id"] .  '" class="btn btn btn-' . $food_table_rows_colours[$i] . ' btn-xs"><span class="glyphicon glyphicon-plus"></span></button> <button id="minus' . $row1["Id"] .  '" ng-click="vm.RemoveFromCart(' . $row1["Id"] .  ', ' . $food_table_rows_colours[$i] . ', \'' . $paging_date . '\', \'' . $filter . '\')" type="submit" name="' . $row1["Id"] .  '" value="' . $row1["Id"] .  '" class="btn btn btn-' . $food_table_rows_colours[$i] . ' btn-xs"><span class="glyphicon glyphicon-minus"></span></button><p style="margin:0;" id="menuItemCount' . $row1["Id"] .  '" class="text-muted">' . $itemsCount .  '</p></td>
					  </tr>';
		
				$previous_food_type = $row1["Type"];
		}
	}	
	
	print '</tbody></table></div>';
	
	# 13.1 Add Scrollbar (in case of displaying full menu).
	if ($filter === '' or $filter === 'custom') {
		print '</div>';
	}
	print '</div></div>';
}
else
{
	print '<br><br>Меню пока отсутствует.';
}

require_once "footer.php";
 ?>