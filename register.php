<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <?php
    if (isset($_GET['error'])) {
        echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</p>";
    }
    ?>
    <form method="post" action="processregister.php">
        <p>
            <label>First Name</label>
            <input type="text" name="fname" required>
        </p>
        <p>
            <label>Last Name</label>
            <input type="text" name="lname" required>
        </p>
        <p>
            <label>Username</label>
            <input type="text" name="username" required>
        </p>
        <p>
            <label>Password</label>
            <input type="password" name="password" required>
        </p>
        <p>
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>
        </p>
        <button type="submit" name="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
