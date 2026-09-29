<?php
// run this ONCE lang para may default accounts tayo, then delete na
require_once "includes/session.php";

$defaultAccounts = array(
	array("Admin", "admin@eventtick.test", "admin123", "admin"),
	array("Juan Dela Cruz", "juan@example.com", "juan1234", "customer")
);

foreach($defaultAccounts as $acc){
	$name = $acc[0];
	$email = $acc[1];
	$pass = password_hash($acc[2], PASSWORD_DEFAULT);
	$role = $acc[3];

	$check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
	if(mysqli_num_rows($check) == 0){
		mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name','$email','$pass','$role')");
	}
}

echo "Done. Accounts: admin@eventtick.test / admin123 and juan@example.com / juan1234 <br> Delete this file na after.";
