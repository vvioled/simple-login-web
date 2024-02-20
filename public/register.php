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
$register_success = false;

if (isset($_POST['register'])) {

  if (!Utilities::validate_string_input(["fullname", "username", "email", "password", "password2"])) {
    bad_requests();
  }

  $fullname = $_POST['fullname'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $_password = $_POST['password'];
  $_password2 = $_POST["password2"];
  $password = password_hash($_password, PASSWORD_BCRYPT);

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err = "The email format is invalid";
  }
  if (!preg_match("/^[a-zA-Z ]+$/", $fullname)) {
    bad_requests();
  }
  if (!preg_match("/^[a-zA-Z0-9\.\_]+$/", $username)) {
    bad_requests();
  }
  if (!Utilities::length_is_valid($username, 4)) {
    $err = "Username must be at least 4 characters long";
  }
  if (!Utilities::length_is_valid($_password, 8)) {
    $err = "Password must be at least 8 characters long";
    goto OUT;
  }
  if (!Utilities::length_is_valid($_password2, 8)) {
    $err = "Confirm password must be at least 8 characters long";
    goto OUT;
  }
  if ($_password2 !== $password) {
    $err = "New password does not match with confirm password";
    goto OUT;
  }
  if (Utilities::get_user_id_by_field("email", $email)) {
    $err = "Email already exists";
    goto OUT;
  }
  if (Utilities::get_user_id_by_field("username", $username)) {
    $err = "Username already exits";
    goto OUT;
  }

  Utilities::register($fullname, $username, $email, $password);

  $register_success = true;
}

OUT:

?>

<!DOCTYPE html>

<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
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
      <h1 class="title">Register</h1>
      <?php if (!empty($err)) { ?>
        <p class="has-text-danger has-text-centered"><?= $err ?></p>
      <?php } ?>
      <?php if ($register_success) { ?>
        <p class="has-text-success has-text-centered">Registration Success</p>
      <?php } ?>
      <form class"has-fullwidth" action="" method="post">
        <div class="field">
          <label class="label">Full Name</label>
          <div class="control has-icons-left">
            <input class="input" type="text" name="fullname" placeholder="John Doe" required />
            <span class="icon is-small is-left">
              <i class="fas fa-user"></i>
            </span>
          </div>
        </div>
        <div class="field">
          <label class="label">Username</label>
          <div class="control has-icons-left">
            <input class="input" type="text" name="username" placeholder="johndoe" required />
            <span class="icon is-small is-left">
              <i class="fas fa-user-circle"></i>
            </span>
          </div>
        </div>
        <div class="field">
          <label class="label">Email</label>
          <div class="control has-icons-left">
            <input class="input" type="text" name="email" placeholder="johndoe@example.com" required />
            <span class="icon is-small is-left">
              <i class="fas fa-envelope"></i>
            </span>
          </div>
        </div>
        <div class="field">
          <label class="label">Password</label>
          <div class="control has-icons-left">
            <input class="input" type="password" name="password" placeholder="********" required />
            <span class="icon is-small is-left">
              <i class="fas fa-lock"></i>
            </span>
          </div>
        </div>
        <div class="field">
          <label class="label">Confirm Password</label>
          <div class="control has-icons-left">
            <input class="input" type="password" name="password2" placeholder="********" required />
            <span class="icon is-small is-left">
              <i class="fas fa-lock"></i>
            </span>
          </div>
        </div>
        <div class="field is-grouped">
          <div class="control">
            <button name="register" class="button is-primary">Sign Up</button>
          </div>
          <p class="control">
            <a class="button is-light" href="login.php">Login</a>
          </p>
        </div>
      </form>
    </div>
  </div>
</body>

</html>