<?php
require_once "../includes/session.php";
checkAdmin();

$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
	$name = mysqli_real_escape_string($conn, $_POST['event_name']);
	$venue_id = intval($_POST['venue_id']);
	$date = $_POST['event_date'];
	$time = $_POST['event_time'];
	$desc = mysqli_real_escape_string($conn, $_POST['description']);

	$ticketNames = $_POST['ticket_name'];
	$ticketPrices = $_POST['price'];
	$ticketQty = $_POST['quantity'];

	if($name == "" || $venue_id == 0 || $date == "" || $time == ""){
		$error = "Please fill up the required fields.";
	} else {
		// insert event muna
		mysqli_query($conn, "INSERT INTO events (venue_id, event_name, event_date, event_time, description) VALUES ($venue_id, '$name', '$date', '$time', '$desc')");
		$event_id = mysqli_insert_id($conn);

		// tapos yung ticket types niya
		for($i=0; $i<count($ticketNames); $i++){
			$tn = mysqli_real_escape_string($conn, trim($ticketNames[$i]));
			$price = floatval($ticketPrices[$i]);
			$qty = intval($ticketQty[$i]);

			if($tn != "" && $price > 0 && $qty > 0){
				mysqli_query($conn, "INSERT INTO ticket_types (event_id, ticket_name, price, quantity, available) VALUES ($event_id, '$tn', $price, $qty, $qty)");
			}
		}

		header("Location: events.php");
		exit;
	}
}

$venues = mysqli_query($conn, "SELECT * FROM venues");
$events = mysqli_query($conn, "SELECT e.*, v.venue_name FROM events e JOIN venues v ON v.venue_id=e.venue_id ORDER BY e.event_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Events - EventTick</title>
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
	<h2>Add New Event</h2>

	<?php if($error != ""){ ?>
		<div class="error"><?php echo $error; ?></div>
	<?php } ?>

	<form method="POST" action="events.php" class="box">
		<label>Event Name</label>
		<input type="text" name="event_name" required>

		<label>Venue</label>
		<select name="venue_id" required>
			<option value="">-- choose venue --</option>
			<?php
			mysqli_data_seek($venues, 0);
			while($v = mysqli_fetch_assoc($venues)){
			?>
			<option value="<?php echo $v['venue_id']; ?>"><?php echo htmlspecialchars($v['venue_name']); ?></option>
			<?php } ?>
		</select>

		<label>Date</label>
		<input type="date" name="event_date" required>

		<label>Time</label>
		<input type="time" name="event_time" required>

		<label>Description</label>
		<textarea name="description" rows="3"></textarea>

		<h4>Ticket Types (fill up to 3, pwede iwan blank yung di gagamitin)</h4>
		<?php for($i=0; $i<3; $i++){ ?>
		<div style="display:flex; gap:10px">
			<input type="text" name="ticket_name[]" placeholder="Ticket name (e.g VIP)">
			<input type="number" name="price[]" placeholder="Price" step="0.01">
			<input type="number" name="quantity[]" placeholder="Qty">
		</div>
		<?php } ?>

		<br>
		<button type="submit" class="btn">Create Event</button>
	</form>

	<h2>All Events</h2>
	<table>
		<tr><th>Event</th><th>Date</th><th>Venue</th></tr>
		<?php while($e = mysqli_fetch_assoc($events)){ ?>
		<tr>
			<td><?php echo htmlspecialchars($e['event_name']); ?></td>
			<td><?php echo $e['event_date']; ?></td>
			<td><?php echo htmlspecialchars($e['venue_name']); ?></td>
		</tr>
		<?php } ?>
	</table>
</div>
</body>
</html>
