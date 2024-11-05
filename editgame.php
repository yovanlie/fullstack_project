<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "class/game.php";
$game = new Game();

if (isset($_GET['id'])) {
    $idgame = $_GET['id'];
    $gameData = $game->getGameById($idgame);

    if (!$gameData) {
        echo "<p>Game not found.</p>";
        exit();
    }
} else {
    echo "<p>No game ID provided.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/style.css">
	<title>Edit Game</title>
</head>

<body>
	<h1>Edit Game</h1>

	<form method="post" action="proseseditgame.php">
		<input type="hidden" name="idgame" value="<?php echo htmlspecialchars($gameData['idgame']); ?>">
		<p>
			<label>Game Name</label>
			<input type="text" name="name" value="<?php echo htmlspecialchars($gameData['name']); ?>" required>
		</p>
		<p>
			<label>Description</label>
			<textarea name="description" required><?php echo htmlspecialchars($gameData['description']); ?></textarea>
		</p>
		<button type="submit" name="submit" value="update">Update</button>
	</form>
	<p><a href="game.php">Back to Games</a></p>
</body>

</html>