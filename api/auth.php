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
			$mypassword = $credentials[1];
			
			# 4. Get hashed password from DB
			require_once "../config.php";
			$sql = "SELECT Id, Password FROM users WHERE Login = '$login'";
			$result = $conn->query($sql);
			
			# 5. Check if user with such login exist in DB
			if ($result->num_rows === 0) {
				http_response_code(401);
				exit;
			}
			
			# 6. Get hashed password from DB
			while ($row = $result->fetch_assoc()) {
				$password_hashed = $row["Password"];
				$user_id = $row["Id"];
			}

			# 7. Verify password
			if (password_verify($mypassword, $password_hashed)) {}
			else {
				http_response_code(401);
				exit;
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