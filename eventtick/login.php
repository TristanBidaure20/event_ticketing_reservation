<?php
require_once "includes/session.php";
if(isLoggedIn()){
	header("Location: dashboard.php");
	exit;
}

$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$password = $_POST['password'];

	$sql = "SELECT * FROM users WHERE email = '$email'";
	$result = mysqli_query($conn, $sql);

	if($result && mysqli_num_rows($result) == 1){
		$row = mysqli_fetch_assoc($result);
		if(password_verify($password, $row['password'])){
			// login ok
			$_SESSION['user_id'] = $row['user_id'];
			$_SESSION['name'] = $row['name'];
			$_SESSION['role'] = $row['role'];

			if($row['role'] == 'customer'){
				header("Location: dashboard.php");
			} else {
				header("Location: admin/index.php");
			}
			exit;
		} else {
			$error = "Wrong password.";
		}
	} else {
		$error = "No account found with that email.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login - EventTick</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container" style="max-width:380px; margin-top:60px;">
	<div class="box">
		<h2 style="text-align:center">EventTick</h2>
		<p style="text-align:center; color:gray">Event Ticketing Reservation System</p>

		<?php if($error != ""){ ?>
			<div class="error"><?php echo $error; ?></div>
		<?php } ?>

		<form method="POST" action="login.php">
			<label>Email</label>
			<input type="email" name="email" required>

			<label>Password</label>
			<input type="password" name="password" required>

			<button type="submit" class="btn" style="width:100%">Login</button>
		</form>
		<p style="text-align:center">No account yet? <a href="register.php">Register here</a></p>
	</div>
</div>
</body>
</html>
