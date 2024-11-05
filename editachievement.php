<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "class/achievement.php";
require_once "class/team.php";
$achievement = new Achievement();
$team = new Team();

if (isset($_GET['id'])) {
    $idachievement = $_GET['id'];
    $achievementData = $achievement->getAchievementById($idachievement);

    if (!$achievementData) {
        echo "<p>Achievement not found.</p>";
        exit();
    }
} else {
    echo "<p>No achievement ID provided.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Edit Achievement</title>
</head>

<body>
    <h1>Edit Achievement</h1>
    <form method="post" action="proseseditachievement.php">
        <input type="hidden" name="idachievement" value="<?php echo htmlspecialchars($achievementData['idachievement']); ?>">
        <p>
            <label>Team</label>
            <select id="team" name="team">
                <?php
                $teams = $team->getAllTeams();
                foreach ($teams as $team) {
                    $selected = $team['idteam'] == $achievementData['idteam'] ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($team['idteam']) . "' $selected>" . htmlspecialchars($team['name']) . "</option>";
                }
                ?>
            </select>
        </p>
        <p>
            <label>Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($achievementData['name']); ?>" required>
        </p>
        <p>
            <label>Date</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($achievementData['date']); ?>" required>
        </p>
        <p>
            <label>Description</label>
            <textarea name="description" required><?php echo htmlspecialchars($achievementData['description']); ?></textarea>
        </p>
        <button type="submit" name="submit" value="update">Update</button>
    </form>
    <p><a href="achievement.php">Back to Achievements</a></p>
</body>

</html>