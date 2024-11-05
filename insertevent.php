<?php
require_once "class/event.php";
require_once "class/team.php";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$event = new Event();
$team = new Team();
$teams = $team->getAllTeams();

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $selectedTeam = isset($_POST['team']) ? $_POST['team'] : null;

    $result = $event->createEvent($name, $date, $description, $selectedTeam);

    if ($result) {
        header("Location: event.php?hasil=1");
    } else {
        header("Location: event.php?hasil=0");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Insert Event</title>
</head>

<body>
    <h1>Insert Event</h1>
    <form method="post" action="insertevent.php">
        <p>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
        </p>
        <p>
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>
        </p>
        <p>
            <label for="description">Description</label>
            <textarea id="description" name="description" required></textarea>
        </p>
        <p>
            <label for="team">Team</label>
            <select id="team" name="team" required>
                <option value="">Select a team...</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo $team['idteam']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <button type="submit" name="submit">Save</button>
    </form>
    <p><a href="event.php">Back to Events</a></p>
</body>

</html>