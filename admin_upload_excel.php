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

<div align="center"><h1>Загрузка Меню на сервер</h1></div>
<br>
Загрузите на сервер Эксель файл с меню, присланный Вам поставщиком обедов.
<br>
<br>
<form action="upload.php" method="post" enctype="multipart/form-data">
<table>
  <tr>
    <td><input type="file" class="btn btn btn-default" name="uploadfile"></td>
    <td><button type="submit" class="btn btn btn-primary"><span class="glyphicon glyphicon-cloud-upload"></span> Загрузить</button></td>
  </tr>
</table>

<?php
require_once "footer.php";
?>