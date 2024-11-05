<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/style.css">
	<title>Edit Event</title>
</head>

<body>
	<?php
	session_start();
	if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
		header("Location: login.php");
		exit();
	}

	require_once "class/event.php";
	require_once "class/team.php";

	$event = new Event();
	$team = new Team();
	$teams = $team->getAllTeams();

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$idevent = intval($_GET['id']);
		$eventDetails = $event->getEventById($idevent);
		$eventTeam = $event->getTeamInEvent($idevent);

		if (!$eventDetails) {
			echo "<p>Event not found.</p>";
			exit();
		}
	} else {
		echo "<p>Invalid or missing event ID. <a href='event.php'>Return to events list</a></p>";
		exit();
	}
	?>

	<h1>Edit Event</h1>
	<form method="post" action="proseseditevent.php">
		<input type="hidden" name="idevent" value="<?php echo htmlspecialchars($idevent); ?>" required>
		<p>
			<label>Event Name</label>
			<input type="text" name="name" value="<?php echo htmlspecialchars($eventDetails['name']); ?>" required>
		</p>
		<p>
			<label>Date</label>
			<input type="date" name="date" value="<?php echo htmlspecialchars($eventDetails['date']); ?>" required>
		</p>
		<p>
			<label>Description</label>
			<textarea name="description" required><?php echo htmlspecialchars($eventDetails['description']); ?></textarea>
		</p>
		<p>
			<label for="team">Team</label>
			<select id="team" name="team" required>
				<option value="">Select a team...</option>
				<?php foreach ($teams as $t): ?>
					<option value="<?php echo $t['idteam']; ?>" <?php echo ($eventTeam && $eventTeam['idteam'] == $t['idteam']) ? 'selected' : ''; ?>>
						<?php echo htmlspecialchars($t['name']); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<button type="submit" name="submit">Save</button>
	</form>
	<p><a href="event.php">Back to Events</a></p>
</body>

</html>