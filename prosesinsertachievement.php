<?php
session_start();
require_once "class/achievement.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $achievement = new Achievement();
    
    $data = [
        'name' => $_POST['name'] ?? null,
        'date' => $_POST['date'] ?? null,
        'description' => $_POST['description'] ?? null,
        'idteam' => $_POST['team'] ?? null
    ];

    if ($data['name'] && $data['date'] && $data['description'] && $data['idteam']) {
        $result = $achievement->insertAchievement($data);
        if ($result) {
            header("Location: achievement.php?hasil=1");
        } else {
            header("Location: achievement.php?hasil=0");
        }
    } else {
        header("Location: achievement.php?hasil=0");
    }
    exit();
}
?>
