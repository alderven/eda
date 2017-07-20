<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
require_once "common.php";

# Check for Admin priveleges
is_admin($role_id);

# Get Email, Name, Surname parameters
$email = strtolower($_POST['email']);
$name = $_POST['name'];
$surname = $_POST['surname'];

# Check, that this email not exist in the system
$sql = "SELECT Name, Surname FROM users WHERE Login = \"$email\"";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$n = $row["Name"];
	$s = $row["Surname"];
	alert("danger", "Пользователь $s $n [$email] уже существует", "admin.users.php");
}

# Generate Password
$password = generatePassword();

# Add User data into DB
$password_encrypted = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (Id, isActive, Login, Password, RoleId, Name, Surname, CompanyId, MenuFilter, RestorePasswordGUID, ShowNotification, AutofillSetting_AutofillType, AutofillSetting_Company, AutofillSetting_OrderFirstDishes, AutofillSetting_OrderSecondDishes, AutofillSetting_OrderSalads, AutofillSetting_OrderDesserts) VALUES (NULL, \"1\", \"$email\", \"$password_encrypted\", \"2\", \"$name\", \"$surname\", \"1\", \"\", \"\", \"0\", \"0\", \"1\", \"1\", \"1\", \"1\", \"1\")";
$result = $conn->query($sql);
if ($result == 0) {
	alert("danger", "Ошибка при добавлении пользователя $surname $name [$email] в БД. Сообщение от БД: $result", "admin.users.php");
}

# Send email to the User
$subject = 'Заказ еды';
$body = '<html><head><meta charset=\"UTF-8\"></head><body>' . $name . ',<br><br>Вы зарегистрированы на сайте для заказа еды.<br>

Сайт: <a href="http://eda.adalisk.com" target="_blank">http://eda.adalisk.com</a><br>
Логин: ' . $email . '<br>
Пароль: ' . $password . '<br><br>

У нас две компании-поставщика обедов: «Цимус» и «Адам».<br>
Обеды от «Цимуса» нужно заказать до 16 часов четверга.<br>
Обеды от «Адама» нужно заказать до 16 часов пятницы.<br>
Если вы закажете позже указанного времени – часть обедов может не прийти.<br>
Можно делать заказы от любого из поставщиков, можно сразу от обоих.<br>
Примерный лимит на заказ - 350 рублей/день.<br>

<h6>Не отвечайте на это письмо! Оно было отправлено роботом. По всем вопросам пишите на aananyev@adalisk.com<h6></body></html>';
$result = email_send($subject, $send_email_from, $send_email_from_pass, $email, $body);

# Show result message
if ($result) {
	alert("danger", "Ошибка при создании пользователя $surname $name [$email]. Сообщение от почтового клиента: $result", "admin.users.php");
}
else {
	alert("success", "Пользователь <b>$surname $name</b> создан. Письмо с данными для авторизации отправлено на электронный адрес <b>$email</b>", "admin.users.php");
}

function generatePassword() {
	$dict = ['AdjustBite', 'BitePenetration', 'ContactSpot', 'CementGapFlat', 'CrossSection', 'DistalContact', 'FreeForm', 'GingivalContact',
		     'LowerJaw', 'MarginLine', 'MinThickness', 'OcclusalContact', 'Reroute', 'SculptKnife', 'ToolRadius'];
    return $dict[array_rand($dict)] . strval(mt_rand(0, 10000));
}