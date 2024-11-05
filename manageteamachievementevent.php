<?php
session_start();
require_once "class/achievement.php";
require_once "class/team.php";
require_once "class/event.php";

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize objects
$achievementObj = new Achievement();
$teamObj = new Team();
$eventObj = new Event();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamId = filter_var($_POST['team_id'] ?? null, FILTER_VALIDATE_INT);

    // Achievement management
    if (isset($_POST['add_achievement']) && isset($_POST['achievement_id'])) {
        $achievementId = filter_var($_POST['achievement_id'], FILTER_VALIDATE_INT);
        if ($teamId && $achievementId) {
            $success = $achievementObj->addAchievementToTeam($teamId, $achievementId);
            $_SESSION['message'] = $success ?
                "Achievement successfully added to team." :
                "Failed to add achievement to team.";
        }
    } elseif (isset($_POST['change_achievement_team']) && isset($_POST['achievement_id']) && isset($_POST['new_team_id'])) {
        $achievementId = filter_var($_POST['achievement_id'], FILTER_VALIDATE_INT);
        $newTeamId = filter_var($_POST['new_team_id'], FILTER_VALIDATE_INT);
        if ($teamId && $achievementId && $newTeamId) {
            $success = $achievementObj->changeTeamAchievement($teamId, $newTeamId, $achievementId);
            if ($success) {
                $_SESSION['message'] = "Achievement successfully changed to new team.";
            } else {
                $_SESSION['error'] = "Failed to change achievement's team. It may not be associated with the original team.";
                error_log("Failed to change achievement $achievementId from team $teamId to team $newTeamId");
            }
        } else {
            $_SESSION['error'] = "Invalid team or achievement ID.";
        }
    }
    // Event management
    elseif (isset($_POST['add_event']) && isset($_POST['event_id'])) {
        $eventId = filter_var($_POST['event_id'], FILTER_VALIDATE_INT);
        if ($teamId && $eventId) {
            $success = $eventObj->addTeamEvent($teamId, $eventId);
            $_SESSION['message'] = $success ?
                "Event successfully added to team." :
                "Failed to add event to team.";
        }
    } elseif (isset($_POST['remove_event']) && isset($_POST['event_id'])) {
        $eventId = filter_var($_POST['event_id'], FILTER_VALIDATE_INT);
        if ($teamId && $eventId) {
            $success = $eventObj->removeTeamEvent($teamId, $eventId);
            $_SESSION['message'] = $success ?
                "Event successfully removed from team." :
                "Failed to remove event from team.";
        }
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] .
        (isset($_GET['team_id']) ? "?team_id=" . $_GET['team_id'] : ""));
    exit();
}

// Get selected team if any
$selectedTeamId = filter_var($_GET['team_id'] ?? null, FILTER_VALIDATE_INT);
$selectedTeam = $selectedTeamId ? $teamObj->getTeamById($selectedTeamId) : null;

// Pagination
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, [
    "options" => ["default" => 1, "min_range" => 1]
]);
$itemsPerPage = 5;

