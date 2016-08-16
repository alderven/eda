<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
?>
<!-- http://bootsnipp.com/snippets/featured/material-design-switch -->
<style>
.material-switch > input[type="checkbox"] {
    display: none;   
}

.material-switch > label {
    cursor: pointer;
    height: 0px;
    position: relative; 
    width: 40px;  
}

.material-switch > label::before {
    background: rgb(0, 0, 0);
    box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
    border-radius: 8px;
    content: '';
    height: 16px;
    margin-top: -8px;
    position:absolute;
    opacity: 0.3;
    transition: all 0.4s ease-in-out;
    width: 40px;
}
.material-switch > label::after {
    background: rgb(255, 255, 255);
    border-radius: 16px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
    content: '';
    height: 24px;
    left: -4px;
    margin-top: -8px;
    position: absolute;
    top: -4px;
    transition: all 0.3s ease-in-out;
    width: 24px;
}
.material-switch > input[type="checkbox"]:checked + label::before {
    background: inherit;
    opacity: 0.5;
}
.material-switch > input[type="checkbox"]:checked + label::after {
    background: inherit;
    left: 20px;
}
</style>

<body>
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

<div align="center"><h1>Настройка автозаполнения</h1></div>
<br>


<?php

// 1. Configure DB connection
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);

// 2. Get User info
$autofillSettingsVaules = array();
$settingNames = array('Заказывать в Цимусе', 'Заказывать у Адама', 'Первые блюда', 'Вторые блюда', 'Салаты', 'Выпечка и десерты', 'Расточительный заказ', 'Любимые блюда');
$sql = "SELECT * FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
        $user_id = $row["Id"];
		$autofillSettingsVaules[] = $row[$autofillSettings[0]];
		$autofillSettingsVaules[] = $row[$autofillSettings[1]];
		$autofillSettingsVaules[] = $row[$autofillSettings[2]];
		$autofillSettingsVaules[] = $row[$autofillSettings[3]];
		$autofillSettingsVaules[] = $row[$autofillSettings[4]];
		$autofillSettingsVaules[] = $row[$autofillSettings[5]];
		$autofillSettingsVaules[] = $row[$autofillSettings[6]];
		$autofillSettingsVaules[] = $row[$autofillSettings[7]];
    }

// 3. Print Settings table header
$checkboxColors = array('primary', 'success',  'info', 'warning', 'danger', 'default', 'primary', 'success');
print '<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
		<form action="settings_autofill_save_config.php" method="post">
            <div class="panel panel-default">
				<ul class="list-group">';

// 4. Print Settings table content
for ($i = 0; $i < count($autofillSettings); $i++) {
	$checked = '';
	if ($autofillSettingsVaules[$i] == 1) {
		$checked = ' checked';
	}
	
    print '
			<li class="list-group-item">' . $settingNames[$i] . '
                <div class="material-switch pull-right">
					<input' . $checked . ' id="' . $autofillSettings[$i] . '" name="' . $autofillSettings[$i] . '" type="checkbox"/>
					<label for="' . $autofillSettings[$i] . '" class="label-' . $checkboxColors[$i]. '"></label>
                </div>
            </li>';
}

// 5. Print Settings table footer
print '
					</ul>
				</div>       
				<div align="center"><input type="submit" class="btn btn-success" value="Сохранить"></div>
			</form>			
        </div>
    </div>
</div>
';

// 6. Close DB connection
$conn->close();

// 7. Add Footer
require_once "footer.php";
 ?>