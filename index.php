<?php 
if(isset($_SESSION['cid'])){
	header("location:igse_dash.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>iGSE Login</title>
	<?php include "include/csslink.php"; ?>
</head>
<body>
	<section class="login">
		<div id="toast"></div>
		<div class="pt-100"></div>
		<div class="container mb-30">
			<div class="row">
				<div class="col-12 offset-md-2 col-md-8 offset-lg-3 col-lg-6">
					<div class="card login-style p-4 text-end" style="background-color:	#7fffd4">
						<div>
							<h1 style="color: blue; text-align: center;">IGSE</h1>
						</div>
						<form id="loginForm" style="margin-top: 30px;">
							<div class="form-group text-left mt-3">
								<label for="userEmail" class="font-weight-bold">Email:</label>
								<input type="email" name="email" class="form-control" placeholder="Email Address" autocomplete="off" style=" text-align: center;" value=<?php if(isset($_COOKIE["cid"])){ echo $_COOKIE["cid"]; } ?>>
							</div>
							<div class="form-group text-left mt-3">
								<label for="password" class="font-weight-bold">Password:</label>
								<input type="password" name="pass" class="form-control" placeholder="Password" style=" text-align: center;" value=<?php if(isset($_COOKIE['ps'])){ echo $_COOKIE['ps']; } ?>>
							</div>
							<div style="display:flex; flex-direction: column;">
							<label class="custom-checkbox text-light float-left">
								<small class="ml-2" style="color:#000000">Remember Me</small>
								<input type="checkbox" name="rem" <?php if (isset($_COOKIE['cid'])) { ?> checked <?php } ?> class="custom-check-btn customCheck">
								<span class="checkmark"></span>
							</label>
							<!-- onclick="SubmitLoginForm()" -->
							<input type="submit" name="submit"  value="Login" style=" background-color:#006400	; text-align: center;" class="btn btn-dark btn-block">
							</div>
						</form>
						<div class="d-flex justify-content-end">
							<p class="mt-2 text-right">New Customer? <a href="igse_register.php" class="text-dark"><u>Register here</u></a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section> 
	<?php include "include/script.php"; ?>
	<script src="dist/validate.js"></script>
	<script>
		$("#loginForm").validate({
			rules: {
				email: {
					required: true,
					email: true
				},
				pass: {
					required: true,
					minlength: 3,
				}
			},
			messages: {
				email: {
					email: "Please enter a valid email"
				},
				pass: {
					required: "Please provide a password",
					minlength: "Your password must be atleast 3 character long"
				}
			}
		});
		$("#loginForm").on("submit", function(e) {
			e.preventDefault();
			e.stopImmediatePropagation();
			if ($("#loginForm").valid()) {
				$.ajax({
					url: "ajax/ajax_request.php",
					method: "POST",
					data: $("#loginForm").serialize() + "&action=loginForm",
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