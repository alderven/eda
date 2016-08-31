<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";


# Get Dishes
function getDishes($AutofillSetting_AutofillType, $table_food, $date, $sqlDishType, $sqlCompany, $sqlAutofillType, $conn, $my_order, $table_orders, $user_id) {
	
	# Decide what method to use for selecting Dishes
	if ($AutofillSetting_AutofillType == 1) {
		$my_order = getFavoriteDishes($table_food, $table_orders, $user_id, $conn, $my_order, $date, $sqlCompany, $sqlDishType);
	}
	else {
		$my_order = getRandomOrRichDishes($table_food, $date, $sqlDishType, $sqlCompany, $sqlAutofillType, $conn, $my_order);
	}
	
	return $my_order;
}


# Get Random or Rich Dishes
function getRandomOrRichDishes($table_food, $date, $sqlDishType, $sqlCompany, $sqlAutofillType, $conn, $my_order) {
	
	# 1. Generate SQL statement
	$sql = "SELECT Id FROM $table_food WHERE Date = '$date' AND Type LIKE '%$sqlDishType%'" . $sqlCompany. " ORDER BY $sqlAutofillType LIMIT 1";
	error_log('sql: ' . $sql, 0);
	
	# 2. Call SQL
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()) {
		array_push($my_order, $row["Id"]);
	}
	
	# 3. Return Array containing Dish Ids.
	return $my_order;
}


# Get Favorite Dishes
function getFavoriteDishes($table_food, $table_orders, $user_id, $conn, $my_order, $date, $sqlCompany, $sqlDishType)
{
	// 1. Find favorite dishes history
	$favoriteDishes = array();
	$sql = "SELECT Name, COUNT(*) AS Total FROM $table_food
			JOIN $table_orders ON food.Id = orders.MenuItemId
			WHERE orders.UserId = $user_id
			AND Type LIKE '%$sqlDishType%'
			GROUP BY Name
			HAVING ( COUNT(Name) > 1 ) ORDER BY Total DESC";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()) {
		array_push($favoriteDishes, $row["Name"]);
		}
	
	// 2. If any Favorite Dishes was found
	if (count($favoriteDishes) > 0) {
	
		// 3. Find and add favorite dishes
		$favoriteDishesLimit = 4; // THIS IS CONFIGURABLE PARAMETER
		$favoriteDishesCount = 0;
		$sql = "SELECT Id FROM $table_food WHERE Date = '$date' AND Name IN (\"" . implode('", "', $favoriteDishes) . "\")" . $sqlCompany. " LIMIT 1";
		//error_log($sql, 0);
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				// Stop adding new dishes if number of found dishes reaches the defined limit
				if ($favoriteDishesCount >= $favoriteDishesLimit) {
					break;
				}
				
				// Add Dish to My Order only if it's not added already
				if (!in_array($row["Id"], $my_order)) {
					array_push($my_order, $row["Id"]);
					$favoriteDishesCount++;
				}
			}	
		}
	}
	
	return $my_order;
}

# 1. Get all days starting from tomorrow
$sql = "SELECT DISTINCT Date FROM $table_food WHERE Date > CURDATE()";
$result = $conn->query($sql);
$all_dates = array();
while($row = $result->fetch_assoc()) {
	array_push($all_dates, $row["Date"]);
}

# 2. Fund unfilled days
$unfilled_dates = array();
foreach ($all_dates as &$date) {
	$sql = "SELECT MenuItemId FROM $table_orders
			LEFT OUTER JOIN $table_food ON $table_food.Id=$table_orders.MenuItemId
			LEFT OUTER JOIN $table_users ON $table_orders.UserId=$table_users.Id
			WHERE $table_food.Date = '$date' AND $table_orders.UserId = $user_id";
	$result = $conn->query($sql);

	if ($result->num_rows == 0)
	{
		array_push($unfilled_dates, $date);
	}
}
# 3. If there are no unfilles dates, then show modal popup.
if (count($unfilled_dates) == 0)
{
	// Unfilled days are absent. Show modal alert.
	print 0;
}
else
{	
	# 4. Define array containing all selected Dish Ids
	$my_order = array();

	# 5. Get Autofill Settings from DB
	$sql = "SELECT * FROM $table_users WHERE Id = $user_id";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
		 $AutofillSetting_AutofillType = $row['AutofillSetting_AutofillType'];
		 $AutofillSetting_Company = $row['AutofillSetting_Company'];
		 $AutofillSetting_OrderFirstDishes = $row['AutofillSetting_OrderFirstDishes'];
		 $AutofillSetting_OrderSecondDishes = $row['AutofillSetting_OrderSecondDishes'];
		 $AutofillSetting_OrderSalads = $row['AutofillSetting_OrderSalads'];
		 $AutofillSetting_OrderDesserts = $row['AutofillSetting_OrderDesserts'];
	}
	
	# 6. Prepare SQL statemend depending on Autofill Type
	$sqlAutofillType = '';
	if ($AutofillSetting_AutofillType == 0) {
		$sqlAutofillType = 'RAND()';
	}
	else if ($AutofillSetting_AutofillType == 2) {
		$sqlAutofillType = 'Price DESC';
	}
	
	# 7. Prepare SQL statement depending on selected Company
	if ($AutofillSetting_Company == 0) {
		$sqlCompany = '';
	}
	else if ($AutofillSetting_Company == 1) {
		$sqlCompany = ' AND Company = "Цимус"';
	}
	else if ($AutofillSetting_Company == 2) {
		$sqlCompany = ' AND Company = "Адам"';
	}
	
	# 8. Select dishes day by day
	foreach ($unfilled_dates as &$date) {
		
		if ($AutofillSetting_OrderFirstDishes == 1) {
			$sqlDishType = 'ерв';
			$my_order = getDishes($AutofillSetting_AutofillType, $table_food, $date, $sqlDishType, $sqlCompany, $sqlAutofillType, $conn, $my_order, $table_orders, $user_id);
		}
		if ($AutofillSetting_OrderSecondDishes == 1) {
			$sqlDishType = 'тор';
			$my_order = getDishes($AutofillSetting_AutofillType, $table_food, $date, $sqlDishType, $sqlCompany, $sqlAutofillType, $conn, $my_order, $table_orders, $user_id);
		}
		if ($AutofillSetting_OrderSalads == 1) {
			$sqlDishType = 'алаты';
			$my_order = getDishes($AutofillSetting_AutofillType, $table_food, $date, $sqlDishType, $sqlCompany, $sqlAutofillType, $conn, $my_order, $table_orders, $user_id);
		}
		if ($AutofillSetting_OrderDesserts == 1) {
			$sqlDishType = 'ыпечка';
			$my_order = getDishes($AutofillSetting_AutofillType, $table_food, $date, $sqlDishType, $sqlCompany, $sqlAutofillType, $conn, $my_order, $table_orders, $user_id);
		}
	}

	# 9. Add selected dishes to the order
	foreach ($my_order as &$menu_item_id) {
		$sql = "INSERT INTO $table_orders (UserId, MenuItemId, Count) VALUES ($user_id, $menu_item_id, 1)";
		$result = $conn->query($sql);
	}

	# 10. Autofill is OK
	print 1;

}
?>