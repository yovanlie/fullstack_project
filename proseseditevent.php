<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "class/event.php";

if (isset($_POST['submit'])) {
    $idevent = $_POST['idevent'];
    $name = $_POST['name'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $teams = isset($_POST['teams']) ? $_POST['teams'] : [];

    $event = new Event();
    $result = $event->updateEvent($idevent, $name, $date, $description, $teams);

    header("Location: event.php?edit=" . ($result ? '1' : '0'));
    exit();
}

header("Location: event.php");
exit();
