<?php
require_once("class/user.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $user = new User();
    $result = $user->register($fname, $lname, $username, $password, $confirm_password);

    if ($result === "Registrasi berhasil. Silakan login.") {
        // Registration successful
        header("Location: login.php?success=1");
        exit();
    } else {
        // Registration failed
        header("Location: register.php?error=" . urlencode($result));
        exit();
    }
} else {
    // If not a POST request, redirect to the registration page
    header("Location: register.php");
    exit();
}
