<?php
//membuat koneksi
require_once "class/game.php";

if (isset($_POST['submit'])) {
    $game = new Game();

    $idgame = $_POST['idgame'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Update the game using the class method
    $affectedRows = $game->updateGame($idgame, $name, $description);

    // Redirect with the result
    header("Location: game.php?edit=" . ($affectedRows > 0 ? "1" : "0"));
    exit(); // Ensure no further code is executed after the redirect
}
