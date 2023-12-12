<?php

session_start();

require "../config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
}

$st = $pdo->prepare("SELECT id FROM users WHERE id = :id");
$st->execute(["id" => $_SESSION["user_id"]]);
if ($st->fetch()) {
    header("Location: profile.php");
}