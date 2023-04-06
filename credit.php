<?php 
session_start();
if(!isset($_SESSION['cid'])){
   header('Location:index.php');
}
include 'dbcon/db.php';
$email=$_SESSION['email'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Credit</title>
	<?php include "include/csslink.php"; ?>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light fixed-top"   style="background-color:#b8860b;">
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
				<li class="nav-item">
					<a class="nav-link" href="pay_bills.php"><i class="fa fa-euro mr-1" aria-hidden="true"></i>Bills</a>
				</li>
				<li class="nav-item active">
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
			<div class="col-12 col-md-6 col-lg-6 offset-md-3 offset-lg-3">
				<h3 class="text-center p-1" style="color: #800080">Account Balance</h3>
				<div class="card p-3"  style="background-color:#bdb76b;">
					<p>You Account balance is <span class="text-danger font-weight-bold" id="accBlnc"></span></p>
					<div class="mx-3">
						<p>Use Top up Voucher to recharge your account</p>
						<label class="font-weight-bold">EVC Code:</label>
						<input type="text" id="v_id" class="form-control"><br>
						<input type="hidden" value="<?= $email; ?>" id="email">
						<button id="submitEvc" class="btn btn-success"  style="background-color: #008080;">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- MODAL -->
	<div class="modal fade" id="SuccessModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-body">
	      	 <div class="text-center">
	      	 	  <p class="p-3">Account Balance added Successfully....</p>
	      	 	  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	      	 </div>     
	      </div>
	    </div>
	  </div>
	</div>
	<?php include "include/script.php"; ?>
	<script src="js/functions.js"></script>
	<script>
      $("body").delegate("#submitEvc","click",function(){
      	let vid=$('#v_id').val().trim();
      	let email=$('#email').val().trim();
      	if(vid != ''){
             $.ajax({
             	url:"ajax/ajax_request.php",
             	method:"POST",
             	data:{voucher_recharge:1,vid:vid,email:email},
             	dataType:"json",
             	success:function(response){
             		if(response.status == 1){
             			fetchBalance()
                     jq("#SuccessModal").modal("show");
                     $('#v_id').val('');
             		}else{
                     myToast(response.status,response.message);
             		}
             	}
             })
      	}else{
             myToast(0,"Please enter EVC code...");
      	}	
      });
      fetchBalance()
      function fetchBalance(){
      	let email=$('#email').val().trim();
	       $.ajax({
	       	url:"ajax/ajax_request.php",
	       	method:"POST",
	       	data:{fetchBalance:1,email:email},
	       	dataType:"json",
	       	success:function(response){
               $("#accBlnc").html(`<i class="fa fa-gbp mx-1"></i>${response}`);
	       	}
	       })
      }
		
	</script>
</body>
</html>
