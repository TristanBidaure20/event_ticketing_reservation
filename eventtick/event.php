<?php
require_once "includes/session.php";
checkLogin();

$id = intval($_GET['id']);

$sql = "SELECT e.*, v.venue_name, v.location FROM events e JOIN venues v ON v.venue_id=e.venue_id WHERE e.event_id=$id";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
	echo "Event not found.";
	exit;
}
$event = mysqli_fetch_assoc($result);

$tickets = mysqli_query($conn, "SELECT * FROM ticket_types WHERE event_id=$id ORDER BY price DESC");
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo htmlspecialchars($event['event_name']); ?> - EventTick</title>
	<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="container">
	<div class="box">
		<h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
		<p>
			<?php echo date("l, F j, Y", strtotime($event['event_date'])); ?> at <?php echo date("g:i A", strtotime($event['event_time'])); ?><br>
			<?php echo htmlspecialchars($event['venue_name']); ?>, <?php echo htmlspecialchars($event['location']); ?>
		</p>
		<p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
	</div>

	<div class="box">
		<h3>Select Ticket</h3>
		<form method="POST" action="checkout.php">
			<?php
			$hasTicket = false;
			while($t = mysqli_fetch_assoc($tickets)){
				$hasTicket = true;
			?>
				<label style="display:block; padding:8px; border:1px solid #ddd; margin-bottom:6px;">
					<input type="radio" name="ticket_id" value="<?php echo $t['ticket_id']; ?>" <?php if($t['available']<=0) echo "disabled"; ?> required>
					<strong><?php echo htmlspecialchars($t['ticket_name']); ?></strong>
					- ₱<?php echo number_format($t['price'],2); ?>
					<?php if($t['available']<=0){ ?>
						<span style="color:red"> (Sold out)</span>
					<?php } else { ?>
						<span style="color:gray"> (<?php echo $t['available']; ?> left)</span>
					<?php } ?>
				</label>
			<?php } ?>

			<?php if(!$hasTicket){ ?>
				<p>No tickets available for this event yet.</p>
			<?php } else { ?>
				<label>Quantity</label>
				<input type="number" name="qty" value="1" min="1" max="10" style="width:100px">

				<br><br>
				<button type="submit" class="btn">Reserve Tickets</button>
			<?php } ?>
		</form>
	</div>
</div>
</body>
</html>
