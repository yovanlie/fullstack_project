<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/style.css">
	<title>Insert Game</title>
</head>

<body>
	<?php
	session_start();
	if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
	    header("Location: login.php");
	    exit();
	}
	?>

	<form method="post" action="prosesinsertgame.php" enctype="multipart/form-data">
		<p>
			<label>Game Name</label><input type="text" name="name" required>
		</p>
		<p>
			<label>Description</label><textarea name="description" required></textarea>
		</p>
		<button type="submit" name="submit" value="simpan">Simpan</button>
	</form>
	<p><a href="game.php">
	<< Back to Events</a>
</body>

</html>