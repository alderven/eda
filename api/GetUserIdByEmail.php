<?php
/*
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
*/

// 1. Get Email.
$email = isset($_GET['email']) ? $_GET['email'] : '';

// 2. Check if Email is empty.
if ($email === '') {
	
	print 'Ошибка. Пустой параметр "email".';
}
else {
	
	// 3. Encode DB symbols.
	require_once "./../config.php";
	$sql = "SET NAMES utf8";
	$conn->query($sql);
	
	// 4 Generate SQL query without User Id.
	$sql = "SELECT Id FROM $table_users WHERE Login = '$email'";
	
	//  5. Make SQL request.
	$result = $conn->query($sql);
	$rows = array();
	 while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		
	// 6. If Email not found.
	if (count($rows) == 0){
		#print json_encode("Ошибка. Пользователь не найден.", JSON_UNESCAPED_UNICODE);
		print "Ошибка. Пользователь не найден.";
	}
	else {
		
		// 6. Print result as JSON.
		print json_encode($rows[0], JSON_UNESCAPED_UNICODE);
	}
	
	// 6. Close DB connection.
	$conn->close();
}
?>