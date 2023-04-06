<?php 
session_start();
if(!isset($_SESSION['cid'])){
   header('Location:index.php');
}
include 'dbcon/db.php';
$customer_id=trim($_SESSION['email']);
$meter_day=$meter_night=$meter_gas=0;
$submitted_date=date('Y-m-d');
$qry=mysqli_query($conn,"SELECT * FROM reading WHERE customer_id='$customer_id' ORDER BY submission_date DESC LIMIT 1");
if(mysqli_num_rows($qry) > 0){
   	$arr=mysqli_fetch_assoc($qry);
    $meter_day=$arr["elec_readings_day"];
    $meter_night=$arr["elet_reading_night"];
    $meter_gas=$arr["gas_reading"];
    $submitted_date=date('Y-m-d',strtotime($arr["submission_date"]));
}else{
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Meter Reading</title>
	<?php include "include/csslink.php"; ?>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color:#b8860b;">
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
					<a class="nav-link" href="igse_m_readings.php"><i class="fa fa-tachometer mr-1"></i>Readings</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="pay_bills.php"><i class="fa fa-gbp mr-1" aria-hidden="true"></i>Bills</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="credit.php"><i class="fa fa-credit-card mr-1" aria-hidden="true"></i>Credit</a>
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
	<div class="container mb-3">
		<div class="row">
			<div class="col col-12 col-md-8 col-lg-8 offset-md-2 offset-lg-2">
				<div class="card p-3 text-center" style="background-color:#bdb76b">
					<h4>Meter Reading</h4>
					<form id="meterReading">
						<div class="text-left p-2">
							<label for="submitted_date" class="font-weight-bold">Submitted Date:</label><br>
							<input type="date" name="submit_date" class="form-control" min="<?= $submitted_date; ?>">
							<input type="hidden" name="customer_id" value="<?= $_SESSION['email']?>">
						</div>
						<div class="text-left p-2">
							<label for="meter_day" class="font-weight-bold">Electricity Meter Reading(Day):</label><br>
							<input type="text" name="m_day" class="form-control" placeholder="Previous Reading is <?= $meter_day ?>">
						</div>
						<div class="text-left p-2">
							<label for="meter_night" class="font-weight-bold">Electricity Meter Reading(Night):</label><br>
							<input type="text" name="m_night" class="form-control" placeholder="Previous Reading is <?= $meter_night ?>">
						</div>
						<div class="text-left p-2">
							<label for="gas_meter" class="font-weight-bold">Gas Meter Reading:</label><br>
							<input type="text" name="g_meter" class="form-control" placeholder="Previous Reading is <?= $meter_gas ?>">
						</div>
						<div class="p-2" style="background-color:#48d1cc">
							<input type="submit" name="submit" id="submit" value="Submit Reading" class="btn btn-block btn-dark">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php include "include/script.php"; ?>
	<script src="dist/validate.js"></script>
	<script>
		$("#meterReading").validate({
			rules:{
				submit_date: {
					required: true,
				},
				m_day: {
					required: true,
					minValue:<?= $meter_day; ?>,
					number: true
				},
				m_night: {
					required: true,
					number:true,
					minValue:<?= $meter_night; ?>
				},
				g_meter: {
					required: true,
					number:true,
					minValue:<?= $meter_gas; ?>
				}
			},
			messages: {
				submit_date: {
					submit_date: "Please enter Submission date"
				},
				m_day: {
					required: "Please Enter Electricity Meter Reading Day",
					minValue:"Your reading must be greater than previous Reading"
				},
				m_night: {
					required: "Please Enter Electricity Meter Reading Night",
					minValue:"Your reading must be greater than previous Reading"
				},
				g_meter: {
					required: "Please Provide Gas Meter Reading",
					minValue:"Your reading must be greater than previous Reading"
				}
			}
		});
		$.validator.addMethod('minValue', function (value, el, param) {
		    return value > param;
		});
		$("#meterReading").on("submit", function(e) {
			e.preventDefault();
			e.stopImmediatePropagation();
			if ($("#meterReading").valid()) {
				$.ajax({
					url: "ajax/ajax_request.php",
					method: "POST",
					data: $("#meterReading").serialize() + "&action=MeterReadingForm",
					dataType: 'json',
					beforeSend:function(){
						$("#submit").prop("disabled",true);
					},
					success: function(response) {
						if (response.status == 1) {
							$("#meterReading")[0].reset();
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