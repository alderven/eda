<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
require_once "common.php";

# Check for Admin priveleges
is_admin($role_id);

?>

<!-- https://datatables.net/ -->
<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css">
<script>
$(document).ready(function() {
    $('#example').DataTable( {
        "pagingType": "full_numbers",
		"language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.11/i18n/Russian.json"
            },
		//"order": [[ 0, 'asc' ]]
		   'aoColumnDefs': [{
				'bSortable': false,
				'aTargets': [-1] /* 1st one, start by the right */
			}]
    } );
} );
</script>

<body ng-app="app" ng-controller="Users as vm">
<div class="container">

<?php

# Display Navigation Bar
print $navigationBar;

# Display Page Title
print '<div align="center"><h1>Управление пользователями</h1></div><br><br>';

# Display Alerts
if (isset($_SESSION['alert_type']) and isset($_SESSION['alert_text'])) {
	print '<div align="center" class="alert alert-' . $_SESSION['alert_type'] . '">' . $_SESSION['alert_text'] . '</div>';
	unset($_SESSION['alert_type']);
	unset($_SESSION['alert_text']);
}

# Display Buttons
print '
<div class="row">

	<div class="col-sm-3" align="center">
		<button type="button" class="btn btn btn-success disabled" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-cloud-plus"></span> Добавить нового пользователя</button>
		
		<!-- Modal -->
		<div id="myModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Добавление нового пользователя</h4>
					</div>
					<div class="modal-body" align="center">
						<form action="admin.user.add.php" method="post" enctype="multipart/form-data">
							<table>
								<tr>
									<td><input type="file" class="btn btn btn-default" name="uploadfile"></td>
									<td><button type="submit" class="btn btn btn-success"><span class="glyphicon glyphicon-cloud-upload"></span> Загрузить Excel</button></td>
								</tr>
							</table>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	
	<form action="admin.user.login_as.php" method="post">
	
		<div class="col-sm-3" align="center">
			<input type="hidden" name="UserId"/>
			<button type="submit" formaction="admin.user.edit.php" class="btn btn-primary disabled"><span class="glyphicon glyphicon-cloud-edit"></span> Редактировать пользователя</button>
		</div>
		
		<div class="col-sm-3" align="center">
			<button type="submit" formaction="admin.user.login_as.php" name="UserId" class="btn btn-warning"><span class="glyphicon glyphicon-user"></span> Войти как пользователь</button>
		</div>
		
		<div class="col-sm-3" align="center">
			<button type="submit" formaction="admin.user.delete.php" name="UserId" class="btn btn-info disabled"><span class="glyphicon glyphicon-remove"></span> Удалить пользователя</button>
	</div>
	
<br><br><hr>';

# Display Table header
print '
<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th><div align="center">Имя</div></th>
			<th><div align="center">Статус</div></th>
			<th><div align="center">Выбрать</div></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><div align="center">Имя</div></th>
			<th><div align="center">Статус</div></th>
			<th><div align="center">Выбрать</div></th>
		</tr>
	</tfoot>
	<tbody>';

# Display Table content
$sql = "SELECT Id, isActive, Name, Surname FROM users
		WHERE CompanyId = $company_id
		ORDER BY Surname DESC";
		
$result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
	
	$status = ($row["isActive"] == 1) ? 'Активирован' : 'Деактивирован';
	
	print '<tr>
		<td align="left">' . $row["Surname"] . ' ' . $row["Name"]  . '</td>
		<td align="left">' . $status  . '</td>
		<td align="center"><label><input type="radio" checked="checked" value="' . $row["Id"] . '"name="UserId"></label></td>
	</tr>';
 }
	
# Display Table footer
print '</tbody>
		</table>
	</div>
</form>';

require_once "footer.php";
 ?>