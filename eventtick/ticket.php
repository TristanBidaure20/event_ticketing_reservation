<?php
require_once "includes/session.php";
checkLogin();

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "SELECT r.reserve_id, r.reserve_date, r.user_id, e.event_name, e.event_date, e.event_time,
		v.venue_name, t.ticket_name, d.quantity
		FROM reservations r
		JOIN reservation_details d ON d.reserve_id=r.reserve_id
		JOIN ticket_types t ON t.ticket_id=d.ticket_id
		JOIN events e ON e.event_id=t.event_id
		JOIN venues v ON v.venue_id=e.venue_id
		WHERE r.reserve_id=$id AND r.status='Confirmed'";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
	echo "Ticket not found.";
	exit;
}

$rows = array();
while($r = mysqli_fetch_assoc($result)){
	$rows[] = $r;
}

// only owner or admin can view
if($rows[0]['user_id'] != $user_id && $_SESSION['role']=='customer'){
	echo "Not your ticket.";
	exit;
}

$ref = "EVT-" . date("Y", strtotime($rows[0]['reserve_date'])) . "-" . str_pad($rows[0]['reserve_id'], 5, "0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html>
<head>
	<title>E-Ticket - EventTick</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="container">
	<div class="box" style="max-width:400px; margin:0 auto; text-align:center">
		<h2 style="color:green">✔ Booking Confirmed!</h2>
		<p>Booking Reference: <strong><?php echo $ref; ?></strong></p>

		<!-- placeholder lang muna to, pwede palitan ng QR library later -->
		<div style="width:180px; height:180px; background:black; color:white; margin:15px auto; line-height:180px;">QR CODE</div>

		<?php foreach($rows as $r){ ?>
			<p>
				<strong><?php echo htmlspecialchars($r['event_name']); ?></strong><br>
				<?php echo date("M j, Y", strtotime($r['event_date'])); ?>, <?php echo date("g:i A", strtotime($r['event_time'])); ?><br>
				<?php echo htmlspecialchars($r['venue_name']); ?><br>
				<?php echo htmlspecialchars($r['ticket_name']); ?> x <?php echo $r['quantity']; ?>
			</p>
		<?php } ?>

		<button onclick="window.print()" class="btn">Download / Print E-Ticket</button>
		<a href="dashboard.php" class="btn" style="background:gray">Back to Dashboard</a>
	</div>
</div>
</body>
</html>
