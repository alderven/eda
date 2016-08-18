<?php

###################################################################################################
# I. Define Constants
###################################################################################################

# 1. Define DB constants
$servername = 'localhost';
$username = 'root';
$password = '12345';
$db_name = 'eda';
$table_users = 'users';
$table_food = 'food';
$table_orders = 'orders';
$table_filters = 'filters';
$table_config = 'config';

# 2. Define web site URL
$site_url = 'http://eda.adalisk.com/';

# 3. Define Email
$send_email_from = 'eda@adalisk.com';

# 4. Define Autofill parameters
$autofillSettings = array('AutofillSetting_OrderInCimus', 'AutofillSetting_OrderInAdam', 'AutofillSetting_OrderFirstDishes', 'AutofillSetting_OrderSecondDishes', 'AutofillSetting_OrderSalads', 'AutofillSetting_OrderDesserts', 'AutofillSetting_Rich', 'AutofillSetting_FavoriteDishes');

###################################################################################################
# II. Connect to DB
###################################################################################################

# 1. Create DB connection
$conn = new mysqli($servername, $username, $password, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

# 2. Configure DB encoding
$sql = "SET NAMES utf8";
$conn->query($sql);

###################################################################################################
# III. Get Email Password
###################################################################################################

$sql = "SELECT ParameterValue FROM $table_config WHERE ParameterName = 'EmailPassword'";
$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	$send_email_from_pass = $row["ParameterValue"];
 }
 
###################################################################################################
# IV. Generate Navigation Bar
###################################################################################################

# 1. Generate Navigation Bar only if User Session defined
if(isset($_SESSION['myusername'])) {
	
	# 2. Get User Id/RoleId/Name/Surname
	$sql = "SELECT * FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
		$user_id = $row["Id"];
		$role_id = $row["RoleId"];
		$name = $row["Name"];
		$surname = $row["Surname"];
		$email = $row["Login"];
	}

	# 3. Initialize Admin Navigation Bar depending on the User RoleId
	$adminNavBar = '';
	if ($role_id == 0 or $role_id == 1) {
		$adminNavBar = '<li class="dropdown">
					<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Администрирование<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="admin_upload_excel.php"><span class="glyphicon glyphicon-cloud-upload"></span> Загрузка Меню на сервер</a></li>
						<li><a href="admin_download_excel.php"><span class="glyphicon glyphicon-cloud-download"></span> Скачивание заполненного Меню</a></li>
					</ul>
				</li>';
	}

	# 4. Define Navigation Bar
	$navigationBar = '
	<div class="container">
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
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Разработка<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="whats_new.php"><span class="glyphicon glyphicon-question-sign"></span> Что нового?</a></li>
							<li><a href="api.php"><span class="glyphicon glyphicon-transfer"></span> Открытое API</a></li>
							<li><a href="android.php"><span class="glyphicon glyphicon-phone"></span> Android-версия</a></li>
						</ul>
					</li>' . $adminNavBar . '</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> ' . $name . ' ' . $surname . ' <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="change_password.php"><span class="glyphicon glyphicon-cog"></span> Сменить пароль</a></li>
							<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
						</ul>
					</li>
				</ul>
				</div><!--/.nav-collapse -->
			</div><!--/.container-fluid -->
		</nav>';
}
?>