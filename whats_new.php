<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

print '<body>';

# Display Navigation Bar
print $navigationBar;

?>

<!-- Page title -->
<div align="center"><h1>Что нового?</h1></div>

<!-- Update 30.10.2016-->
<div><h3><table class="table"><tr class="info"><td align="center">Версия от 30.10.2016</td></tr></tbody></table></h3></div>
На странице <a href="menu.php" target="_blank">«Меню»</a> добавлена кнопка «Распечатать заказ»:
<div align="center"><a href="menu.php" target="_blank"><img src="img\update20161030_printButton.png"></a></div>
<hr>
<p>На странице авторизации теперь отображается общее количество заказов:
<div align="center"><img src="img\update20161030_loginPage.png"></div>
<hr>

<!-- Update 08.10.2016-->
<div><h3><table class="table"><tr class="info"><td align="center">Версия от 08.10.2016</td></tr></tbody></table></h3></div>
Переработаны <a href="settings_autofill.php" target="_blank">настройки Автозаполнения</a>:</p>
<div align="center"><a href="settings_autofill.php" target="_blank"><img src="img\update20161008_autofill.png"></a></div>
<hr>

<!-- Update 20.08.2016-->
<div><h3><table class="table"><tr class="info"><td align="center">Версия от 20.08.2016</td></tr></tbody></table></h3></div>
<p>В разделе <a href="menu.php" target="_blank">«Меню»</a> добавлена кнопка «Очистить заказ», поменялся интерфейс кнопки «Автозаполнение»:</p>
<div align="center"><a href="menu.php" target="_blank"><img src="img\update20160820_cleanupOrder.png"></a></div>
<hr>
<p>Добавлена страница с <a href="whats_new.php" target="_blank">историей разработки</a>.</p>
<p>Страница находится в разделе: «Разработка» → «Что нового?»</p>
<hr>
<p>Исходный код проекта опубликован в открытом доступе: <a href="https://github.com/alderven/eda" target="_blank">https://github.com/alderven/eda</a></p>
<p>Сюда можно заносить баги (требуется GitHub аккаунт): <a href="https://github.com/alderven/eda/issues" target="_blank">https://github.com/alderven/eda/issues</a></p>
<p>Исправлен баг: <a href="https://github.com/alderven/eda/issues/1" target="_blank">Unable to login if Login in uppercase</a></p>
<hr>

<!-- Update 27.07.2016-->
<div><h3><table class="table"><tr class="info"><td align="center">Версия от 27.07.2016</td></tr></tbody></table></h3></div>
<p>Ручная настройка автозаполнения:</p>
<div align="center"><a href="settings_autofill.php" target="_blank"><img src="img\update20160727_autofill.png"></a></div>
<br>
<p>Настройка автозаполнения находится в разделе: «Настройки» → «Автозаполнение».</p>
<p>Перейти к настройке <a href="settings_autofill.php" target="_blank">сейчас</a>.</p>
<hr>
<p>Список Ваших любимых блюд:</p>
<div align="center"><a href="stat_favorite_dishes.php" target="_blank"><img src="img\update20160727_favoriteDishes.png"></a></div>
<br>
<p>Статистика находится в разделе: «Статистика» → «Любимые блюда».</p>
<p>Перейти к статистике <a href="stat_favorite_dishes.php" target="_blank">сейчас</a>.</p>
<hr>
 <?php
require_once "footer.php";
 ?>