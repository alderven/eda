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

<div align="center"><h1>Приложение для Android</h1></div>
<br>
<div class="row">
    <div align="center" class="col-sm-12">Приложение позволяет просматривать заказанные Вами блюда</div>
</div>
<br>
<div class="row">
    <div align="center" class="col-sm-12"><a target="_blank" href="https://play.google.com/store/apps/details?id=ru.net.eda.eda" class="btn btn-success btn-lg" role="button">Установить приложение</a></div>
</div>
<br>
<br>
<div class="row">
  <div align="right" class="col-sm-6"><img src="img\android1.png" alt="Android" style="width:360px;height:640px;"></div>
  <div align="left" class="col-sm-6"><img src="img\android2.png" alt="Android" style="width:360px;height:640px;"></div>
</div>

 <?php
require_once "footer.php";
 ?>