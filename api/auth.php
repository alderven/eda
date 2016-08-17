<?php

$user_id = Null;

# 1. Read the Get Request Headers
foreach (getallheaders() as $name => $value) {
	# 2. Look for Authorization Header
	if ($name == 'Authorization') {
		# 3. Retrieve Login and Password
		error_reporting(0);
		try {
			$encoded_creds = explode(' ', $value)[1];
			$decoded_creds = base64_decode($encoded_creds);
			$credentials = explode(':', $decoded_creds);
			$login = $credentials[0];
			$pass = $credentials[1];
			
			# 4. Validate Login and Password
			require_once "../config.php";
			$sql = "SET NAMES utf8";
			$conn->query($sql);
			$sql = "SELECT Id FROM $table_users WHERE Login = '$login' AND Password = '$pass'";
			$result = $conn->query($sql);
			if ($result->num_rows === 1) {
				while ($row = $result->fetch_assoc()) {
					$user_id = $row["Id"];
					}
				}
			}
		catch (Exception $e) {}
	}
}

error_reporting(E_ERROR | E_WARNING | E_PARSE);
if (is_null($user_id)) {
	http_response_code(401);
	exit;
}
?>