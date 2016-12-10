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
require_once "common.php";


$sql = "SELECT food.Company, food.Date, food.Name, users.Name as UserName, users.Surname, orders.Count
		FROM users
		JOIN orders
		ON orders.UserId = users.Id
		JOIN food ON food.Id = orders.MenuItemId
		WHERE orders.UserId = $user_id
		AND food.Date >= CURDATE()
		ORDER BY food.Date";
		
if ($result = $conn->query($sql)) {
	
	if ($result->num_rows > 0) {
	
		setlocale( LC_TIME, 'ru_RU', 'russian' );
		
		$previous_date = Null;

		$flag = true;
		
		while ($row = $result->fetch_assoc()) {
			
			if ($flag) {
				print '<html><body><h1>' . $row["UserName"] . ' ' . $row["Surname"] . '</h1>';
				$flag = false;
			}
						
			if ($previous_date != $row["Date"]) {
				
				$dayofweek = day_of_week($row["Date"]);
				
				$months = array( '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
				$month = $months[date("m", strtotime($row["Date"]))];
				
				
				print '<br><h4><b>' . $dayofweek . ' ' . date("d", strtotime($row["Date"])) . ' ' . $month . '</b></h4>';
			}
			print '<ul class=leaders><li><span>' . $row["Name"] . ' (' . $row["Company"] . ')</span><span>' . $row["Count"] . '</span></ul>';
			
			$previous_date =  $row["Date"];
		}
	}
	else
	{
		print '<h1 align="center">Заказов не найдено</h1>';	
	}
}
else {
	print '<h1 align="center">Ошибка: невозможно получить список заказов</h1>';
}
print '</body></html>';

?>