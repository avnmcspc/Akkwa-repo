<?php
include('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: login-signup-operations/login-form.php");
    exit();
}

// Function to validate password
function validatePassword($password)
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must include at least one uppercase letter";
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must include at least one lowercase letter";
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must include at least one number";
    }

    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must include at least one special character";
    }

    return $errors;
}

// Check if form is submitted
if (isset($_POST['updateProfile'])) {
    $currentUser = $_SESSION['email'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate current password is provided
    if (empty($currentPassword)) {
        $_SESSION['error'] = "Please enter your current password to make changes";
        header("Location: index.php");
        exit();
    }

    // Get current user data
    $sql = "SELECT * FROM user_accounts WHERE email_address = '$currentUser'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify current password
        if (password_verify($currentPassword, $user['password'])) {
            $updateFields = [];
            $hasChanges = false;

            // Check if name is changed
            if ($name != $user['name']) {
                $updateFields[] = "name = '$name'";
                $hasChanges = true;
            }

            // Check if email is changed
            if ($email != $currentUser) {
                // Check if new email already exists
                $checkEmail = "SELECT * FROM user_accounts WHERE email_address = '$email' AND email_address != '$currentUser'";
                $emailResult = mysqli_query($conn, $checkEmail);

                if (mysqli_num_rows($emailResult) > 0) {
                    $_SESSION['error'] = "Email address is already in use by another account";
                    header("Location: index.php");
                    exit();
                }

                $updateFields[] = "email_address = '$email'";
                $hasChanges = true;
            }

            // Check if password is being changed
            if (!empty($newPassword)) {
                // Validate password
                $passwordErrors = validatePassword($newPassword);

                if (!empty($passwordErrors)) {
                    $_SESSION['error'] = implode("<br>", $passwordErrors);
                    header("Location: index.php");
                    exit();
                }

                // Check if passwords match
                if ($newPassword != $confirmPassword) {
                    $_SESSION['error'] = "New password and confirmation password do not match";
                    header("Location: index.php");
                    exit();
                }

                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateFields[] = "password = '$hashedPassword'";
                $hasChanges = true;
            }

            // Update user data if there are changes
            if ($hasChanges) {
                $updateQuery = "UPDATE user_accounts SET " . implode(", ", $updateFields) . " WHERE email_address = '$currentUser'";

                if (mysqli_query($conn, $updateQuery)) {
                    // Update session variables
                    $_SESSION['name'] = $name;
                    if ($email != $currentUser) {
                        $_SESSION['email'] = $email;
                    }

                    $_SESSION['success'] = "Profile updated successfully";
                } else {
                    $_SESSION['error'] = "Error updating profile: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['info'] = "No changes were made to your profile";
            }
        } else {
            $_SESSION['error'] = "Current password is incorrect";
        }
    } else {
        $_SESSION['error'] = "User not found";
    }

    header("Location: index.php");
    exit();
}
