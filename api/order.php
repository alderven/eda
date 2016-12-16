<?php

# 1. Auth in Service
include 'auth.php';

# 2. Get Dish Id
$data = json_decode(file_get_contents('php://input'), true);
$dish_id = $data["dishId"];
if(is_null($dish_id))
{
	$arr = array('error' => 'DishId parameter not set');
	print json_encode($arr);
	http_response_code(400);
	exit;
}

# 3. Check whether MenuItemId already exist
$count = 0;
$sql = "SELECT Count FROM orders WHERE UserId = $user_id AND MenuItemId = $dish_id";
$result = $conn->query($sql);

# 4. Increment Count if such Order already exist
if ($result->num_rows > 0)
{
	while($row = $result->fetch_assoc()) {
		$count = $row["Count"];
	}
	
	$sql = "UPDATE orders SET Count = " . ++$count . " WHERE UserId = $user_id AND MenuItemId = $dish_id";
	$result = $conn->query($sql);
}
else
{

# 5. Create new Order item if Order not exist
$sql = "INSERT INTO orders (UserId, MenuItemId, Count) VALUES ($user_id, $dish_id, 1)";
$result = $conn->query($sql);
}

if ($result == 1) {
	print 'OK';
}
else {
$arr = array('error' => 'Unable to add dish to DB');
print json_encode($arr);
http_response_code(500);
exit;
}

# 3. Print result as JSON.
#print json_encode($result, JSON_UNESCAPED_UNICODE);

# 4. Close DB connection.
$conn->close();
?>