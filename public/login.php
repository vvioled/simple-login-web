<?php
session_start();

require "../utilities.php";

if (Utilities::session_exist()) {
    header("Location: profile.php");
    exit();
}

function bad_requests(): void
{
    http_response_code(400);
    echo "<h1>Error 400 Bad Request!</h1>";
    exit();
}

$err = "";

if (isset($_POST['login'])) {

    if (!Utilities::validate_string_input(["username", "password"])) {
        bad_requests();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!Utilities::length_is_valid($password, 8)) {
        $err = "Password must be at least 8 characters long";
        goto OUT;
    }

    $ret = Utilities::login($username, $password, $err);

    if (is_null($ret)) {
        $err = "";
        goto OUT;
    }
    $_SESSION['user_id'] = (int)$ret["id"];
    header("Location: profile.php");
    exit();
}

OUT:

?>

<!DOCTYPE html>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css" integrity="sha512-HqxHUkJM0SYcbvxUw5P60SzdOTy/QVwA1JJrvaXJv4q7lmbDZCmZaqz01UPOaQveoxfYRv1tHozWGPMcuTBuvQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 100%;
            max-width: 500px;
            margin-inline: 15px;
        }
    </style>
</head>

<body class="has-background-light">
    <div class="card">
        <div class="card-content">
            <h1 class="title">Login</h1>
            <?php if (!empty($err)) : ?>
                <p class="has-text-danger has-text-centered"><?= $err ?></p>
            <?php endif; ?>
            <form class"has-fullwidth" action="" method="post">
                <div class="field">
                    <label class="label">Username or Email</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" name="username" placeholder="johndoe / johndoe@example.com" required />
                        <span class="icon is-small is-left">
                            <i class="fas fa-user-circle"></i>
                        </span>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Password</label>
                    <div class="control has-icons-left">
                        <input class="input" type="password" id="password" name="password" placeholder="********" required />
                        <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control">
                        <button name="login" class="button is-primary">Login</button>
                    </div>
                    <p class="control">
                        <a class="button is-light" href="register.php">Sign Up</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>