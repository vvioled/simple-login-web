<?php

require "config.php";

class Utilities
{
    static function validate(array $list): bool
    {
        foreach ($list as $key) {
            if (!(isset($_POST[$key]) && is_string($_POST[$key])))
                return false;
        }
        return true;
    }
    static function check(string $key, string $value): bool
    {
        global $pdo;
        $st = $pdo->prepare("SELECT id FROM users WHERE $key = :$key LIMIT 1");
        $st->execute([
            $key => $value
        ]);
        if ($st->fetch())
            return true;
        return false;
    }
    static function store(string $fullname, string $username, string $email, string $password): void
    {
        global $pdo;
        $st = $pdo->prepare("INSERT INTO users (fullname, username, email, password) VALUES (:fullname, :username, :email, :password)");
        $st->execute([
            "fullname" => $fullname,
            "username" => $username,
            "email" => $email,
            "password" => $password
        ]);
    }
    static function get(string $val, string $input): ?string
    {
        global $pdo;
        $st = $pdo->prepare("SELECT $val FROM users WHERE username = :username OR email = :email LIMIT 1");
        $st->execute([
            "username" => $input,
            "email" => $input
        ]);
        $fetch = $st->fetch();
        if ($fetch)
            return $fetch[$val];
        return null;
    }
    static function get_profile(string $id): array
    {
        global $pdo;
        $st = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $st->execute([
            "id" => $id
        ]);
        $fetch = $st->fetchAll();
        if ($fetch)
            return $fetch;
        return [];
    }
    static function session_exist(): bool
    {
        global $pdo;
        if (isset($_SESSION["user_id"])) {
            $st = $pdo->prepare("SELECT id FROM users WHERE id = :id");
            $st->execute(["id" => $_SESSION["user_id"]]);
            if ($st->fetch()) {
                return true;
            }
        }
        return false;
    }
    static function length_is_valid(string $input, int $req): bool
    {
        if (strlen($input) < $req)
            return false;
        return true;
    }
}

