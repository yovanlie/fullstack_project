<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "class/team.php";

if (isset($_POST['submit'])) {
    $idteam = $_POST['idteam'];
    $name = $_POST['name'];
    $idgame = $_POST['idgame'];

    $team = new Team();
    $affectedRows = $team->updateTeam($idteam, $idgame, $name);

    header("Location: team.php?edit=" . $affectedRows);
    exit();
}

header("Location: team.php");
exit();
