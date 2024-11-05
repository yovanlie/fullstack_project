<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once "class/event.php";

if (isset($_GET['idhapus'])) {
    $idhapus = $_GET['idhapus'];

    // Create an instance of the Event class
    $event = new Event();
    $affectedRows = $event->deleteEvent($idhapus);

    header("Location: event.php?hapus=" . $affectedRows);
    exit();
}
?>
