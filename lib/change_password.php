<?php

require_once "../utilities.php";

$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!Utilities::validate_string_input(["old_password", "new_password", "new_password2"])) {
        bad_requests();
    }

    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $new_password2 =  $_POST["new_password2"];

    if (!Utilities::length_is_valid($new_password, 8)) {
        $err = "Password must be at least 8 characters long";
        goto OUT;
    }


    if (!Utilities::verify_password($_SESSION["user_id"], $old_password, $new_password, $new_password2, $err)) {
        goto OUT;
    }

    if (Utilities::update_password($_SESSION["user_id"], $new_password)) {
        header("Location: profile.php");
        exit();
    } else {
        goto OUT;
    }
}

OUT:
?>

<h1 class="title">Change Password</h1>
<?php if ($err) { ?>
    <p class="has-text-danger has-text-centered"><?= $err ?></p>
<?php } ?>
<form method="post" action="">
    <div class="field">
        <span class="label">Old Password</span>
        <div class="control has-icons-left">
            <input type="password" class="input" name="old_password" />
            <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
            </span>
        </div>
    </div>
    <div class="field">
        <span class="label">New Password</span>
        <div class="control has-icons-left">
            <input type="password" class="input" name="new_password" />
            <span class="icon is-small is-left">
                <i class="fas fa-key"></i>
            </span>
        </div>
    </div>
    <div class="field mb-4">
        <span class="label">Confirm New Password</span>
        <div class="control has-icons-left">
            <input type="password" class="input" name="new_password2" />
            <span class="icon is-small is-left">
                <i class="fas fa-key"></i>
            </span>
        </div>
    </div>
    <div class="field has-text-centered">
        <div class="control">
            <input type="submit" value="change password" class="button is-primary" />
            <a href="profile.php" class="button is-primary">cancel</a>
        </div>
    </div>
</form>