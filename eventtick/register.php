<?php
require_once "includes/session.php";
if(isLoggedIn()){
	header("Location: dashboard.php");
	exit;
}

$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$password = $_POST['password'];
	$confirm = $_POST['confirm'];

	// simple validation lang muna
	if($name == "" || $email == "" || $password == ""){
		$error = "Please fill up all fields.";
	} else if(strlen($password) < 6){
		$error = "Password should be at least 6 characters.";
	} else if($password != $confirm){
		$error = "Password did not match.";
	} else {
		$check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
		if(mysqli_num_rows($check) > 0){
			$error = "Email already registered.";
		} else {
			$hashed = password_hash($password, PASSWORD_DEFAULT);
			$sql = "INSERT INTO users (name, email, password, role) VALUES ('$name','$email','$hashed','customer')";
			if(mysqli_query($conn, $sql)){
				header("Location: login.php");
				exit;
			} else {
				$error = "Something went wrong, try again.";
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Register - EventTick</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container" style="max-width:380px; margin-top:60px;">
	<div class="box">
		<h2 style="text-align:center">Create account</h2>

		<?php if($error != ""){ ?>
			<div class="error"><?php echo $error; ?></div>
		<?php } ?>

		<form method="POST" action="register.php">
			<label>Full name</label>
			<input type="text" name="name" required>

			<label>Email</label>
			<input type="email" name="email" required>

			<label>Password</label>
			<input type="password" name="password" required>

			<label>Confirm password</label>
			<input type="password" name="confirm" required>

			<button type="submit" class="btn" style="width:100%">Register</button>
		</form>
		<p style="text-align:center"><a href="login.php">Back to login</a></p>
	</div>
</div>
</body>
</html>
