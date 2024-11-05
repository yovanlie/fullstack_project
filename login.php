<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php
    if (isset($_GET['error'])) {
        echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</p>";
    }
    if (isset($_GET['success'])) {
        echo "<p style='color: green;'>Registration successful. Please login.</p>";
    }
    ?>
    <form method="post" action="processlogin.php">
        <p>
            <label>Username</label>
            <input type="text" name="username" required>
        </p>
        <p>
            <label>Password</label>
            <input type="password" name="password" required>
        </p>
        <button type="submit" name="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>