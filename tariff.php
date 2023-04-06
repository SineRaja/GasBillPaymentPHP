<?php
session_start(); 
if(!isset($_SESSION['cid']) || $_SESSION['type'] != 'admin'){
   header("location:index.php");
}
include "dbcon/db.php";
include "include/func.php";
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Tariff</title>
	<?php include "include/csslink.php"; ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top"  style="background-color:#b8860b;">
	  <a class="navbar-brand" href="#"><h1 style="color: blue;">IGSE</h1></a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarNav">
	    <ul class="navbar-nav ml-auto">
	      <li class="nav-item">
	        <a class="nav-link" href="igse_dash.php"><i class="fa fa-home mr-1"></i>Home</a>
	      </li>
	      <li class="nav-item active">
	        <a class="nav-link" href="tariff.php"><i class="fa fa-gbp mr-1"></i>tariff</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="igse_customer.php"><i class="fa fa-user-circle mr-1"></i>Customers</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out mr-1" aria-hidden="true"></i>logout</a>
	      </li>
	    </ul>
	  </div>
	</nav>
	<div class="w-100 text-center pt-150">
		<div class="text-center" id="toast"></div>
	</div>
	<h4 class="text-center my-2"  style="color:#b8860b">Set Tariff</h4>
   <div class="container my-3">
   	<div class="row">
   		<div class="col col-12 col-md-6 offset-md-3 col-lg-6 offset-lg-3">
   			<div class="card p-2"  style="background-color:#bdb76b">
					<form id="setTariff">
						<div class="text-left p-2">
							<label for="meter_day" class="font-weight-bold">Electricity Meter Reading(Day) Price:</label><br>
							<input type="text" name="m_day_unit" class="form-control" value="<?= tariffPrice($conn,'electricity_day') ?>">
						</div>
						<div class="text-left p-2">
							<label for="meter_night" class="font-weight-bold">Electricity Meter Reading(Night) Price:</label><br>
							<input type="text" name="m_night_unit" class="form-control" value="<?= tariffPrice($conn,'electricity_night') ?>">
						</div>
						<div class="text-left p-2">
							<label for="gas_meter" class="font-weight-bold">Gas Meter Reading Price:</label><br>
							<input type="text" name="g_meter_unit" class="form-control" value="<?= tariffPrice($conn,'gas') ?>">
						</div>
						<div class="text-left p-2">
							<label for="gas_meter" class="font-weight-bold">Meter Standing Price:</label><br>
							<input type="text" name="standing_price" class="form-control" value="<?= tariffPrice($conn,'sanding_charge') ?>">
						</div>
						<div class="p-2" style="background-color:#48d1cc">
							<input type="submit" name="submit" id="submit" value="Submit" class="btn btn-block btn-dark">
						</div>
					</form>
   			</div>
   		</div>
   	</div>
   </div>
	<?php include "include/script.php"; ?>
	<script src="dist/validate.js"></script>
	<script>
		$("#setTariff").validate({
			rules:{
				meter_day_unit: {
					required: true,
					number: true
				},
				meter_night_unit: {
					required: true,
					number:true
				},
				gas_meter_unit: {
					required: true,
					number:true
				},
				standing_price:{
					required: true,
					number:true
				}
			},
			messages: {
				meter_day_unit: {
					required: "Please Enter Electricity Meter Reading Day Price"
				},
				meter_night_unit: {
					required: "Please Enter Electricity Meter Reading Night Price"
				},
				gas_meter_unit: {
					required: "Please Set Gas Reading Price"
				},
				standing_price_unit:{
					required: "Please Set Standing Charge"
				}
			}
		});
		$("#setTariff").on("submit", function(e) {
			e.preventDefault();
			e.stopImmediatePropagation();
			if ($("#setTariff").valid()) {
				$.ajax({
					url: "ajax/ajax_request.php",
					method: "POST",
					data: $("#setTariff").serialize() + "&action=setTariffForm",
					dataType: 'json',
					beforeSend:function(){
						$("#submit").prop("disabled",true);
					},
					success: function(response) {
						if (response.status == 1) {
							$("#setTariff")[0].reset();
							myToast(response.status,response.message);
							setTimeout(function(){
								location.reload();
							},3000);
						} else {
							$("#submit").prop("disabled",true);
							myToast(response.status,response.message);
						}
					}
				})
			}
		})
	</script>

</body>
</html>