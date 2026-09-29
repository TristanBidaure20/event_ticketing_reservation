<?php
require_once "includes/session.php";
checkLogin();

// release muna yung mga expired pending reservation (10 mins hold lang)
mysqli_query($conn, "UPDATE ticket_types t JOIN reservation_details d ON d.ticket_id=t.ticket_id JOIN reservations r ON r.reserve_id=d.reserve_id SET t.available = t.available + d.quantity, r.status='Expired' WHERE r.status='Pending' AND r.reserve_date < (NOW() - INTERVAL 10 MINUTE)");

$search = "";
if(isset($_GET['q'])){
	$search = mysqli_real_escape_string($conn, $_GET['q']);
}

$sql = "SELECT e.event_id, e.event_name, e.event_date, e.event_time, v.venue_name,
		MIN(t.price) as min_price, SUM(t.available) as tickets_left
		FROM events e
		JOIN venues v ON v.venue_id = e.venue_id
		LEFT JOIN ticket_types t ON t.event_id = e.event_id
		WHERE e.event_date >= CURDATE()";

if($search != ""){
	$sql .= " AND (e.event_name LIKE '%$search%' OR v.venue_name LIKE '%$search%')";
}

$sql .= " GROUP BY e.event_id ORDER BY e.event_date ASC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Events - EventTick</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="container">
	<h2>Upcoming Events</h2>

	<form method="GET" action="dashboard.php" style="margin-bottom:15px">
		<input type="text" name="q" placeholder="Search event or venue..." value="<?php echo htmlspecialchars($search); ?>" style="width:250px; display:inline-block">
		<button class="btn" type="submit">Search</button>
	</form>

	<?php if(mysqli_num_rows($result) == 0){ ?>
		<p>No events found.</p>
	<?php } else {
		while($row = mysqli_fetch_assoc($result)){
	?>
		<div class="box event-card">
			<div class="poster-placeholder">Event Poster</div>
			<strong><?php echo htmlspecialchars($row['event_name']); ?></strong><br>
			<small><?php echo date("M j, Y", strtotime($row['event_date'])); ?> - <?php echo date("g:i A", strtotime($row['event_time'])); ?></small><br>
			<small><?php echo htmlspecialchars($row['venue_name']); ?></small><br><br>

			<?php if($row['min_price'] == null){ ?>
				<span>No tickets yet</span>
			<?php } else { ?>
				<span>From ₱<?php echo number_format($row['min_price'],2); ?></span>
			<?php } ?>
			<br><br>

			<?php if($row['tickets_left'] <= 0){ ?>
				<span style="color:red">SOLD OUT</span>
			<?php } else { ?>
				<a href="event.php?id=<?php echo $row['event_id']; ?>" class="btn">View Details</a>
			<?php } ?>
		</div>
	<?php
		}
	} ?>
</div>
</body>
</html>
