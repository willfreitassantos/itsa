<?php
require_once './vendor/autoload.php';

use ITSA\Model\User;

if(User::isLoggedIn()):
	// redirect user to new-oder page
	header('Location: ./orders/new');
	die();
else:
?>
	<!DOCTYPE html>
	<html lang="en-US">
		<head>
			<meta charset="UTF-8"/>
			<meta name="msapplication-tap-highlight" content="no"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
			<meta name="description" content="">
			<meta name="author" content="">
			<title>Login || itsa...</title>
			<!-- Favicon -->
			<link href="resources/images/favicon.png" rel="shortcut icon" type="image/png">
			<link href="resources/images/apple-icon.png" rel="icon" type="image/png">
			<!--=================== style sheet===========-->
			<link rel="stylesheet" href="resources/plugins/bootstrap/css/bootstrap.min.css">
			<link rel="stylesheet" href="resources/fonts/custom-fonts.css">
			<link rel="stylesheet" href="resources/plugins/ioicons/css/ionicons.min.css">
			<link rel="stylesheet" href="resources/css/form-login.css">
		</head>
		<body>
			<main>
				<img class="logo-itsa" src="resources/images/logo_itsa.png" alt="ITSA logo">
				<?php
				if(isset($_SESSION['login_failed'])) :
				?>
					<div id="alert" class="alert alert-danger alert-dismissible fade show text-center" role="alert">
		  				<strong>Oops!</strong> <?=$_SESSION['login_failed']?>
		  				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    				<span aria-hidden="true">&times;</span>
		  				</button>
					</div>
				<?php
					unset($_SESSION['login_failed']);
				endif;
				?>
				<span class="sign-in">SIGN IN</span>
				<form action="./user/sign-in" method="POST">
					<input type="text" name="login" class="custom-input" maxlength="50" placeholder="type@your.email" required autofocus>
					<input type="password" name="passwd" class="custom-input" maxlength="8" placeholder="********" required>
					<label class="checkbox-container">Keep me signed in
						<input type="checkbox" name="keep_signed_in">
						<span class="checkmark"></span>
					</label>
					<input type="submit" class="btn-sign-in" value="SIGN IN">
				</form>
			</main>
			<script type="text/javascript" src="resources/plugins/jquery/js/jquery-3.3.1.min.js"></script>
			<script type="text/javascript" src="resources/plugins/bootstrap/js/bootstrap.min.js"></script>
		</body>
	</html>
<?php
endif;
?>
