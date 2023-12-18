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

$username_exist = false;
$email_exist = false;
$register_success = false;

if (isset($_POST['register'])) {

  if (!Utilities::validate(["fullname", "username", "email", "password"])) {
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
    bad_requests();
  }
  if (!preg_match("/^[a-zA-Z0-9\.\_]+$/", $username)) {
    bad_requests();
  }
  if (!Utilities::length_is_valid($username, 4)) {
    bad_requests();
  }
  if (!Utilities::length_is_valid($_password, 6)) {
    bad_requests();
  }
  if (Utilities::check("email", $email)) {
    $email_exist = true;
    goto out;
  }
  if (Utilities::check("username", $username)) {
    $username_exist = true;
    goto out;
  }

  Utilities::store($fullname, $username, $email, $password);

  $register_success = true;

}

out:

?>

<!DOCTYPE html>

<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <link rel="shortcut icon" href="favicon.ico" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css"
    integrity="sha512-HqxHUkJM0SYcbvxUw5P60SzdOTy/QVwA1JJrvaXJv4q7lmbDZCmZaqz01UPOaQveoxfYRv1tHozWGPMcuTBuvQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
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
      <?php if ($register_success) { ?>
        <p class="has-text-success has-text-centered">Pendaftaran berhasil</p>
      <?php } ?>
      <form class"has-fullwidth" action="" method="post">
        <div class="field">
          <label class="label">Full Name</label>
          <div class="control has-icons-left">
            <input class="input" type="text" id="fullname" name="fullname" placeholder="John Doe" required />
            <span class="icon is-small is-left">
              <i class="fas fa-user"></i>
            </span>
            <p class="has-text-danger is-size-7 my-2 is-hidden"></p>
          </div>
        </div>
        <div class="field">
          <label class="label">Username</label>
          <?php if ($username_exist) { ?>
            <p class="has-text-danger is-size-7 my-2">
              Username already exist.
            </p>
          <?php } ?>
          <div class="control has-icons-left">
            <input class="input" type="text" id="username" name="username" placeholder="johndoe" required />
            <span class="icon is-small is-left">
              <i class="fas fa-user-circle"></i>
            </span>
            <p class="has-text-danger is-size-7 my-2 is-hidden"></p>
          </div>
        </div>
        <div class="field">
          <label class="label">Email</label>
          <?php if ($email_exist) { ?>
            <p class="has-text-danger is-size-7 my-2">Email already exist.</p>
          <?php } ?>
          <div class="control has-icons-left">
            <input class="input" type="text" id="email" name="email" placeholder="johndoe@example.com" required />
            <span class="icon is-small is-left">
              <i class="fas fa-envelope"></i>
            </span>
            <p class="has-text-danger is-size-7 my-2 is-hidden"></p>
          </div>
        </div>
        <div class="field">
          <label class="label">Password</label>
          <div class="control has-icons-left">
            <input class="input" type="password" id="password" name="password" placeholder="********" required />
            <span class="icon is-small is-left">
              <i class="fas fa-lock"></i>
            </span>
            <p class="has-text-danger is-size-7 my-2 is-hidden"></p>
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
  <script src="assets/js/zepto/zepto.min.js"></script>
  <script>
    const validateEmail = (email) => {
      return String(email)
        .toLowerCase()
        .match(
          /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
    };

    const validateUsername = (username) => {
      return String(username).match(/^[a-zA-Z0-9\.\_]+$/);
    }
    const validateFullname = (fullname) => {
      return String(fullname).match(/^[a-zA-Z ]+$/);
    }

    $("#password").on("input", function () {
      if (this.value.length === 0) {
        $(this).removeClass("is-success");
        $(this).removeClass("is-danger");
        $(this).next().next().addClass("is-hidden");
      } else if (this.value.length > 0 && this.value.length < 8) {
        $(this).removeClass("is-success");
        $(this).addClass("is-danger");
        $(this).next().next().removeClass("is-hidden");
        $(this).next().next().text(`Password must be at least 8 characters long.`);
      } else {
        $(this).removeClass("is-danger");
        $(this).addClass("is-success");
        $(this).next().next().addClass("is-hidden");
      }
    });
    $("#username").on("input", function () {
      if (this.value.length === 0) {
        $(this).removeClass("is-success");
        $(this).removeClass("is-danger");
        $(this).next().next().addClass("is-hidden");
      } else if (!validateUsername(this.value)) {
        $(this).removeClass("is-success");
        $(this).addClass("is-danger");
        $(this).next().next().removeClass("is-hidden");
        $(this).next().next().text(`${this.value} is invalid username.`);
      } else if (this.value.length > 0 && this.value.length < 4) {
        $(this).removeClass("is-success");
        $(this).addClass("is-danger");
        $(this).next().next().removeClass("is-hidden");
        $(this).next().next().text(`Username must be at least 8 characters long.`);
      } else {
        $(this).removeClass("is-danger");
        $(this).addClass("is-success");
        $(this).next().next().addClass("is-hidden");
      }
    });
    $("#fullname").on("input", function () {
      if (this.value.length === 0) {
        $(this).removeClass("is-success");
        $(this).removeClass("is-danger");
        $(this).next().next().addClass("is-hidden");
      } else if (!validateFullname(this.value)) {
        $(this).removeClass("is-success");
        $(this).addClass("is-danger");
        $(this).next().next().removeClass("is-hidden");
        $(this).next().next().text(`${this.value} is invalid full name.`);
      } else {
        $(this).removeClass("is-danger");
        $(this).addClass("is-success");
        $(this).next().next().addClass("is-hidden");
      }
    });
    $("#email").on("input", function () {
      if (this.value.length === 0) {
        $(this).removeClass("is-success");
        $(this).removeClass("is-danger");
        $(this).next().next().addClass("is-hidden");
      } else if (!validateEmail(this.value)) {
        $(this).removeClass("is-success");
        $(this).addClass("is-danger");
        $(this).next().next().removeClass("is-hidden");
        $(this).next().next().text(`${this.value} is invalid email.`);
      } else {
        $(this).removeClass("is-danger");
        $(this).addClass("is-success");
        $(this).next().next().addClass("is-hidden");
      }
    });
  </script>
</body>

</html>