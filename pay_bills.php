<?php 
session_start();
if(!isset($_SESSION['cid'])){
   header('Location:index.php');
}
include "dbcon/db.php";
$email=$_SESSION['email'];
$qry=mysqli_query($conn,"SELECT * FROM customer WHERE customer_id='$email'");
$arr=mysqli_fetch_assoc($qry);
$bills=mysqli_query($conn,"SELECT * FROM reading WHERE customer_id='$email' AND status='Pending' ORDER BY submission_date DESC");
$total_due=$total_day_reading=$total_night_reading=$total_gas_reading=$total_days=0;
$date1=$date2="00-00-0000";
$total_bill=0;
if(mysqli_num_rows($bills) < 1){
	$total_bill=0;
}else{
	while($arrBills=mysqli_fetch_assoc($bills)){
	$total_bill+=$arrBills["total_bill"];
	}
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
					<a class="nav-link" href="igse_m_readings.php"><i class="fa fa-tachometer mr-1"></i>Readings</a>
				</li>
				<li class="nav-item active">
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
	<div class="container-fluid my-3">
		<div class="row">
			<div class="col-12"  style="background-color:#bc8f8f">
				<h3 class="text-center p-1">Total Bills</h3>
				<div class="card p-3" style="background-color:#bdb76b;">
					<p>Total Due:<span class="text-danger font-weight-bold"><i class="fa fa-gbp mx-1"></i><?= $total_bill ?></span></p>
					<div class="mx-3">
						<a href="javascript:void(0)" class="btn btn-success" id="pay_full">Pay Now</a>
						<input type="hidden" id="countBill" value="<?= $total_bill ?>">
						<input type="hidden" id="balance" value="<?= $arr['balance'] ?>">
						<input type="hidden" id="email" value="<?= $email ?>">
						<a href="credit.php" class="btn btn-primary ml-2">Recharge Now</a>
					</div>  
				   <p class="my-2">Your total Top up Credit Left: <span class="text-success font-weight-bold"><i class="fa fa-gbp mr-1"></i><?= $arr['balance'] ?></span></p>
				</div>
			</div>
		</div>
	</div>
	<h4 class="p-2 my-2 text-center" style="background-color:#bc8f8f">Bill History</h4>
	<div class="bill-table my-3 mx-2" style="overflow-x:scroll;">
		 <table class="table table-striped">
		 	  <thead>
		 	  	   <tr>
		 	  	   	<td>Submitted Date</td>
		 	  	   	<td>Day Reading</td>
		 	  	   	<td>Night Reading</td>
		 	  	   	<td>Gas Reading</td>
		 	  	   	<td>Total Bills</td>
		 	  	   	<td>status</td>
		 	  	   </tr>
		 	  </thead>
		 	  <tbody id="fetch_bill" style="background-color:#bdb76b;">


		 	  </tbody>
		 </table>
	   <div class="modal fade" id="billModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-body">

		      	 <div class="text-center">
		      	 	  <p class="p-3">Oops!! Look like You Don't have enough Account balance to pay your full bill..</p>
		      	 	  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		      	 </div>     
		      </div>
		    </div>
		  </div>
		</div>
		<div class="modal fade" id="SuccessModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-body">

		      	 <div class="text-center">
		      	 	  <p class="p-3">Your bill Paid Successfully....</p>
		      	 	  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		      	 </div>     
		      </div>
		    </div>
		  </div>
		</div>
	</div>
	<?php include "include/script.php"; ?>
	<script src="js/functions.js"></script>
	<script>
		$("body").delegate("#pay_full","click",function(){
			let balance=$("#balance").val();
			let countBill=$("#countBill").val();
			let email=$("#email").val();
			if(balance > countBill){
				 $.ajax({
				 	url:"ajax/ajax_request.php",
	             	method:"POST",
	             	data:{payFull:1,countBill:countBill,balance:balance,email:email},
	             	dataType:"json",
	             	success:function(response){
	             		if(response.status == 1){ 
	             			fetchBills();
	                     jq("#SuccessModal").modal("show");
	                     window.setTimeout(function(){
	                     	location.reload();
	                     },2000);
	             		}else{
	             			myToast(response.status,response.message);
	             		}
	             	}
				 })
			}else{
				jq("#billModal").modal("show");
			}
		})
		$("body").delegate(".payMonthlyBill","click",function(){
			let payID=$(this).attr("payId");
			let bill=$(this).attr("bill");
			let email=$("#email").val();
			if($("#balance").val() > bill){
             $.ajax({
             	url:"ajax/ajax_request.php",
             	method:"POST",
             	data:{payMonthly:1,payID:payID,bill:bill,email:email},
             	dataType:"json",
             	success:function(response){
             		if(response.status == 1){ 
             			fetchBills();
                     jq("#SuccessModal").modal("show");
                     window.setTimeout(function(){
                     	location.reload();
                     },2000);
             		}else{
             			myToast(response.status,response.message);
             		}
             	}
             })
			}else{
				jq("#billModal").modal("show");
			}
		})
		fetchBills();
		function fetchBills(){
			let email=$("#email").val();
			$.ajax({
				url:"ajax/ajax_request.php",
				method:"POST",
				data:{fetchBill:1,email:email},
				dataType:"json",
				success:function(response){
				  let data;
              if(response.length > 0){
              	for(var i in response){
              		let status=(response[i].status== 'Pending') ? `<i class="text-danger">${response[i].status}</i>` : `<i class="text-success">${response[i].status}</i>`;
                  data+=`<tr>
		              <td>${response[i].submission_date}</td>
		              <td>${response[i].elec_readings_day}</td>
		              <td>${response[i].elet_reading_night}</td>
		              <td>${response[i].gas_reading}</td>
		              <td>
		                 <i class="fa fa-gbp mr-1"></i>${response[i].total_bill}
		                </td>
		              <td>
		                <div class="d-block">
		                  <p>${status}</p>
		                  ${(response[i].status== 'Pending') ? 
									 `<a href="javascript:void(0)" class="btn-sm btn-success btn-block payMonthlyBill" payId="${response[i].reading_id}" bill="${response[i].total_bill}">Pay Now</a>`
		                   : ''}	                     
		                  
		                </div>  
		              </td>
		             </tr>`
              	}
              	$("#fetch_bill").html(data);
              }else{
              	data='No Result Found...';
              	$("#fetch_bill").html(data);
              }
				}
			})
		}

	</script>
</body>
</html>
