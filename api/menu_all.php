<?php

# 1. Auth in Service
include 'auth.php';

# 2. Get Menu from DB
$sql = "SELECT Id, Date, Type, Name, Weight, Price, Company FROM $table_food
		WHERE Date >= CURDATE()
		ORDER BY Date";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$rows = array();
	 while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
}
# 3. Print result as JSON.
print json_encode($rows, JSON_UNESCAPED_UNICODE);

# 4. Close DB connection.
$conn->close();
?>