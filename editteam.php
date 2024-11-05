<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "class/team.php";
$team = new Team();

if (isset($_GET['id'])) {
    $idteam = $_GET['id'];
    $teamData = $team->getTeamById($idteam);

    if (!$teamData) {
        echo "<p>Team not found.</p>";
        exit();
    }
} else {
    echo "<p>No team ID provided.</p>";
    exit();
}

$games = $team->getGames();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Edit Team</title>
</head>
<body>
    <h1>Edit Team</h1>
    <form method="post" action="proseseditteam.php">
        <input type="hidden" name="idteam" value="<?php echo htmlspecialchars($teamData['idteam']); ?>">
        <p>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($teamData['name']); ?>" required>
        </p>
        <p>
            <label for="idgame">Game:</label>
            <select id="idgame" name="idgame">
                <?php foreach ($games as $game): ?>
                    <option value="<?php echo $game['idgame']; ?>" <?php echo ($game['idgame'] == $teamData['idgame']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($game['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <button type="submit" name="submit" value="update">Update</button>
    </form>
    <p><a href="team.php">Back to Teams</a></p>
</body>
</html>