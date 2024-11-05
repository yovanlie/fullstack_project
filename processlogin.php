<?php
require_once("class/user.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $user = new User();
    $result = $user->login($username, $password);

    if ($result === true) {
        // Login successful
        header("Location: dashboard.php");
        exit();
    } else {
        // Login failed
        header("Location: login.php?error=" . urlencode($result));
        exit();
    }
} else {
    // If not a POST request, redirect to the login page
    header("Location: login.php");
    exit();
}
