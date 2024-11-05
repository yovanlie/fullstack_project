<?php
session_start();
require_once "class/achievement.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$achievement = new Achievement();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5;

// Get all achievements
$achievements = $achievement->getAllAchievements();

// Calculate total pages
$totalAchievements = count($achievements);
$totalPages = ceil($totalAchievements / $itemsPerPage);

// Get achievements for the current page
$currentPageAchievements = array_slice($achievements, ($page - 1) * $itemsPerPage, $itemsPerPage);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Achievements</title>
</head>

<body>
    <h1>Achievement List</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <table border="1">
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Description</th>
            <th>Team</th>
            <?php if ($user_role === 'admin'): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($currentPageAchievements as $ach): ?>
            <tr>
                <td><?php echo htmlspecialchars($ach['name']); ?></td>
                <td><?php echo htmlspecialchars($ach['date']); ?></td>
                <td><?php echo htmlspecialchars($ach['description']); ?></td>
                <td><?php echo htmlspecialchars($ach['idteam']); ?></td>
                <?php if ($user_role === 'admin'): ?>
                    <td>
                        <a href="editachievement.php?id=<?php echo $ach['idachievement']; ?>">Edit</a> |
                        <a href="prosesdeleteachievement.php?id=<?php echo $ach['idachievement']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" <?php echo ($i == $page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>

    <?php if ($user_role === 'admin'): ?>
        <p><a href="insertachievement.php">Insert Achievement</a></p>
    <?php endif; ?>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>

</html>