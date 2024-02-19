<?php

$host = "localhost";
$dbname = "learn";
$user = "kyzsuki";
$pass = "qwerty123";

$dsn = "mysql:host=$host;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>500 - Internal Server Error</h1>";
    exit();
}