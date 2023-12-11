<?php

$host = "localhost";
$db = "my_database";
$user = "root";
$pass = "";

$dsn = "mysql:host=$host;dbname=$db";
$pdo = new PDO($dsn, $user, $pass);