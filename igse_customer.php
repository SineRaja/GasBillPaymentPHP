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
	<title>Customers</title>
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
	      <li class="nav-item">
	        <a class="nav-link" href="tariff.php"><i class="fa fa-gbp mr-1"></i>tariff</a>
	      </li>
	      <li class="nav-item active">
	        <a class="nav-link" href="igse_customer.php"><i class="fa fa-user-circle mr-1"></i>Customers</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out mr-1" aria-hidden="true"></i>logout</a>
	      </li>
	    </ul>
	  </div>
	</nav>
	<div class="pt-150" >
		<h2 class="text-center p-2" style="font-family:Fantasy;" >Customers</h2>
	</div>
	<div class="container-fluid my-3">
		<div class="row p-1">
			<div class="col col-12 col-lg-12 col-md-12">
				<div class="card my-4" style="overflow-x:scroll;">
					<table class="table table-striped">
						<thead style="background-color:#b8860b">
							<tr>
								<td>Customer ID</td>
								<td>Electricity(KWh)</td>
								<td>Gas(KWh)</td>
								<td>Last Due</td>
								<td>Submission</td>
								<td>Status</td>
								<td>Stats</td>
							</tr>
						</thead>
						<tbody  style="background-color:#bc8f8f">
							<?php
							$qry=mysqli_query($conn,"select * FROM (select customer_id,submission_date,elec_readings_day,elet_reading_night,gas_reading,total_bill,status,ROW_NUMBER() over (partition by customer_id ORDER BY submission_date DESC) as MAX_ID from reading) x WHERE MAX_ID = 1");
							if($qry){
								while($arr=mysqli_fetch_assoc($qry)){
?>
							<tr>
								<td><?= $arr["customer_id"] ?></td>
								<td><?= $arr["elec_readings_day"]+$arr["elet_reading_night"] ?></td>
								<td><?= $arr["gas_reading"] ?></td>
								<td><i class="fa fa-gbp mr-1"></i><?= $arr["total_bill"] ?></td>
								<td><?= $arr["submission_date"] ?></td>
								<td><?= $arr["status"] ?></td>
								<td><a class="btn btn-sm btn-success" href="stats.php?id=<?= base64_encode($arr["customer_id"]) ?>"><i class="fa fa-line-chart"></i> Stats</a></td>
							</tr>
<?php
								}
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php include "include/script.php"; ?>
</body>
</html>