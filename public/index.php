<?php
session_start();

require "../utilities.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (Utilities::session_exist()) {
    header("Location: profile.php");
    exit();
}