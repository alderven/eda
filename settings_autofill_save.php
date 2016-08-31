<?php
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";

# 1. Get Setting Name
foreach ($_GET as $key => $value) {
	
	# Debug:
	# print 'Key: ' . $key . '\t';
	# print 'Value: ' . $value . '\t';
	
	# 1.1 Get Setting Name
	if ($key == 'settingName') {
		$settingName = $value;
	}
	
	# 1.2 Get Checkbox Value
	if ($value == 'undefined') {
		
		# Get Setting Value from DB
		$sql = "SELECT $settingName FROM $table_users WHERE Id = $user_id";
		$result = $conn->query($sql);
		 while ($row = $result->fetch_assoc()) {
				$settingValue = $row[$settingName];
		   }
		  
		# Invert Setting Value (we assume that it was modified by User to the opposite value)
		$settingValue = ($settingValue == 1) ? 0 : 1;
	}
	# 1.3 Get Radiobutton Value
	else {
		$settingValue = $value;
	}
}

# 2. Save Setting to DB
$sql = "UPDATE $table_users SET $settingName = $settingValue WHERE Id = $user_id";
$result = $conn->query($sql);

# 3. Close DB connection
$conn->close();
?>