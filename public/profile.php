<?php
session_start();

require "../utilities.php";


if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$profile = Utilities::get_profile($_SESSION["user_id"]);

if (is_null($profile)) {
    header("Location: logout.php");
    exit();
}

?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
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
            <?php if (isset($_GET["edit"])) : ?>
                <?php "../lib/edit_profile.php" ?>
            <?php elseif (isset($_GET["change_password"])) : ?>
                <?php include "../lib/change_password.php" ?>
            <?php else : ?>
                <h1 class="title">Profile</h1>
                <div class="field">
                    <figure class="image is-128x128">
                        <img class="is-rounded" src="./storage/files/<?= $profile["profile_image"] ?>">
                    </figure>
                </div>
                <div class="field">
                    <span class="label">Full Name</span>
                    <div class="control has-icons-left">
                        <span class="input">
                            <?= $profile["fullname"] ?>
                        </span>
                        <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>
                <div class="field">
                    <span class="label">Username</span>
                    <div class="control has-icons-left">
                        <span class="input">
                            <?= $profile["username"] ?>
                        </span>
                        <span class="icon is-small is-left">
                            <i class="fas fa-user-circle"></i>
                        </span>
                    </div>
                </div>
                <div class="field mb-4">
                    <span class="label">Email</span>
                    <div class="control has-icons-left">
                        <span class="input">
                            <?= $profile["email"] ?>
                        </span>
                        <span class="icon is-small is-left">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                </div>
                <div class="field"><a href="profile.php?change_password=1" class="button is-text">Change password</a></div>
                <div class="field has-text-centered">
                    <div class="control">
                        <a href="profile.php?edit=1" class="button is-primary">Edit Profile</a>
                        <a href="logout.php" class="button is-primary">Logout</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>