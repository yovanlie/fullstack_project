<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "class/team.php";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $game = $_POST['game'];

    $team = new Team();
    $affectedRows = $team->insertTeam($name, $game);

    header("Location: team.php?hasil=" . $affectedRows);
    exit();
}

header("Location: team.php");
exit();

?>
