<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

print '<body>';

# 1. Display Navigation Bar
print $navigationBar;

?>

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