<?php

require "config.php";

class Utilities
{
    public static function login(string $user, string $pass, string &$err): ?array
    {
        global $pdo;

        $field = filter_var($user, FILTER_VALIDATE_EMAIL) ? "email" : "username";

        $q = "SELECT `id`, `password`, `username` FROM `users` WHERE {$field} = ? LIMIT 1";
        $st = $pdo->prepare($q);
        $st->execute([$user]);
        $ret = $st->fetch(PDO::FETCH_ASSOC);

        if (!$ret) {
            $err = "Username or email does not exist";
            return null;
        }

        if (!password_verify($pass, $ret["password"])) {
            $err = "Password is wrong";
            return null;
        }

        return $ret;
    }

    public static function validate_string_input(array $list): bool
    {
        foreach ($list as $key) {
            if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
                return false;
            }
        }
        return true;
    }

    public static function get_user_id_by_field(string $key, string $value): int
    {
        global $pdo;
        $st = $pdo->prepare("SELECT id FROM users WHERE $key = :$key LIMIT 1");
        $st->execute([$key => $value]);
        $ret = $st->fetch(PDO::FETCH_NUM);
        if (!$ret) {
            return 0;
        }
        return (int)$ret[0];
    }

    public static function register(string $fullname, string $username, string $email, string $password): void
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

    // public static function get(string $val, string $input): ?string
    // {
    //     global $pdo;
    //     $st = $pdo->prepare("SELECT $val FROM users WHERE username = :username OR email = :email LIMIT 1");
    //     $st->execute(["username" => $input, "email" => $input]);
    //     $fetch = $st->fetch();
    //     return $fetch ? $fetch[$val] : null;
    // }

    public static function update_profile(string $id, string $fullname, string $username, string $email, ?string $filename, ?string $ext): bool
    {
        global $pdo;
        $field = [
            "id" => $id,
            "fullname" => $fullname,
            "username" => $username,
            "email" => $email,
        ];

        if (!is_null($filename)) {
            $field["profile_image"] = $filename;
            $field["ext"] = $ext;
            $qr = ", profile_image = :profile_image, ext = :ext";
        } else {
            $qr = "";
        }

        $st = $pdo->prepare("UPDATE users SET fullname = :fullname, username = :username, email = :email {$qr} WHERE id = :id");
        return $st->execute($field);
    }


    public static function get_profile(string $id): ?array
    {
        global $pdo;
        $st = $pdo->prepare("SELECT fullname, username, email, profile_image, ext FROM users WHERE id = :id LIMIT 1");
        $st->execute(["id" => $id]);
        $ret = $st->fetch(PDO::FETCH_ASSOC);
        if (!$ret) {
            return NULL;
        }

        if (!$ret["profile_image"]) {
            $ret["profile_image"] = "default_profile.png";
        } else {
            $ret["profile_image"] = bin2hex($ret["profile_image"]) . "." . $ret["ext"];
        }
        $err = "Password dont match";


        return $ret;
    }

    public static function session_exist(): bool
    {
        global $pdo;
        if (isset($_SESSION["user_id"])) {
            $st = $pdo->prepare("SELECT id FROM users WHERE id = :id LIMIT 1");
            $st->execute(["id" => $_SESSION["user_id"]]);
            return (bool)$st->fetch();
        }
        return false;
    }

    public static function length_is_valid(string $input, int $req): bool
    {
        return strlen($input) >= $req;
    }
    public static function verify_password(string $id, string $old_password, string $new_password, string $new_password2, &$err): bool
    {
        global $pdo;
        $st = $pdo->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
        $st->execute(["id" => $id]);
        $ret = $st->fetch(PDO::FETCH_NUM);

        if (!$ret) {
            return false;
        }

        if (!password_verify($old_password, $ret[0])) {
            $err = "The old password is wrong";
            return false;
        }
        if ($new_password !== $new_password2) {
            $err = "New password does not match with confirm password";
            return false;
        }

        return true;
    }
    public static function update_password(string $id, string $new_password): bool
    {
        global $pdo;
        $st = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id LIMIT 1");
        return $st->execute([
            "password" => password_hash($new_password, PASSWORD_BCRYPT),
            "id" => $id
        ]);
    }
}
