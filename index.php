<?php

session_start();

include "./config.php";

$query = $pdo->query("SELECT id FROM users");
$fetch = $query->fetchAll();

foreach ($fetch as $user) {
    if ($_SESSION["user_id"] === $user["id"]) {
        header("Location: profile.php");
    }
}