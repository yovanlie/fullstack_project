<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "class/team.php";

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if ($id === false) {
        header("Location: team.php?hapus=0");
        exit();
    }

    $team = new Team();
    $affectedRows = $team->deleteTeam($id);

    header("Location: team.php?hapus=" . $affectedRows);
    exit();
}

header("Location: team.php");
exit();
