<?php
  include "dbcon/db.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>iGSE Customer Register</title>
	<?php include "include/csslink.php"; ?>
</head>

<body>
	<section class="login">
		<div class="w-100 text-center">
			<div class="text-center" id="toast"></div>
		</div>
		
		<div class="pt-100"></div>
		<div class="container mb-30">
			<div class="row">
				<div class="col-12 offset-md-2 col-md-8 offset-lg-2 col-lg-8">
					<div class="card login-style p-4 text-center"  style="background-color:	#7fffd4;">
						<div>
							<h1 style="color: blue; text-align: center;">IGSE</h1>
						</div>
						<form id="registerForm">
							<div class="form-group text-left mt-3">
								<input type="email" name="email" class="form-control" placeholder="Email Address"   style="text-align: center;" required>
							</div>
							<div class="form-group text-left mt-3">
								<input type="password" name="reg_pass" class="form-control" placeholder="Password"   style="text-align: center;"  required>
							</div>
							<div class="form-group text-left mt-3">
								<textarea name="address" class="form-control" rows="4" placeholder="Address"  style=" text-align: center;"  required></textarea>
							</div>
							<div class="form-group text-left mt-3">
								
								<select id="property_type" name="pro_type" class="form-control" style="text-align: center;"  required>
									<option>Select Property Type</option>
						<?php $qry=mysqli_query($conn,"SELECT * FROM pro_type");
						while($arr=mysqli_fetch_assoc($qry)){
							?>
							       <option value="<?= $arr["id"]?>"><?= $arr["name"]; ?></option>
							<?php
						}
						?>
								</select>
							</div>
							<div class="form-group text-left mt-3">
								<input type="text" name="no_of_bedrooms" class="form-control" placeholder="No. Of Bedrooms"   style=" text-align: center;"  required>
							</div>
							<div class="form-group text-left mt-3">
								<input type="text" name="evc" class="form-control" required pattern="[0-9]{8}" placeholder="EVC(8 DIGITS)"   style=" text-align: center; ">
							</div>
							<input type="submit" name="submit" id="submit" value="Login" style=" text-align: center; background-color:#006400;color: white	;" class="btn btn-block">
						</form>
						<div class="d-flex justify-content-center">
							<p class="mt-2 text-right">Existing Customer? <a href="index.php" class="text-dark" ><u>Login here</u></a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php include "include/script.php"; ?>
	<script src="dist/validate.js"></script>
	<script>
		$("#registerForm").validate({
			rules: {
				email: {
					required: true,
					email: true
				},
				reg_pass: {
					required: true,
					minlength: 3,
				},
				address: {
					required: true
				},
				pro_type: {
					required: true
				},
				no_of_bedrooms: {
					required: true,
					digits: true
				},
				evc: {
					required: true,
					minlength: 8,
					maxlength: 8
				}
			},
			messages: {
				email: {
					email: "Please enter a valid emailID"
				},
				reg_pass: {
					required: "Please provide a password",
					minlength: "Your password must be atleast 3 character long"
				},
				address: {
					required: "Please Enter Address"
				},
				pro_type: {
					required: "Please provide property type"
				},
				no_of_bedrooms: {
					required: "please enter no of bedroom",
					digits: "Only digits allowed"
				},
				evc: {
					required: "Please Enter EVC Voucher to register",
					minlength: "Please Enter valid Energy Voucher",
					maxlength: "Please Enter valid Energy Voucher"
				}
			}
		});
		$("#registerForm").on("submit", function(e) {
			e.preventDefault();
			e.stopImmediatePropagation();
			if ($("#registerForm").valid()) {
				// console.log($("#registerForm").serialize());
				$.ajax({
					url: "ajax/ajax_request.php",
					method: "POST",
					data: $("#registerForm").serialize() + "&action=submitRegisterForm",
					dataType: 'json',
					success: function(response) {
						if (response.status == 1) {
							window.location.href = "igse_dash.php";
						} else {
							myToast(response.status, response.message);
						}
					}
				})
			}
		})
	</script>
</body>

</html>