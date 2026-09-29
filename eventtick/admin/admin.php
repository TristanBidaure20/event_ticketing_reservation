<?php
require_once "../includes/session.php";
checkAdmin();

// total counts lang for the dashboard
$totalEvents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM events"))['c'];
$totalConfirmed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM reservations WHERE status='Confirmed'"))['c'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM payments WHERE status='Paid'"))['t'];
if($totalRevenue == null) $totalRevenue = 0;

$perEvent = mysqli_query($conn, "SELECT e.event_name, e.event_date,
		SUM(CASE WHEN r.status='Confirmed' THEN d.quantity ELSE 0 END) as sold,
		SUM(CASE WHEN r.status='Confirmed' THEN d.subtotal ELSE 0 END) as revenue
		FROM events e
		LEFT JOIN ticket_types t ON t.event_id=e.event_id
		LEFT JOIN reservation_details d ON d.ticket_id=t.ticket_id
		LEFT JOIN reservations r ON r.reserve_id=d.reserve_id
		GROUP BY e.event_id ORDER BY e.event_date");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin - EventTick</title>
	<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="navbar">
	<a href="index.php"><strong>EventTick Admin</strong></a>
	<a href="events.php">Manage Events</a>
	<a href="../dashboard.php">View as Customer</a>
	<a href="../logout.php" style="float:right">Logout</a>
</div>

<div class="container">
	<h2>Sales Overview</h2>

	<div style="display:flex; gap:10px; margin-bottom:15px">
		<div class="box" style="flex:1"><small>Total Events</small><h2><?php echo $totalEvents; ?></h2></div>
		<div class="box" style="flex:1"><small>Confirmed Reservations</small><h2><?php echo $totalConfirmed; ?></h2></div>
		<div class="box" style="flex:1"><small>Revenue</small><h2>₱<?php echo number_format($totalRevenue,2); ?></h2></div>
	</div>

	<h3>Sales per Event</h3>
	<table>
		<tr><th>Event</th><th>Date</th><th>Tickets Sold</th><th>Revenue</th></tr>
		<?php while($row = mysqli_fetch_assoc($perEvent)){ ?>
		<tr>
			<td><?php echo htmlspecialchars($row['event_name']); ?></td>
			<td><?php echo $row['event_date']; ?></td>
			<td><?php echo $row['sold']; ?></td>
			<td>₱<?php echo number_format($row['revenue'],2); ?></td>
		</tr>
		<?php } ?>
	</table>
</div>
</body>
</html>
