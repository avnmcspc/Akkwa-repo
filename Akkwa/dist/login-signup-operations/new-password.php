<?php
include('../links/link-files.php');
include('../../config.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create a New Password</title>
    <link rel="icon" href="../assets/images/akkwa.webp" type="image/webp">


    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f8f9fa;
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 30px;
        }

        .form {
            background: #fff;
            padding: 40px 45px;
            border-radius: 5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.8);
            width: 100%;
        }

        @media (max-width: 600px) {
            .form {
                padding: 30px;
            }
        }

        .input-group.mb-3 {
            margin-bottom: 20px;
        }

        .form-control {
            height: 50px;
            font-size: 1em;
        }

        .btn {
            color: white;
            width: 100%;
            padding: 10px;
            font-size: 1.1em;
            height: 50px;
            margin-bottom: 20px;

        }

        .btn:hover {
            color: #fff;
        }
    </style>


</head>

<body>
    <div class="container">
        <div class="form">
            <div class="col-lg-8 bg-white m-auto rounded-top">
                <form method="POST" id="passwordResetForm" onsubmit="return validateForm()">
                    <h4 class="text-center pt-3">Reset your Password</h4> <br>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" placeholder="Enter New Password" name="password" required>
                    </div>
                    <div id="passwordRequirements" class="text-muted small mb-3">
                        Password must contain at least 8 characters, including special characters.
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm-password" placeholder="Confirm New Password" name="confirm-password" required>
                    </div>
                    <div id="passwordMatch" class="text-danger small mb-3"></div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="show-password">
                        <label class="form-check-label" for="show-password">Show Password</label>
                    </div>

                    <div class="col text-center">
                        <button type="submit" class="btn" style="background-color:#6610f2; color: white;" name="reset">Confirm Password</button>
                    </div>
                    <p class="text-center m-3">
                        Return to Login Up Form? <a href="login-form.php">Click Here </a>
                    </p>
                </form>

                <?php
                if (isset($_POST["reset"])) {
                    $psw = $_POST["password"];
                    $confirmPsw = $_POST["confirm-password"];
                    $Email = $_SESSION['email'];

                    // Server-side password validation
                    if (strlen($psw) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $psw)) {
                ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Password',
                                text: 'Password must be at least 8 characters long and contain special characters.'
                            });
                        </script>
                    <?php
                        exit();
                    }

                    // Check if passwords match
                    if ($psw !== $confirmPsw) {
                    ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Passwords Do Not Match',
                                text: 'Please ensure both passwords are identical.'
                            });
                        </script>
                    <?php
                        exit();
                    }

                    $hash = password_hash($psw, PASSWORD_DEFAULT);

                    $sql = mysqli_query($conn, "SELECT * FROM user_accounts WHERE email_address='$Email'");
                    $query = mysqli_num_rows($sql);
                    $fetch = mysqli_fetch_assoc($sql);

                    if ($Email) {
                        $new_pass = $hash;
                        $user_id = $fetch['id'];

                        mysqli_query($conn, "UPDATE user_accounts SET password ='$new_pass' WHERE id ='$user_id'");
                    ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Your password has been successfully reset.',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.replace("login-form.php");
                                }
                            });
                        </script>
                    <?php
                    } else {
                    ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Please try again.'
                            });
                        </script>
                <?php
                    }
                }
                ?>

                <script>
                    // Show/hide password toggle
                    document.getElementById('show-password').addEventListener('change', function() {
                        var passwordField = document.getElementById('password');
                        var confirmPasswordField = document.getElementById('confirm-password');
                        var type = this.checked ? 'text' : 'password';
                        passwordField.type = type;
                        confirmPasswordField.type = type;
                    });

                    // Real-time password validation
                    document.getElementById('password').addEventListener('input', validatePassword);
                    document.getElementById('confirm-password').addEventListener('input', checkPasswordMatch);

                    function validatePassword() {
                        var password = document.getElementById('password').value;
                        var requirements = document.getElementById('passwordRequirements');

                        var isValid = password.length >= 8 && /[!@#$%^&*(),.?":{}|<>]/.test(password);

                        if (!isValid) {
                            requirements.classList.remove('text-muted');
                            requirements.classList.add('text-danger');
                        } else {
                            requirements.classList.remove('text-danger');
                            requirements.classList.add('text-success');
                        }
                    }

                    function checkPasswordMatch() {
                        var password = document.getElementById('password').value;
                        var confirmPassword = document.getElementById('confirm-password').value;
                        var matchMessage = document.getElementById('passwordMatch');

                        if (password !== confirmPassword) {
                            matchMessage.textContent = 'Passwords do not match';
                        } else {
                            matchMessage.textContent = '';
                        }
                    }

                    function validateForm() {
                        var password = document.getElementById('password').value;
                        var confirmPassword = document.getElementById('confirm-password').value;

                        // Check password length and special characters
                        if (password.length < 8 || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Password',
                                text: 'Password must be at least 8 characters long and contain special characters.'
                            });
                            return false;
                        }

                        // Check if passwords match
                        if (password !== confirmPassword) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Passwords Do Not Match',
                                text: 'Please ensure both passwords are identical.'
                            });
                            return false;
                        }

                        return true;
                    }
                </script>
            </div>
        </div>
    </div>
</body>

</html>