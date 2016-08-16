<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
?>
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
		<li class="active"><a href="api.php"><span class="glyphicon glyphicon-transfer"></span> API</a></li>
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

<div align="center"><h1>API</h1></div>

<table class="table">
  <tr>
    <td colspan="1"><div align="center"><h3>Аутентификация пользователя</h3></div></td>
  </tr>
  <tr class="info">
    <td><div align="center"><h4>Для аутентификации пользователя используется <a href="https://en.wikipedia.org/wiki/Basic_access_authentication#Client_side" target="_blank">Basic access authentication</a></h4></div></td>
  </tr>
</table>

<br>

<table class="table">
  <tr>
    <td colspan="2"><div align="center"><h3>Получение заполненного пользователем меню</h3></div></td>
  </tr>
  <tr class="warning">
    <td width="200"><div align="center"><h4>Тип запроса:</h4></div></td>
    <td><h4>GET</p></h4></td>
  </tr>
  <tr class="info">
    <td><div align="center"><h4>URL запроса:</h4></div></td>

<?php
print '<td><h4><a href="' . $site_url . 'api/menu.php" target="_blank">' . $site_url . 'api/menu.php</a>';
?>

    </p></h4></td>
  </tr>
  <tr class="success">
    <td><div align="center"><h4>Пример ответа:</h4></div></td>
    <td><h4><pre><code>[{
		"Date" : "2016-02-16",
		"Name" : "Похлёбка старомосковская "
	}, {
		"Date" : "2016-02-16",
		"Name" : "Стейк куриный запеченный с пом и сыром (100\/40 г)"
	}, {
		"Date" : "2016-02-17",
		"Name" : "Суп гороховый со свининой"
	}, {
		"Date" : "2016-02-17",
		"Name" : "Сырники творожные, 2 шт (со сметаной)"
	}, {
		"Date" : "2016-02-18",
		"Name" : "Борщ с говядиной"
	}
]
</code></pre></h4></td>
  </tr>
</table>
 <?php
require_once "footer.php";
 ?>