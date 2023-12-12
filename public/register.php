<?php
session_start();

require "../config.php";

$registery_success = false;
$username_exist = false;
$email_exist = false;


if (isset($_POST['register'])) {

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = hash('sha256', $_POST['password']);

    $query = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $query->execute(['username' => $username]);
    $fetch = $query->fetch();

    if ($fetch) {
        $username_exist = true;
    } else {
        $query = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $query->execute(['email' => $email]);
        $fetch = $query->fetch();
        if ($fetch) {
            $email_exist = true;
        } else {
            $query = $pdo->prepare("INSERT INTO users (fullname, username, email, password) VALUES (:fullname, :username, :email, :password)");
            $query->execute(['fullname' => $fullname, 'username' => $username, 'email' => $email, 'password' => $password]);
            $registery_success = true;
        }
    }
}


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
                <?php if ($registery_success) { ?>
                    <span class="text-success text-center mb-2">Pendaftaran berhasil</span>
                <?php } ?>
                <div class="form-group pb-3">
                    <label for="fullname" class="fw-semibold mb-2">Nama Lengkap:</label>
                    <input placeholder="Masukkan nama lengkap" type="text" name="fullname" class="form-control"
                        required />
                </div>
                <div class="form-group pb-3">
                    <label for="username" class="fw-semibold mb-2">Username:</label>
                    <input placeholder="Masukkan username" type="text" name="username" class="form-control" required />
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
                </div>
                <button type="submit" name="register" class="btn btn-success mb-3">Register</button>

            </form>
        </div>
    </div>
</body>

</html>