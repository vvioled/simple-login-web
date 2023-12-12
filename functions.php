<?php

class BelajarPHP
{
    function validate_input(array $list): bool
    {
        foreach ($list as $key) {
            if (!(isset($_POST[$key]) && is_string($_POST[$key])))
                return false;
        }
        return true;
    }

    function check_username_exist(string $username): bool
    {
        global $pdo;
        $st = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
        $st->execute(["username" => $username]);
        if ($st->fetch())
            return true;
        return false;
    }
    function check_email_exist(string $email): bool
    {
        global $pdo;
        $st = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $st->execute(["email" => $email]);
        if ($st->fetch())
            return true;
        return false;
    }
    function length_is_valid(string $input, int $req): bool
    {
        if (strlen($input) < $req)
            return false;
        return true;
    }
    function store_to_db(string $fullname, string $username, string $email, string $password): void
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
    function get(string $val, string $input): string
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
}