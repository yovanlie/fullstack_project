<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

require_once("class/event.php");
require_once("class/team.php");
require_once("class/game.php");
require_once("class/achievement.php");

$eventObj = new Event();
$teamObj = new Team();
$gameObj = new Game();
$achievementObj = new Achievement();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Esports Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <h1>Esports Management System Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($user_name); ?> (<?php echo htmlspecialchars($user_role); ?>)</p>
        <p>Username: <?php echo htmlspecialchars($username); ?></p>
        <p>User ID: <?php echo htmlspecialchars($user_id); ?></p>
    </header>

    <?php if ($user_role === 'admin'): ?>
        <p><a href="adminjoinproposal.php">Manage Join Proposals</a></p>
        <p><a href="team.php">Team Dashboard</a></p>
        <p><a href="game.php">Game Dashboard</a></p>
        <p><a href="achievement.php">Achievement Dashboard</a></p>
        <p><a href="event.php">Event Dashboard</a></p>
        <p><a href="manageteamachievementevent.php">Manage Team Achievements and Team Events</a></p>
    <?php endif; ?>
    <?php if ($user_role === 'member'): ?>
        <p><a href="teamjoinproposal.php">Join a Team</a></p>
        <p><a href="team.php">Team Dashboard</a></p>
        <p><a href="game.php">Game Dashboard</a></p>
        <p><a href="achievement.php">Achievement Dashboard</a></p>
        <p><a href="event.php">Event Dashboard</a></p>

    <?php endif; ?>
    <p><a href="logout.php">Logout</a></p>
</body>

</html>