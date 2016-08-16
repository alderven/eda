<?php
$servername = 'localhost';
$username = 'root';
$password = '12345';
$db_name = 'eda';
$table_users = 'users';
$table_food = 'food';
$table_orders = 'orders';
$table_filters = 'filters';

$site_url = 'http://eda/';
# $send_email_from = 'eda@adalisk.com';
# $send_email_from_pass = 'EdaAdalisk';

$GLOBALS['send_email_from'] = 'eda@adalisk.com';
$GLOBALS['send_email_from_pass'] = 'EdaAdalisk';

// Autofill settings list
$autofillSettings = array('AutofillSetting_OrderInCimus', 'AutofillSetting_OrderInAdam', 'AutofillSetting_OrderFirstDishes', 'AutofillSetting_OrderSecondDishes', 'AutofillSetting_OrderSalads', 'AutofillSetting_OrderDesserts', 'AutofillSetting_Rich', 'AutofillSetting_FavoriteDishes');

// Create connection
$conn = new mysqli($servername, $username, $password, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>