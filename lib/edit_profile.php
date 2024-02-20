<?php

require_once "../utilities.php";

function bad_requests(): void
{
    http_response_code(400);
    echo "<h1>Error 400 Bad Request!</h1>";
    exit();
}

const ALLOWED_PHOTO_EXTS = [
    "jpg", "jpeg", "png"
];

const MAX_ALLOWED_SIZE = 1024 * 1024 * 2;

$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!Utilities::validate_string_input(["fullname", "username", "email"])) {
        bad_requests();
    }
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];

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

    $user_id = Utilities::get_user_id_by_field("username", $username);

    if ($user_id !== $_SESSION["user_id"] && $user_id !== 0) {
        $err = "Username already in use";
        goto OUT;
    }

    $user_id = Utilities::get_user_id_by_field("email", $email);


    if ($user_id !== $_SESSION["user_id"] && $user_id !== 0) {
        $err = "Email already in use";
        goto OUT;
    }

    if (!empty($_FILES["profile_image"]["name"]) && is_string($_FILES["profile_image"]["name"])) {
        $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, ALLOWED_PHOTO_EXTS)) {
            $err = "Only PNG, JPG, and JPEG files are allowed";
            goto OUT;
        } elseif ($_FILES['profile_image']['size'] > MAX_ALLOWED_SIZE) {
            $err = "Maximum file size allowed is 2MB";
            goto OUT;
        } else {
            $hashfile = hash_file('sha256', $_FILES['profile_image']['tmp_name'], true);
            $filename =  bin2hex($hashfile) . "." . $file_ext;

            $file_path = "storage/files/" . $filename;

            $upload = move_uploaded_file($_FILES["profile_image"]["tmp_name"], $file_path);

            if (!$upload) {
                $err = "Failed to move the uploaded file";
                goto OUT;
            }
        }
    } else {
        $hashfile = NULL;
        $file_ext = NULL;
    }

    Utilities::update_profile($_SESSION["user_id"], $fullname, $username, $email, $hashfile, $file_ext);
    header("Location: profile.php");
    exit();
}

OUT:
?>

<h1 class="title">Edit Profile</h1>
<?php if ($err) : ?>
    <p class="has-text-danger is-size-7 my-2"><?= $err ?></p>
<?php endif; ?>
<form method="post" action="" enctype="multipart/form-data">
    <div class="field">
        <figure class="image is-128x128">
            <img class="is-rounded" src="./storage/files/<?= $profile["profile_image"] ?>">
        </figure>
    </div>
    <div class="field">
        <div class="file">
            <label class="file-label">
                <input class="file-input" type="file" name="profile_image">
                <span class="file-cta">
                    <span class="file-icon">
                        <i class="fas fa-upload"></i>
                    </span>
                    <span class="file-label">
                        Choose a file…
                    </span>
                </span>
            </label>
        </div>
    </div>
    <div class="field">
        <span class="label">Full Name</span>
        <div class="control has-icons-left">
            <input type="text" class="input" id="fullname" name="fullname" value="<?= $profile["fullname"] ?>" />
            <span class="icon is-small is-left">
                <i class="fas fa-user"></i>
            </span>
            <p class="has-text-danger is-size-7 my-2 is-hidden"></p>
        </div>
    </div>
    <div class="field">
        <span class="label">Username</span>
        <div class="control has-icons-left">
            <input type="text" class="input" id="username" name="username" value="<?= $profile["username"] ?>" />
            <span class="icon is-small is-left">
                <i class="fas fa-user-circle"></i>
            </span>
            <p class="has-text-danger is-size-7 my-2 is-hidden"></p>
        </div>
    </div>
    <div class="field mb-4">
        <span class="label">Email</span>
        <div class="control has-icons-left">
            <input type="email" class="input" id="email" name="email" value="<?= $profile["email"] ?>" />
            <span class="icon is-small is-left">
                <i class="fas fa-envelope"></i>
            </span>
            <p class="has-text-danger is-size-7 my-2 is-hidden"></p>
        </div>
    </div>
    <div class="field has-text-centered">
        <div class="control">
            <input type="submit" value="update" class="button is-primary" />
            <a href="profile.php" class="button is-primary">cancel</a>
        </div>
    </div>
</form>

<script src="assets/js/zepto/zepto.min.js"></script>
<script>
    const validateInput = (inputField, validationFn, errorMessage) => {
        if (inputField.value.length === 0) {
            $(inputField).removeClass("is-success").removeClass("is-danger").next().next().addClass("is-hidden");
        } else if (!validationFn(inputField.value)) {
            $(inputField).removeClass("is-success").addClass("is-danger").next().next().removeClass("is-hidden").text(`${inputField.value} ${errorMessage}`);
        } else {
            $(inputField).removeClass("is-danger").addClass("is-success").next().next().addClass("is-hidden");
        }
    };

    const validateEmail = (email) => String(email).toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
    const validateUsername = (username) => String(username).match(/^[a-zA-Z0-9\.\_]+$/);
    const validateFullname = (fullname) => String(fullname).match(/^[a-zA-Z ]+$/);

    $("#fullname, #username, #email").on("input", function() {
        switch (this.id) {
            case "username":
                validateInput(this, validateUsername, "is an invalid username.");
                break;
            case "fullname":
                validateInput(this, validateFullname, "is an invalid full name.");
                break;
            case "email":
                validateInput(this, validateEmail, "is an invalid email.");
                break;
        }
    });
</script>