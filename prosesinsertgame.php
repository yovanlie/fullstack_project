<?php
require_once "class/game.php";

if (isset($_POST['submit'])) {
	$game = new Game();
	
	$name = $_POST['name'];
	$description = $_POST['description'];

	$affectedRows = $game->insertGame($name, $description);

	header("Location: game.php?hasil=" . $affectedRows);
	exit();
}
