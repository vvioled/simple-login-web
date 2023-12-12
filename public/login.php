<?php
session_start();

require "../config.php";

$users_unvailable = false;
$password_unvailable = false;


if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $query->execute(['username' => $username, 'email' => $username]);
    $fetch = $query->fetch();

    if ($fetch) {
        if (hash_equals(hash("sha256", $password), $fetch['password'])) {
            $_SESSION['user_id'] = $fetch['id'];
            header("Location: profile.php");
        } else {
            $password_unvailable = true;
        }
    } else {
        $users_unvailable = true;
    }
}


?>

<!DOCTYPE html>

<html>

<head>
    <title>Login</title>
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
                <h1 class="mt-3 fw-bold text-uppercase text-center">Login</h1>
                <div class="form-group pb-3">
                    <label for="username" class="fw-semibold mb-2">Username atau email:</label>
                    <input placeholder="Masukkan username atau email" type="text" name="username" class="form-control"
                        required />
                    <?php if ($users_unvailable) { ?>
                        <span class="text-danger mt-1">Username atau email tidak tersedia</span>
                    <?php } ?>
                </div>
                <div class="form-group pb-3">
                    <label for="password" class="fw-semibold mb-2">Password:</label>
                    <input placeholder="Masukkan Password" type="password" name="password" class="form-control">
                    <?php if ($password_unvailable) { ?>
                        <span class="text-danger mt-1">Password salah</span>
                    <?php } ?>
                </div>
                <button type="submit" name="login" class="btn btn-success mb-3">Login</button>
                <span class="text-success text-center mb-3">Tidak punya akun? silahkan <a
                        href="./register.php">daftar</a></span>
            </form>
        </div>
    </div>
</body>

</html>