// Fetch data
try {
    $teams = $teamObj->getAllTeams();
    $achievements = $achievementObj->getAllAchievements();
    $events = $eventObj->getAllEvents();

    if ($selectedTeam) {
        // Get team-specific achievements and events with pagination
        $teamAchievements = $achievementObj->getTeamAchievements($selectedTeamId, $page, $itemsPerPage);
        $totalTeamAchievements = $achievementObj->getTotalTeamAchievements($selectedTeamId);

        $teamEvents = $eventObj->getTeamEvents($selectedTeamId, $page, $itemsPerPage);
        $totalTeamEvents = $eventObj->getTotalTeamEvents($selectedTeamId);

        $totalPagesAchievements = ceil($totalTeamAchievements / $itemsPerPage);
        $totalPagesEvents = ceil($totalTeamEvents / $itemsPerPage);
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Team Achievements and Events</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .success {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }

        .team-selector {
            margin: 20px 0;
        }

        .section {
            margin: 30px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f5f5f5;
        }

        .pagination {
            margin: 20px 0;
        }

        .pagination a {
            padding: 5px 10px;
            margin: 0 2px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        form {
            margin: 20px 0;
        }

        select,
        button {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .button-remove {
            background-color: #dc3545;
        }

        .button-remove:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Manage Team Achievements and Events</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <?php
                echo htmlspecialchars($_SESSION['message']);
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?php
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="team-selector">
            <h2>Select Team</h2>
            <form method="get">
                <select name="team_id" onchange="this.form.submit()">
                    <option value="">Select a team...</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?php echo htmlspecialchars($team['idteam']); ?>"
                            <?php echo $selectedTeamId == $team['idteam'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($team['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($selectedTeam): ?>
            <div class="section">
                <h2>Achievements for <?php echo htmlspecialchars($selectedTeam['name']); ?></h2>

                <?php if (!empty($teamAchievements)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Achievement Name</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teamAchievements as $ta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ta['name']); ?></td>
                                    <td><?php echo htmlspecialchars($ta['date']); ?></td>
                                    <td><?php echo htmlspecialchars($ta['description']); ?></td>
                                    <td>
                                        <form method="post" style="margin: 0; display: inline;">
                                            <input type="hidden" name="team_id"
                                                value="<?php echo $selectedTeamId; ?>">
                                            <input type="hidden" name="achievement_id"
                                                value="<?php echo $ta['idachievement']; ?>">
                                            <select name="new_team_id" required>
                                                <option value="">Change team...</option>
                                                <?php foreach ($teams as $team): ?>
                                                    <?php if ($team['idteam'] != $selectedTeamId): ?>
                                                        <option value="<?php echo htmlspecialchars($team['idteam']); ?>">
                                                            <?php echo htmlspecialchars($team['name']); ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" name="change_achievement_team">Change</button>
                                        </form>
                                        <form method="post" style="margin: 0; display: inline;">
                                            <input type="hidden" name="team_id"
                                                value="<?php echo $selectedTeamId; ?>">
                                            <input type="hidden" name="achievement_id"
                                                value="<?php echo $ta['idachievement']; ?>">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPagesAchievements; $i++): ?>
                            <a href="?team_id=<?php echo $selectedTeamId; ?>&page=<?php echo $i; ?>"
                                <?php echo ($i === $page) ? 'class="active"' : ''; ?>>
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <p>No achievements found for this team.</p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>Events for <?php echo htmlspecialchars($selectedTeam['name']); ?></h2>

                <?php if (!empty($teamEvents)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teamEvents as $te): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($te['name']); ?></td>
                                    <td><?php echo htmlspecialchars($te['date']); ?></td>
                                    <td><?php echo htmlspecialchars($te['description']); ?></td>
                                    <td>
                                        <form method="post" style="margin: 0;">
                                            <input type="hidden" name="team_id"
                                                value="<?php echo $selectedTeamId; ?>">
                                            <input type="hidden" name="event_id"
                                                value="<?php echo $te['idevent']; ?>">
                                            <button type="submit" name="remove_event"
                                                class="button-remove"
                                                onclick="return confirm('Are you sure?');">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPagesEvents; $i++): ?>
                            <a href="?team_id=<?php echo $selectedTeamId; ?>&page=<?php echo $i; ?>"
                                <?php echo ($i === $page) ? 'class="active"' : ''; ?>>
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <p>No events found for this team.</p>
                <?php endif; ?>

                <form method="post">
                    <h3>Add Event</h3>
                    <input type="hidden" name="team_id" value="<?php echo $selectedTeamId; ?>">
                    <select name="event_id" required>
                        <option value="">Select an event...</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?php echo htmlspecialchars($event['idevent']); ?>">
                                <?php echo htmlspecialchars($event['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="add_event">Add Event</button>
                </form>
            </div>
        <?php else: ?>
            <p>Please select a team to manage their achievements and events.</p>
        <?php endif; ?>

        <p><a href="dashboard.php">Back to Admin Dashboard</a></p>
    </div>
</body>

</html>