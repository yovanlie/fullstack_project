<?php
// Start the session to store messages
session_start();

// Include the Event class
require_once "class/event.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Sanitize and validate input data
    $name = htmlspecialchars(trim($_POST['name']));
    $date = htmlspecialchars(trim($_POST['date']));
    $description = htmlspecialchars(trim($_POST['description']));
    $teamId = $_POST['team'];
    // Create an instance of the Event class
    $event = new Event();

    // Insert the event and get the number of affected rows
    $affectedRows = $event->createEvent($name, $date, $description, $teamId);

    // Store a message in the session based on the result
    if ($affectedRows > 0) {
        $_SESSION['message'] = "Event successfully created!";
    } else {
        $_SESSION['message'] = "Error: Unable to create event.";
    }

    // Redirect to the event page with a message
    header("Location: event.php");
    exit();
}
