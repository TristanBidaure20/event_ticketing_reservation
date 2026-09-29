<!-- top navbar, ginawa kong include na lang para di paulit ulit -->
<div class="navbar">
	<a href="dashboard.php"><strong>EventTick</strong></a>
	<a href="dashboard.php">Events</a>
	<a href="my_reservations.php">My Reservations</a>
	<?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'organizer'){ ?>
		<a href="admin/admin.php">Admin</a>
	<?php } ?>
	<a href="logout.php" style="float:right">Logout (<?php echo $_SESSION['name']; ?>)</a>
</div>
