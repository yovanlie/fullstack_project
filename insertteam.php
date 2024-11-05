<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Insert Team</title>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: login.php");
        exit();
    }
    require_once "class/team.php";

    $team = new Team();
    $games = $team->getGames();
    ?>

    <h1>Insert New Team</h1>
    <form method="post" action="prosesinsertteam.php">
        <p>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </p>
        <p>
            <label for="game">Game:</label>
            <select id="game" name="game" required>
                <?php foreach ($games as $game): ?>
                    <option value="<?php echo $game['idgame']; ?>"><?php echo htmlspecialchars($game['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <button type="submit" name="submit" value="simpan">Save</button>
    </form>
    <p><a href="team.php">Back to Teams</a></p>
</body>

</html>