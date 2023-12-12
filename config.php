<?php

$host = "localhost";
$db = "my_database";
$user = "root";
$pass = "";

$dsn = "mysql:host=$host;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>500 - Internal Server Error</h1>";
    exit(0);
}