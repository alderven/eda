<?php
require_once "config.php";

$sql = "SELECT Id, Password FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0)
{
	while($row = $result->fetch_assoc()) {
		$id = $row["Id"];
		$password = $row["Password"];
		
		$new_password = password_hash($password, PASSWORD_DEFAULT);

		print $id . ' ' . $password . ' ' . $new_password . '<br>';
		
		$sql_1 = "UPDATE users SET Password = \"$new_password\" WHERE Id = $id";
		$result_1 = $conn->query($sql_1);
	}
}
$conn->close();
?>