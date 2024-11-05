<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
	header("Location: login.php");
	exit();
}

require_once "class/achievement.php";

if (isset($_GET['id'])) {
	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	
	if ($id === false) {
		header("Location: achievement.php?error=invalid_id");
		exit();
	}

	$achievement = new Achievement();
	$result = $achievement->deleteAchievement($id);

	if ($result) {
		header("Location: achievement.php?success=deleted");
	} else {
		header("Location: achievement.php?error=delete_failed");
	}
} else {
	header("Location: achievement.php?error=no_id");
}
exit();
