<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<style>
#enabled_1, #enabled_0 {
border: 2px dotted #999999;
width: 450px;
min-height: 20px;
list-style-type: none;
margin: 0;
padding: 5px 0 0 0;
float: left;
margin-right: 10px;
}
#enabled_1 li, #enabled_0 li {
margin: 0 5px 5px 5px;
padding: 5px;
font-size: 1.2em;
width: 430px;
}
</style>
<script>
$(function() {

$( "#enabled_1, #enabled_0").sortable({
  items: "li:not(.ui-state-disabled)"
});
	
$("#enabled_1, #enabled_0").sortable({
	connectWith: ".connectedSortable",
    update: function (event, ui) {
        //var data = $(this).sortable('serialize');
		var enabled = $(this).sortable( "widget" )[0].id.slice(-1);
		var data = $(this).sortable( "toArray" );

		// Make HTTP POST request
		//$.post( "SaveFilter.php", function( data ));
		$.post("SaveFilter.php",
		{
			enabled: enabled,
			data: data
		}
		/* For debug:
		, function(data, status){
			alert(data);
		}*/
		);
		
    }
}).disableSelection();
});
</script>

<?php

print '<body>';

# 1. Display Navigation Bar
print $navigationBar;

print '<div align="center"><h1>Настройка ручного фильтра</h1></div><br>';

// 3. Create Table according to the Custom Filters
// 3.1 Create 'Left' table
print '<ul align="center" id="enabled_1" class="connectedSortable">
<li class="ui-state-disabled"><h2><b>Фильтр</b></h2>
	<h5>- Отсортируйте категории блюд по приоритету<br>- Отправьте ненужные в корзину</h5></li>';
$sql = "SELECT FoodType, Company FROM $table_filters WHERE UserId = $user_id AND Enabled = 1 ORDER BY Priority ASC";
$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	 print '<li id="' . $row["FoodType"] . '|' . $row["Company"] . '" class="ui-state-default">' . $row["FoodType"] . ' (' . $row["Company"] . ')</li>';
}
print '</ul>';

// 3.2 Create 'Trash' table
print '<ul align="center" id="enabled_0" class="connectedSortable">
<li class="ui-state-disabled"><h2><b>Корзина</b></h2>
	<h5>- Перетащите сюда категории блюд, которые вы не хотите заказывать</h5></li>';
$sql = "SELECT * FROM $table_filters WHERE UserId = $user_id AND Enabled = 0 ORDER BY Priority ASC";
$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	 print '<li id="' . $row["FoodType"] . '|' . $row["Company"] . '" class="ui-state-default">' . $row["FoodType"] . ' (' . $row["Company"] . ')</li>';
}
print '</ul>';

// 4. Close DB connection
$conn->close();

// 5. Add Footer
require_once "footer.php";
 ?>