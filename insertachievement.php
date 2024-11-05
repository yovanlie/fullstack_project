<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Insert Achievement</title>
</head>

<body>
    <form method="post" action="prosesinsertachievement.php" enctype="multipart/form-data">
        <p>
            <label>Team</label>
            <select id="team" name="team">
                <?php
                // Include the Achievement class and create an instance to access the database connection
                require_once "class/achievement.php";
                $achievement = new Achievement();
                $mysqli = $achievement->getConnection();

                // Fetch teams from the database
                $sql = "SELECT * FROM team";
                $stmt = $mysqli->prepare($sql);
                $stmt->execute();
                $res = $stmt->get_result();

                // Display teams in the select dropdown
                while ($team = $res->fetch_assoc()) {
                    echo "<option value='" . $team['idteam'] . "'>" . $team['name'] . "</option>";
                }
                ?>
            </select>
        </p>
        <p>
            <label>Name</label><input type="text" name="name" required>
        </p>
        <p>
            <label>Date</label><input type="date" name="date" required>
        </p>
        <p>
            <label>Description</label><textarea name="description" required></textarea>
        </p>
        <button type="submit" name="submit" value="simpan">Simpan</button>
    </form>
    
    <?php
    // Close the statement and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($mysqli)) {
        $mysqli->close();
    }
    ?>
</body>

</html>
