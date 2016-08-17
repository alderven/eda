<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

$companyName= isset($_GET['companyName']) ? $_GET['companyName'] : '';
$_SESSION['companyName'] = $companyName;

print 'PHP companyName: ' . $companyName;

$sql = "SELECT * FROM $table_food WHERE Company = '" . $_SESSION['companyName'] . "'";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
        print $row["Name"];
    }

$conn->close();
?>