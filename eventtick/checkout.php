<?php
require_once "includes/session.php";
checkLogin();

if($_SERVER['REQUEST_METHOD'] != "POST"){
	header("Location: dashboard.php");
	exit;
}

$ticket_id = intval($_POST['ticket_id']);
$qty = intval($_POST['qty']);

if($qty < 1) $qty = 1;
if($qty > 10) $qty = 10;

// kunin details ng ticket
$sql = "SELECT t.*, e.event_id, e.event_name, e.event_date, e.event_time FROM ticket_types t JOIN events e ON e.event_id=t.event_id WHERE t.ticket_id=$ticket_id";
$res = mysqli_query($conn, $sql);
$ticket = mysqli_fetch_assoc($res);

if(!$ticket){
	echo "Invalid ticket.";
	exit;
}

$step = isset($_POST['step']) ? $_POST['step'] : 'review';

if($step == 'confirm'){
	// dito na actual na i-reserve natin, check muna available bago mag insert
	mysqli_query($conn, "START TRANSACTION");

	$check = mysqli_query($conn, "SELECT available, price FROM ticket_types WHERE ticket_id=$ticket_id FOR UPDATE");
	$row = mysqli_fetch_assoc($check);

	if($row['available'] < $qty){
		mysqli_query($conn, "ROLLBACK");
		echo "Sorry, not enough tickets available na lang. Available: " . $row['available'];
		echo "<br><a href='event.php?id=".$ticket['event_id']."'>Go back</a>";
		exit;
	}

	$subtotal = $qty * $row['price'];
	$user_id = $_SESSION['user_id'];

	mysqli_query($conn, "UPDATE ticket_types SET available = available - $qty WHERE ticket_id=$ticket_id");
	mysqli_query($conn, "INSERT INTO reservations (user_id, total_amount, status) VALUES ($user_id, $subtotal, 'Pending')");
	$reserve_id = mysqli_insert_id($conn);
	mysqli_query($conn, "INSERT INTO reservation_details (reserve_id, ticket_id, quantity, subtotal) VALUES ($reserve_id, $ticket_id, $qty, $subtotal)");

	mysqli_query($conn, "COMMIT");

	header("Location: payment.php?id=" . $reserve_id);
	exit;
}

// step == review, ipakita muna summary bago i-confirm
$subtotal = $qty * $ticket['price'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Checkout - EventTick</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="container">
	<div class="box" style="max-width:500px; margin:0 auto">
		<h2>Reservation Summary</h2>
		<p><strong><?php echo htmlspecialchars($ticket['event_name']); ?></strong><br>
		<?php echo date("M j, Y", strtotime($ticket['event_date'])); ?>, <?php echo date("g:i A", strtotime($ticket['event_time'])); ?></p>

		<table>
			<tr><th>Ticket</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
			<tr>
				<td><?php echo htmlspecialchars($ticket['ticket_name']); ?></td>
				<td><?php echo $qty; ?></td>
				<td>₱<?php echo number_format($ticket['price'],2); ?></td>
				<td>₱<?php echo number_format($subtotal,2); ?></td>
			</tr>
		</table>

		<h3>Total: ₱<?php echo number_format($subtotal,2); ?></h3>
		<p style="color:gray">Note: tickets will be held for 10 minutes lang while you pay.</p>

		<form method="POST" action="checkout.php">
			<input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
			<input type="hidden" name="qty" value="<?php echo $qty; ?>">
			<input type="hidden" name="step" value="confirm">
			<a href="event.php?id=<?php echo $ticket['event_id']; ?>" class="btn" style="background:gray">Cancel</a>
			<button type="submit" class="btn btn-green">Continue to Payment</button>
		</form>
	</div>
</div>
</body>
</html>
