<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
	header("Location: login.php");
	exit();
}
require_once "class/game.php";

if (isset($_GET['idhapus'])) {
	$game = new Game();
	$idhapus = $_GET['idhapus'];

	$affectedRows = $game->deleteGame($idhapus);

	header("Location: game.php?hapus=" . $affectedRows);
	exit();
}
