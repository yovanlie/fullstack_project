<?php
session_start();
require_once "class/event.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];
$event = new Event();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Events</title>
</head>
<body>
    <h1>Event List</h1>
    <?php 
    echo $event->displayMessages();
    echo $event->displayEvents($page);
    
    if ($user_role === 'admin'): 
    ?>
        <p><a href="insertevent.php">Insert Event</a></p>
    <?php endif; ?>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>