<style>
ul.leaders {
    max-width: 45em;
    padding: 0;
    overflow-x: hidden;
    list-style: none}
ul.leaders li:before {
    float: left;
    width: 0;
    white-space: nowrap;
    content:
 ". . . . . . . . . . . . . . . . . . . . "
 ". . . . . . . . . . . . . . . . . . . . "
 ". . . . . . . . . . . . . . . . . . . . "
 ". . . . . . . . . . . . . . . . . . . . "}
ul.leaders span:first-child {
    padding-right: 0.33em;
    background: white}
ul.leaders span + span {
    float: right;
    padding-left: 0.33em;
    background: white}
</style>

<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
/*
$sql = "SELECT * FROM $table_food
		LEFT OUTER JOIN $table_orders ON $table_food.Id=$table_orders.MenuItemId
		WHERE $table_orders.UserId = $user_id
		AND Date >= CURDATE()
		ORDER BY Date";
*/

$sql = "SELECT $table_food.Company, $table_food.Date, $table_food.Name, $table_orders.Count
		FROM $table_users
			JOIN $table_orders
				ON $table_orders.UserId = $table_users.Id
			JOIN $table_food
				ON $table_food.Id = $table_orders.MenuItemId
		WHERE $table_orders.UserId = $user_id
		AND Date >= CURDATE()
		ORDER BY Date";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
	
	setlocale( LC_TIME, 'ru_RU', 'russian' );
	
	$previous_date = Null;
	
	print '<html><body>';
	
	while ($row = $result->fetch_assoc()) {
					
		if ($previous_date != $row["Date"]) {
			
			$dayofweek = date('w', strtotime($row["Date"]));
			if($dayofweek == 1) {
				$dayofweek = 'Понедельник';
			} elseif($dayofweek == 2) {
				$dayofweek = 'Вторник';
			} elseif($dayofweek == 3) {
				$dayofweek = 'Среда';
			} elseif($dayofweek == 4) {
				$dayofweek = 'Четверг';
			} elseif($dayofweek == 5) {
				$dayofweek = 'Пятница';
			} elseif($dayofweek == 6) {
				$dayofweek = 'Суббота';
			} elseif($dayofweek == 7) {
				$dayofweek = 'Воскресенье';
			} else {
				$dayofweek = '';
			};
			
			$months = array( '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
			$month = $months[date("m", strtotime($row["Date"]))];
			
			
			print '<br><h4><b>' . $dayofweek . ' ' . date("d", strtotime($row["Date"])) . ' ' . $month . '</b></h4>';
		}
		print '<ul class=leaders><li><span>' . $row["Name"] . ' (' . $row["Company"] . ')</span><span>' . $row["Count"] . '</span></ul>';
		
		$previous_date =  $row["Date"];
	}
	
	print '</body></html>';
}
else{
	print 'Заказов нет';
}


?>