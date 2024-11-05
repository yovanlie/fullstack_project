<?php
require_once "class/achievement.php";
$achievement = new Achievement();

if (isset($_POST['submit'])) {
    $idachievement = $_POST['idachievement'];
    $name = $_POST['name'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $idteam = $_POST['team'];

    $result = $achievement->updateAchievement($idachievement, $name, $date, $description, $idteam);
    
    if ($result) {
        header("Location: achievement.php?edit=1");
    } else {
        header("Location: achievement.php?edit=0");
    }
    exit();
}
?>
