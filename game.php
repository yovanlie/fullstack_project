<?php
session_start();
require_once "class/game.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$game = new Game();

// Display messages based on query parameters
echo $game->displayMessages();

// Get the current page from the URL, default to 1 if not set
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5; // You can adjust this value as needed

// Get total number of games and calculate total pages
$totalGames = $game->getTotalGames();
$totalPages = ceil($totalGames / $itemsPerPage);

// Get games for the current page
$games = $game->getGames($page, $itemsPerPage);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/style.css">
	<title>Games</title>
</head>

<body>
	<h1>Game List</h1>
	<table border="1">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Description</th>
			<?php if ($user_role === 'admin'): ?>
				<th>Actions</th>
			<?php endif; ?>
		</tr>
		<?php foreach ($games as $game): ?>
			<tr>
				<td><?php echo $game['idgame']; ?></td>
				<td><?php echo htmlspecialchars($game['name']); ?></td>
				<td><?php echo htmlspecialchars($game['description']); ?></td>
				<?php if ($user_role === 'admin'): ?>
					<td>
						<a href="editgame.php?id=<?php echo $game['idgame']; ?>">Edit</a> |
						<a href="prosesdeletegame.php?idhapus=<?php echo $game['idgame']; ?>">Delete</a>
					</td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>
	</table>

	<!-- Pagination -->
	<div class="pagination">
		<?php for ($i = 1; $i <= $totalPages; $i++): ?>
			<a href="?page=<?php echo $i; ?>" <?php echo ($i == $page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
		<?php endfor; ?>
	</div>

	<?php if ($user_role === 'admin'): ?>
		<p><a href="insertgame.php">Insert Game</a></p>
	<?php endif; ?>
	<p><a href="dashboard.php">Back to Dashboard</a></p>
</body>

</html>
