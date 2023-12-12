<?php
session_start();

require "../config.php";
require "../functions.php";

if (isset($_SESSION["user_id"])) {
    $st = $pdo->prepare("SELECT id FROM users WHERE id = :id");
    $st->execute(["id" => $_SESSION["user_id"]]);
    if ($st->fetch()) {
        header("Location: profile.php");
    }
}
function bad_requests(): void
{
    http_response_code(400);
    echo "<h1>Error 400 Bad Request!</h1>";
    exit();
}


$register_success = false;
$invalid_fullname = false;
$invalid_username = false;
$username_length_invalid = false;
$password_length_invalid = false;
$username_exist = false;
$email_exist = false;


if (isset($_POST['register'])) {

    $bp = new BelajarPHP();

    if (!$bp->validate_input(["fullname", "username", "email", "password"])) {
        bad_requests();
    }

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $_password = $_POST['password'];
    $password = password_hash($_password, PASSWORD_BCRYPT);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        bad_requests();
    }
    if (!preg_match("/^[a-zA-Z ]+$/", $fullname)) {
        $invalid_fullname = true;
        goto out;
    }
    if (!preg_match("/^[a-zA-Z0-9\.\_]+$/", $username)) {
        $invalid_username = true;
        goto out;
    }
    if (!$bp->length_is_valid($username, 4)) {
        $username_length_invalid = true;
        goto out;
    }
    if (!$bp->length_is_valid($_password, 6)) {
        $password_length_invalid = true;
        goto out;
    }
    if ($bp->check_email_exist($email)) {
        $email_exist = true;
        goto out;
    }
    if ($bp->check_username_exist($username)) {
        $username_exist = true;
        goto out;
    }

    $bp->store_to_db($fullname, $username, $email, $password);
    $register_success = true;
}

out:
?>

<!DOCTYPE html>

<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <style>
        form {
            max-width: 400px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="vh-100 w-100 d-flex justify-content-center align-items-center">
            <form action="" method="post" class="card px-3 shadow w-100">
                <h1 class="mt-3 fw-bold text-uppercase text-center">Register</h1>
                <?php if ($register_success) { ?>
                    <span class="text-success text-center mb-2">Pendaftaran berhasil</span>
                <?php } ?>
                <div class="form-group pb-3">
                    <label for="fullname" class="fw-semibold mb-2">Nama Lengkap:</label>
                    <input placeholder="Masukkan nama lengkap" type="text" name="fullname" class="form-control"
                        required />
                    <?php if ($invalid_fullname) { ?>
                        <span class="text-danger mt-1">Nama tidak valid</span>
                    <?php } ?>
                </div>
                <div class="form-group pb-3">
                    <label for="username" class="fw-semibold mb-2">Username:</label>
                    <input placeholder="Masukkan username" type="text" name="username" class="form-control" required />
                    <?php if ($invalid_username) { ?>
                        <span class="text-danger mt-1">Username tidak valid</span>
                    <?php } ?>
                    <?php if ($username_length_invalid) { ?>
                        <span class="text-danger mt-1">panjang username tidak boleh kurang dari 3</span>
                    <?php } ?>
                    <?php if ($username_exist) { ?>
                        <span class="text-danger mt-1">Username sudah terdaftar</span>
                    <?php } ?>
                </div>
                <div class="form-group pb-3">
                    <label for="email" class="fw-semibold mb-2">Email:</label>
                    <input placeholder="Masukkan email" type="email" name="email" class="form-control" required />
                    <?php if ($email_exist) { ?>
                        <span class="text-danger mt-1">Email sudah terdaftar</span>
                    <?php } ?>
                </div>
                <div class="form-group pb-3">
                    <label for="password" class="fw-semibold mb-2">Password:</label>
                    <input placeholder="Masukkan Password" type="password" name="password" class="form-control"
                        required />
                    <?php if ($password_length_invalid) { ?>
                        <span class="text-danger mt-1">panjang password tidak boleh kurang dari 6</span>
                    <?php } ?>
                </div>
                <button type="submit" name="register" class="btn btn-success mb-3">Register</button>

                <span class="text-success text-center mb-3">Sudah punya akun? silahkan <a
                        href="./login.php">Login</a></span>

            </form>
        </div>
    </div>
</body>

</html>