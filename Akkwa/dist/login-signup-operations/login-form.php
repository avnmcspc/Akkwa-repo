<?php
include('../links/link-files.php');
include('../../config.php');

session_start();

//Check if user is already logged in
if (isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}


if (isset($_SESSION['logout_message'])) {
    $logout_message = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']);
    echo "
    <script>                                                    
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Logged Out!',
                text: '$logout_message',
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../links/customize.css">
    <link rel="icon" href="../assets/images/akkwa.webp" type="image/webp">



    <style>
        .container {
            max-width: 100%;
            padding: 15px;
            margin-top: 50px;
        }

        @media (min-width: 768px) {
            .container {
                max-width: 720px;
            }
        }

        @media (min-width: 992px) {
            .container {
                max-width: 960px;
            }
        }

        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }

        .login-img {
            object-fit: contain;
            max-height: 100%;
            max-width: 100%;
            padding: 20px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-btn svg {
            margin-right: 0.5rem;
        }

        @media (max-width: 767px) {
            .card-body {
                padding: 2rem !important;
            }
        }
    </style>
</head>

<body>
    <section class="py-5">
        <div class="container">
            <div class="card shadow-lg">
                <div class="row g-0">
                    <div class="col-md-6 d-none d-md-block">
                        <img class="img-fluid rounded-start login-img" src="../assets/images/akkwa.webp" alt="AKKWA Logo" loading="lazy">
                    </div>

                    <?php

                    // Handle form submission
                    if (isset($_POST["submit"])) {
                        $email = $_POST['email'];
                        $password = trim($_POST['password']);

                        // Fetch user details
                        $sql = "SELECT * FROM user_accounts WHERE email_address = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "s", $email);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

                        if ($user) {

                            // error_log("User found: " . print_r($user, true));
                            // error_log("Password entered: " . $password);
                            // error_log("Hashed password in DB: " . $user["password"]);
                            // error_log("Password verification result: " . (password_verify($password, $user["password"]) ? "true" : "false"));

                            // echo "<pre>";
                            // echo "User found: " . htmlspecialchars(print_r($user, true)) . "\n";
                            // echo "Password entered: " . htmlspecialchars($password) . "\n";
                            // echo "Hashed password in DB: " . htmlspecialchars($user["password"]) . "\n";
                            // echo "Password verification result: " . (password_verify($password, $user["password"]) ? "true" : "false") . "\n";
                            // echo "</pre>";
                            // exit(); // Stop execution to see the debug info
                            // Check if the USER ACCOUNT is 'ACTIVE'.
                            if ($user['status'] == 'Active') {
                                // Check if the Entered Password and Password are both EQUAL/MATCH.
                                if (password_verify($password, $user["password"])) {
                                    // Set session variables
                                    $_SESSION["user"] = "yes";
                                    $_SESSION['name'] = $user['name'];
                                    $_SESSION['email'] = $email;
                                    $_SESSION['usertype'] = $user['user_type'];
                                    echo "<script>
                                             window.location.href = '../index.php';
                                          </script>";
                                } else {
                                    // INCORRECT PASSWORD
                                    echo "<script>
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Invalid Credentials',
                                                text: 'Invalid password.',
                                                confirmButtonText: 'OK'
                                            }).then(() => {
                                                window.location.href = 'login-form.php';
                                            });
                                        </script>";
                                }
                            } else {
                                // User account is currently DEACTIVATED
                    ?>
                                <script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Account Deactivated',
                                        text: 'Your account is deactivated. Please ask the administrator.',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = "login-form.php";
                                    });
                                </script>
                    <?php
                            }
                        } else {
                            // Email not found
                            echo "<script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Invalid Email',
                                    text: 'The email address entered does not exist.',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href = 'login-form.php';
                                });
                            </script>";
                        }
                    }
                    ?>


                    <div class="col-md-6 shadow-lg">
                        <div class="card-body p-4 p-xl-5">
                            <h4 class="text text-secondary">WELCOME</h4>
                            <h2 class="mb-4">Log in to AKKWA</h2>
                            <form action="login-form.php" method="POST">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" autocomplete="off" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="show-password">
                                    <label class="form-check-label" for="show-password">Show Password</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" name="submit" style="background-color:#6610f2; color: white;">Log in </button>
                            </form>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between mb-4">
                                <a href="signup-form.php" class="text-decoration-none">Sign Up </a>
                                <a href="forgot-password.php" class="text-decoration-none">Forgot password?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('show-password').addEventListener('change', function() {
            var passwordField = document.getElementById('password');
            var confirmPasswordField = document.getElementById('confirm-password');
            var type = this.checked ? 'text' : 'password';
            passwordField.type = type;
            confirmPasswordField.type = type;
        });
    </script>
</body>

</html>