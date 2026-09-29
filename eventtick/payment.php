<?php
require_once "includes/session.php";
checkLogin();

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM reservations WHERE reserve_id=$id AND user_id=$user_id";
$res = mysqli_query($conn, $sql);
$reservation = mysqli_fetch_assoc($res);

if(!$reservation){
	echo "Reservation not found.";
	exit;
}

if($reservation['status'] == 'Confirmed'){
	header("Location: ticket.php?id=$id");
	exit;
}

if($reservation['status'] != 'Pending'){
	echo "This reservation is " . $reservation['status'] . " na. Please make a new reservation.";
	exit;
}

$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
	$method = $_POST['method'];

	// dito dapat tawagin yung actual payment gateway, pero since prototype lang, simulate lang natin na success palagi
	$success = true;

	mysqli_query($conn, "INSERT INTO payments (reserve_id, amount, method, status) VALUES ($id, {$reservation['total_amount']}, '$method', '" . ($success ? 'Paid' : 'Failed') . "')");

	if($success){
		mysqli_query($conn, "UPDATE reservations SET status='Confirmed' WHERE reserve_id=$id");
		header("Location: ticket.php?id=$id");
		exit;
	} else {
		$error = "Payment failed, try again.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Payment - EventTick</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="container">
	<div class="box" style="max-width:450px; margin:0 auto">
		<h2>Payment</h2>

		<?php if($error != ""){ ?>
			<div class="error"><?php echo $error; ?></div>
		<?php } ?>

		<h3>Amount to Pay: ₱<?php echo number_format($reservation['total_amount'],2); ?></h3>

		<form method="POST" action="payment.php?id=<?php echo $id; ?>">
			<label>Payment Method</label>
			<select name="method">
				<option value="Card">Credit / Debit Card</option>
				<option value="GCash">GCash</option>
				<option value="PayPal">PayPal</option>
				<option value="Bank Transfer">Bank Transfer</option>
			</select>

			<!-- note: card fields walang name kasi prototype lang, di talaga sya sini-send kahit saan -->
			<label>Card Number</label>
			<input type="text" placeholder="1234 5678 9012 3456">

			<label>Expiry Date</label>
			<input type="text" placeholder="MM/YY" style="width:48%; display:inline-block">
			<label style="display:inline-block; width:48%">CVV</label>
			<input type="text" placeholder="***" style="width:48%; display:inline-block">

			<br><br>
			<button type="submit" class="btn btn-green">Pay Now ₱<?php echo number_format($reservation['total_amount'],2); ?></button>
		</form>
	</div>
</div>
</body>
</html>
