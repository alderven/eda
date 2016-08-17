<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

// Define function for collecting Favorite Dishes
function collectFavoriteDishes($table_food, $table_orders, $user_id, $conn, $my_order, $date, $sqlCompany, $sqlDishType)
{
	// 1. Find favorite dishes history
	$favoriteDishes = array();
	$sql = "SELECT Name, COUNT(*) AS Total FROM $table_food
			JOIN $table_orders ON food.Id = orders.MenuItemId
			WHERE orders.UserId = $user_id
			$sqlDishType
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

// 3. Get all days starting from tomorrow.
$sql = "SELECT DISTINCT Date FROM $table_food WHERE Date > CURDATE()";
$result = $conn->query($sql);
$all_dates = array();
while($row = $result->fetch_assoc()) {
	array_push($all_dates, $row["Date"]);
}

// 4. Filter dates which are empty for current user.
$empty_dates = array();
foreach ($all_dates as &$date) {
	$sql = "SELECT MenuItemId FROM $table_orders
			LEFT OUTER JOIN $table_food ON $table_food.Id=$table_orders.MenuItemId
			LEFT OUTER JOIN $table_users ON $table_orders.UserId=$table_users.Id
			WHERE $table_food.Date = '$date' AND $table_orders.UserId = $user_id";
	$result = $conn->query($sql);

	if ($result->num_rows == 0)
	{
		array_push($empty_dates, $date);
	}
}
// 5. If no empty dates, then show modal popup.
if (count($empty_dates) == 0)
{
	// Empty days are absent. Show modal alert.
	print 0;
}
else
{	
	// 6. Make dishes selection according to the Autofill Settings
	$autofillSettingsDishes = array();
	$sqlLike = array('ерв', 'тор', 'алаты', 'ыпечка');
	$sql = "SELECT * FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
		$result = $conn->query($sql);
		 while ($row = $result->fetch_assoc()) {
			$autofillSetting_OrderInCimus = $row[$autofillSettings[0]];
			$autofillSetting_OrderInAdam = $row[$autofillSettings[1]];
			$autofillSettingsDishes[] = $row[$autofillSettings[2]];
			$autofillSettingsDishes[] = $row[$autofillSettings[3]];
			$autofillSettingsDishes[] = $row[$autofillSettings[4]];
			$autofillSettingsDishes[] = $row[$autofillSettings[5]];
			$autofillSetting_Rich = $row[$autofillSettings[6]];
			$autofillSetting_FavoriteDishes = $row[$autofillSettings[7]];
		}
		
	// 7. Prepare SQL statement depending on selected Company
	$sqlCompany = '';
	if ($autofillSetting_OrderInCimus == 1 and $autofillSetting_OrderInAdam == 1) {
		$sqlCompany = ' AND (Company = "Цимус" OR Company = "Адам")';
	}
	elseif ($autofillSetting_OrderInCimus == 1 and $autofillSetting_OrderInAdam == 0) {
		$sqlCompany = ' AND Company = "Цимус"';
	}
	elseif ($autofillSetting_OrderInCimus == 0 and $autofillSetting_OrderInAdam == 1) {
		$sqlCompany = ' AND Company = "Адам"';
	}
	elseif ($autofillSetting_OrderInCimus == 0 and $autofillSetting_OrderInAdam == 0) {
		$sqlCompany = ' AND Company = "FakeCompany"'; // got no results because no companies was selected
	}

	// 8. Select random dishes for the empty dates for current user.
	$my_order = array();
	foreach ($empty_dates as &$date) {

		// Collect Favorite Dishes
		//error_log(implode(', ', $autofillSettingsDishes), 0);
		if ($autofillSetting_FavoriteDishes == 1 and !in_array(1, $autofillSettingsDishes)) {
			//error_log('Before CollectFavoriteDishes' . count($my_order), 0);
			$my_order = collectFavoriteDishes($table_food, $table_orders, $user_id, $conn, $my_order, $date, $sqlCompany, '');
			//error_log('After CollectFavoriteDishes' . count($my_order), 0);
		}
		// Collect Dishes depending on Dish Type
		else {
			for ($i = 0; $i < count($autofillSettingsDishes); $i++) {
				if ($autofillSettingsDishes[$i] == 1) {
					
					// Define SQL Type statement
					$sqlDishType = " AND Type LIKE '%" . $sqlLike[$i]. "%'";
					
					// Collect Favorite dishes
					if ($autofillSetting_FavoriteDishes == 1) {
						//error_log('Before CollectFavoriteDishes' . count($my_order), 0);
						$my_order = collectFavoriteDishes($table_food, $table_orders, $user_id, $conn, $my_order, $date, $sqlCompany, $sqlDishType);
						//error_log('After CollectFavoriteDishes' . count($my_order), 0);
						}
					// Or collect random/rich dishes
					else {
						$sqlOrderBy = 'RAND()';
						if ($autofillSetting_Rich == 1) {
							$sqlOrderBy = 'Price DESC';
						}
						$sql = "SELECT Id FROM $table_food WHERE Date = '$date'" . $sqlDishType . $sqlCompany. " ORDER BY $sqlOrderBy LIMIT 1";
						$result = $conn->query($sql);
						while($row = $result->fetch_assoc()) {
							array_push($my_order, $row["Id"]);
						}
					}
				}
			}
		}
	}

	// 9. Add selected dishes to the order.
	foreach ($my_order as &$menu_item_id) {
		$sql = "INSERT INTO $table_orders (UserId, MenuItemId, Count) VALUES ($user_id, $menu_item_id, 1)";
		$result = $conn->query($sql);
	}
	
	// 10. Autofill is OK.
	print 1;
}
?>