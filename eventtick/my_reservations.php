<?php
require_once "includes/session.php";
checkLogin();

$user_id = $_SESSION['user_id'];

// cancel action
if(isset($_POST['cancel_id'])){
	$rid = intval($_POST['cancel_id']);

	$check = mysqli_query($conn, "SELECT * FROM reservations WHERE reserve_id=$rid AND user_id=$user_id AND status='Pending'");
	if(mysqli_num_rows($check) > 0){
		mysqli_query($conn, "UPDATE ticket_types t JOIN reservation_details d ON d.ticket_id=t.ticket_id SET t.available = t.available + d.quantity WHERE d.reserve_id=$rid");
		mysqli_query($conn, "UPDATE reservations SET status='Cancelled' WHERE reserve_id=$rid");
	}
	header("Location: my_reservations.php");
	exit;
}

$sql = "SELECT r.reserve_id, r.reserve_date, r.total_amount, r.status, e.event_name, e.event_date, t.ticket_name, d.quantity
		FROM reservations r
		JOIN reservation_details d ON d.reserve_id=r.reserve_id
		JOIN ticket_types t ON t.ticket_id=d.ticket_id
		JOIN events e ON e.event_id=t.event_id
		WHERE r.user_id=$user_id ORDER BY r.reserve_id DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
	<title>My Reservations - EventTick</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="container">
	<h2>My Reservations</h2>

	<?php if(mysqli_num_rows($result) == 0){ ?>
		<p>Wala ka pang reservation. <a href="dashboard.php">Browse events</a></p>
	<?php } else { ?>
	<table>
		<tr><th>Reference</th><th>Event</th><th>Ticket</th><th>Total</th><th>Status</th><th></th></tr>
		<?php while($r = mysqli_fetch_assoc($result)){
			$ref = "EVT-" . date("Y", strtotime($r['reserve_date'])) . "-" . str_pad($r['reserve_id'], 5, "0", STR_PAD_LEFT);
		?>
		<tr>
			<td><?php echo $ref; ?></td>
			<td><?php echo htmlspecialchars($r['event_name']); ?><br><small><?php echo date("M j, Y", strtotime($r['event_date'])); ?></small></td>
			<td><?php echo htmlspecialchars($r['ticket_name']); ?> x <?php echo $r['quantity']; ?></td>
			<td>₱<?php echo number_format($r['total_amount'],2); ?></td>
			<td><?php echo $r['status']; ?></td>
			<td>
				<?php if($r['status']=='Confirmed'){ ?>
					<a href="ticket.php?id=<?php echo $r['reserve_id']; ?>">View</a>
				<?php } elseif($r['status']=='Pending'){ ?>
					<a href="payment.php?id=<?php echo $r['reserve_id']; ?>">Pay</a>
					<form method="POST" style="display:inline" onsubmit="return confirm('Cancel this reservation?')">
						<input type="hidden" name="cancel_id" value="<?php echo $r['reserve_id']; ?>">
						<button type="submit" style="border:none; background:none; color:red; cursor:pointer">Cancel</button>
					</form>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</table>
	<?php } ?>
</div>
</body>
</html>
