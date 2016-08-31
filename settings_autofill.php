<?php
require_once "header.php";
session_start();
if(!isset($_SESSION['myusername'])) {
header("location:index.php");
}
require_once "config.php";
?>

<!-- http://bootsnipp.com/snippets/featured/funky-radio-buttons -->
<style>
@import('https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.0/css/bootstrap.min.css') 

.funkyradio div {
  clear: both;
  overflow: hidden;
}

.funkyradio label {
  width: 100%;
  border-radius: 3px;
  border: 1px solid #D1D3D4;
  font-weight: normal;
}

.funkyradio input[type="radio"]:empty,
.funkyradio input[type="checkbox"]:empty {
  display: none;
}

.funkyradio input[type="radio"]:empty ~ label,
.funkyradio input[type="checkbox"]:empty ~ label {
  position: relative;
  line-height: 2.5em;
  text-indent: 3.25em;
  margin-top: 2em;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}

.funkyradio input[type="radio"]:empty ~ label:before,
.funkyradio input[type="checkbox"]:empty ~ label:before {
  position: absolute;
  display: block;
  top: 0;
  bottom: 0;
  left: 0;
  content: '';
  width: 2.5em;
  background: #D1D3D4;
  border-radius: 3px 0 0 3px;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label {
  color: #888;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label:before,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label:before {
  content: '\2714';
  text-indent: .9em;
  color: #C2C2C2;
}

.funkyradio input[type="radio"]:checked ~ label,
.funkyradio input[type="checkbox"]:checked ~ label {
  color: #777;
}

.funkyradio input[type="radio"]:checked ~ label:before,
.funkyradio input[type="checkbox"]:checked ~ label:before {
  content: '\2714';
  text-indent: .9em;
  color: #333;
  background-color: #ccc;
}

.funkyradio input[type="radio"]:focus ~ label:before,
.funkyradio input[type="checkbox"]:focus ~ label:before {
  box-shadow: 0 0 0 3px #999;
}

.funkyradio-default input[type="radio"]:checked ~ label:before,
.funkyradio-default input[type="checkbox"]:checked ~ label:before {
  color: #333;
  background-color: #ccc;
}

.funkyradio-primary input[type="radio"]:checked ~ label:before,
.funkyradio-primary input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #337ab7;
}

.funkyradio-success input[type="radio"]:checked ~ label:before,
.funkyradio-success input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #5cb85c;
}

.funkyradio-danger input[type="radio"]:checked ~ label:before,
.funkyradio-danger input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #d9534f;
}

.funkyradio-warning input[type="radio"]:checked ~ label:before,
.funkyradio-warning input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #f0ad4e;
}

.funkyradio-info input[type="radio"]:checked ~ label:before,
.funkyradio-info input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #5bc0de;
}
</style>

<script>
angular.module('app', [])
	.controller('autofill', function($scope, $http) {
		var vm = this;

		// Save Autofill Settings
		vm.saveAutofillSettings = function(settingName, value) {
			var url = 'settings_autofill_save.php?settingName=' + settingName + '&value=' + value;
			$http.get(url);
		};
	});
</script>
<?php

print '<body ng-app="app" ng-controller="autofill as vm">';

# 1. Display Navigation Bar
print $navigationBar;

print '<div align="center"><h1>Настройка автозаполнения</h1></div><br>';

# 2. Get User Autofill Settings
$sql = "SELECT * FROM $table_users WHERE Id = $user_id";
	$result = $conn->query($sql);
	 while ($row = $result->fetch_assoc()) {
		$autofillType = $row['AutofillSetting_AutofillType'];
		$autofillCompany = $row['AutofillSetting_Company'];
		$AutofillSetting_OrderFirstDishes = $row['AutofillSetting_OrderFirstDishes'];
		$AutofillSetting_OrderSecondDishes = $row['AutofillSetting_OrderSecondDishes'];
		$AutofillSetting_OrderSalads = $row['AutofillSetting_OrderSalads'];
		$AutofillSetting_OrderDesserts = $row['AutofillSetting_OrderDesserts'];
    }

# 3. Autofill Settings: Type
$autofillTypeCheckedStatus = array('', '', '');
$autofillTypeCheckedStatus[$autofillType] = 'checked';

# 4. Autofill Settings: Company
$autofillCompanyCheckedStatus = array('', '', '');
$autofillCompanyCheckedStatus[$autofillCompany] = 'checked';

