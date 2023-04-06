<?php
session_start(); 
if(!isset($_SESSION['cid'])){
   header("location:index.php");
}
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $_SESSION["type"] ?> Dashboard</title>
	<?php include "include/csslink.php"; ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color:#b8860b;">
	  <a class="navbar-brand" href="#"><h1 style="color: blue; text-align: center;">IGSE</h1></a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarNav">
	    <ul class="navbar-nav ml-auto">
	      <li class="nav-item active">
	        <a class="nav-link" href="#"><i class="fa fa-home mr-1"></i>Home</a>
	      </li>
	      <?php if($_SESSION['type'] == 'admin'){ ?>
	      <li class="nav-item">
	        <a class="nav-link" href="tariff.php"><i class="fa fa-gbp mr-1"></i>tariff</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="igse_customer.php"><i class="fa fa-user-circle mr-1"></i>Customers</a>
	      </li>
          <?php }else{ ?>
	      <li class="nav-item">
	        <a class="nav-link" href="igse_m_readings.php"><i class="fa fa-tachometer mr-1"></i>Readings</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="pay_bills.php"><i class="fa fa-euro mr-1" aria-hidden="true"></i>Bills</a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="credit.php"><i class="fa fa-credit-card mr-1" aria-hidden="true"></i>Credit</a>
	      </li>
	      <?php } ?>
	      <li class="nav-item">
	        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out mr-1" aria-hidden="true"></i>logout</a>
	      </li>
	    </ul>
	  </div>
	</nav>
	<h3 class="pt-150 text-center m-3">Welcome to <?= $_SESSION['type']; ?> Dashboard</h3>
	<?php include "include/script.php"; ?>
</body>
</html>