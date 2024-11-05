<?php
session_start();
require_once("class/team.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$teamObj = new Team();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5;

$teams = $teamObj->getTeams($page, $itemsPerPage);
$totalTeams = $teamObj->getTotalTeams();
$totalPages = ceil($totalTeams / $itemsPerPage);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Team Management</title>
</head>

<body>
    <h1>Team List</h1>
    <?php echo $teamObj->displayMessages(); ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Game</th>
            <?php if ($user_role === 'admin'): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($teams as $team): ?>
            <tr>
                <td><?php echo $team['idteam']; ?></td>
                <td><?php echo htmlspecialchars($team['name']); ?></td>
                <td><?php echo htmlspecialchars($team['game_name']); ?></td>
                <?php if ($user_role === 'admin'): ?>
                    <td>
                        <a href="editteam.php?id=<?php echo $team['idteam']; ?>">Edit</a> |
                        <a href="prosesdeleteteam.php?id=<?php echo $team['idteam']; ?>" onclick="return confirm('Are you sure you want to delete this team?');">Delete</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>

    <?php if ($user_role === 'admin'): ?>
        <p><a href="insertteam.php">Insert New Team</a></p>
    <?php endif; ?>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>

</html>