# 5. Autofill Settings: First/Second/Salads/Dessers
$AutofillSetting_OrderFirstDishes_CheckedStatus = ($AutofillSetting_OrderFirstDishes == 1) ? 'checked' : '';
$AutofillSetting_OrderSecondDishes_CheckedStatus = ($AutofillSetting_OrderSecondDishes == 1) ? 'checked' : '';
$AutofillSetting_OrderSalads_CheckedStatus = ($AutofillSetting_OrderSalads == 1) ? 'checked' : '';
$AutofillSetting_OrderDesserts_CheckedStatus = ($AutofillSetting_OrderDesserts == 1) ? 'checked' : '';

	
# 6. Print Table
print '
		<div class="container">
			<div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
				<table class="table table-bordered">
				<tbody><tr><td>
					<div class="funkyradio">
						<div class="funkyradio-success">
							<input type="radio" ng-click="vm.saveAutofillSettings(\'AutofillSetting_AutofillType\', 0)" name="radio1" id="radio1" ' . $autofillTypeCheckedStatus[0] . '/>
							<label for="radio1">Случайные блюда</label>
						</div>
						<div class="funkyradio-info">
							<input type="radio" ng-click="vm.saveAutofillSettings(\'AutofillSetting_AutofillType\', 1)" name="radio1" id="radio2" ' . $autofillTypeCheckedStatus[1] . '/>
							<label for="radio2">Любимые блюда</label>
						</div>
						<div class="funkyradio-warning">
							<input type="radio" ng-click="vm.saveAutofillSettings(\'AutofillSetting_AutofillType\', 2)" name="radio1" id="radio3" ' . $autofillTypeCheckedStatus[2] . '/>
							<label for="radio3">Расточительный заказ</label>
						</div>
					</div>
							</td></tr>
						</tbody>
					</table>
					
									<table class="table table-bordered">
				<tbody><tr><td>
					<div class="funkyradio">
						<div class="funkyradio-success">
							<input type="radio" ng-click="vm.saveAutofillSettings(\'AutofillSetting_Company\', 0)" name="radio" id="radio4" ' . $autofillCompanyCheckedStatus[0] . '/>
							<label for="radio4">Заказывать везде</label>
						</div>
						<div class="funkyradio-info">
							<input type="radio" ng-click="vm.saveAutofillSettings(\'AutofillSetting_Company\', 1)" name="radio" id="radio5" ' . $autofillCompanyCheckedStatus[1] . '/>
							<label for="radio5">Заказывать только в Цимусе</label>
						</div>
						<div class="funkyradio-warning">
							<input type="radio" ng-click="vm.saveAutofillSettings(\'AutofillSetting_Company\', 2)" name="radio" id="radio6" ' . $autofillCompanyCheckedStatus[2] . '/>
							<label for="radio6">Заказывать только у Адама</label>
						</div>
					</div>
					
												</td></tr>
						</tbody>
					</table>
					
									<table class="table table-bordered">
				<tbody><tr><td>
					
					<div class="funkyradio">
						<div class="funkyradio-success">
							<input type="checkbox" ng-click="vm.saveAutofillSettings(\'AutofillSetting_OrderFirstDishes\')" name="AutofillSetting_OrderFirstDishes" id="AutofillSetting_OrderFirstDishes" ' . $AutofillSetting_OrderFirstDishes_CheckedStatus . '/>
							<label for="AutofillSetting_OrderFirstDishes">Первые блюда</label>
						</div>
						<div class="funkyradio-info">
							<input type="checkbox" ng-click="vm.saveAutofillSettings(\'AutofillSetting_OrderSecondDishes\')" name="AutofillSetting_OrderSecondDishes" id="AutofillSetting_OrderSecondDishes" ' . $AutofillSetting_OrderSecondDishes_CheckedStatus . '/>
							<label for="AutofillSetting_OrderSecondDishes">Вторые блюда</label>
						</div>
						<div class="funkyradio-warning">
							<input type="checkbox" ng-click="vm.saveAutofillSettings(\'AutofillSetting_OrderSalads\')" name="AutofillSetting_OrderSalads" id="AutofillSetting_OrderSalads" ' . $AutofillSetting_OrderSalads_CheckedStatus . '/>
							<label for="AutofillSetting_OrderSalads">Салаты</label>
						</div>
						<div class="funkyradio-danger">
							<input type="checkbox" ng-click="vm.saveAutofillSettings(\'AutofillSetting_OrderDesserts\')" name="AutofillSetting_OrderDesserts" id="AutofillSetting_OrderDesserts"  ' . $AutofillSetting_OrderDesserts_CheckedStatus . '/>
							<label for="AutofillSetting_OrderDesserts">Выпечка и десерты</label>
						</div>
							</div>
							</td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>       
	</div>	
</div>';

# 7. Close DB connection
$conn->close();

# 8. Add Footer
require_once "footer.php";
 ?>