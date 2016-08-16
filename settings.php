<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<style>
#enabled_1, #enabled_0 {
border: 2px dotted #999999;
width: 450px;
min-height: 20px;
list-style-type: none;
margin: 0;
padding: 5px 0 0 0;
float: left;
margin-right: 10px;
}
#enabled_1 li, #enabled_0 li {
margin: 0 5px 5px 5px;
padding: 5px;
font-size: 1.2em;
width: 430px;
}
</style>
<script>
$(function() {

$( "#enabled_1, #enabled_0").sortable({
  items: "li:not(.ui-state-disabled)"
});
	
$("#enabled_1, #enabled_0").sortable({
	connectWith: ".connectedSortable",
    update: function (event, ui) {
        //var data = $(this).sortable('serialize');
		var enabled = $(this).sortable( "widget" )[0].id.slice(-1);
		var data = $(this).sortable( "toArray" );

		// Make HTTP POST request
		//$.post( "SaveFilter.php", function( data ));
		$.post("SaveFilter.php",
		{
			enabled: enabled,
			data: data
		}
		/* For debug:
		, function(data, status){
			alert(data);
		}*/
		);
		
    }
}).disableSelection();
});
</script>

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

<div align="center"><h1>Настройка ручного фильтра</h1></div>
<br>


<?php

// 1. Configure DB connection
require_once "config.php";
$sql = "SET NAMES utf8";
$conn->query($sql);

// 2. Get UserId
$sql = "SELECT Id FROM $table_users WHERE Login = '" . $_SESSION['myusername'] . "'";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
        $user_id = $row["Id"];
    }

// 3. Create Table according to the Custom Filters
// 3.1 Create 'Left' table
print '<ul align="center" id="enabled_1" class="connectedSortable">
<li class="ui-state-disabled"><h2><b>Фильтр</b></h2>
	<h5>- Отсортируйте категории блюд по приоритету<br>- Отправьте ненужные в корзину</h5></li>';
$sql = "SELECT FoodType, Company FROM $table_filters WHERE UserId = $user_id AND Enabled = 1 ORDER BY Priority ASC";
$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	 print '<li id="' . $row["FoodType"] . '|' . $row["Company"] . '" class="ui-state-default">' . $row["FoodType"] . ' (' . $row["Company"] . ')</li>';
}
print '</ul>';

// 3.2 Create 'Trash' table
print '<ul align="center" id="enabled_0" class="connectedSortable">
<li class="ui-state-disabled"><h2><b>Корзина</b></h2>
	<h5>- Перетащите сюда категории блюд, которые вы не хотите заказывать</h5></li>';
$sql = "SELECT * FROM $table_filters WHERE UserId = $user_id AND Enabled = 0 ORDER BY Priority ASC";
$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	 print '<li id="' . $row["FoodType"] . '|' . $row["Company"] . '" class="ui-state-default">' . $row["FoodType"] . ' (' . $row["Company"] . ')</li>';
}
print '</ul>';

// 4. Close DB connection
$conn->close();

// 5. Add Footer
require_once "footer.php";
 ?>