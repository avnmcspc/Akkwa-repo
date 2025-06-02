<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up Form</title>
    <link rel="stylesheet" href="../links/customize.css">
    <link rel="icon" href="../assets/images/akkwa.webp" type="image/webp">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .password-requirements {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .requirement {
            color: #dc3545;
        }

        .requirement.met {
            color: #198754;
        }

        .password-strength-meter {
            height: 5px;
            background-color: #f0f0f0;
            margin: 5px 0;
            border-radius: 3px;
        }

        .strength-meter {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease-in-out;
        }

        .weak {
            background-color: #dc3545;
            width: 33%;
        }

        .medium {
            background-color: #ffc107;
            width: 66%;
        }

        .strong {
            background-color: #198754;
            width: 100%;
        }
    </style>
</head>

<body>
    <section class="py-3 py-md-5">
        <div class="container">
            <div class="card shadow-lg">
                <div class="row g-0">
                    <div class="col-lg-6 d-none d-lg-block">
                        <img class="img-fluid rounded-start login-img" src="../assets/images/akkwa.webp" alt="BootstrapBrain Logo" loading="lazy">
                    </div>

                    <?php
                    include('../links/link-files.php');
                    include('../../config.php');

                    use PHPMailer\PHPMailer\PHPMailer;
                    use PHPMailer\PHPMailer\Exception;

                    require '../PHPMailer/src/Exception.php';
                    require '../PHPMailer/src/PHPMailer.php';
                    require '../PHPMailer/src/SMTP.php';

                    session_start();

                    if (isset($_POST["submitInfo"])) {
                        // Retrieve the form data
                        $name = mysqli_real_escape_string($conn, $_POST["name"]);
                        $email = mysqli_real_escape_string($conn, $_POST['email-address']);
                        $password = mysqli_real_escape_string($conn, $_POST["password"]);

                        // Check if email already exists
                        $check_query = mysqli_prepare($conn, "SELECT * FROM user_accounts WHERE email_address = ?");
                        mysqli_stmt_bind_param($check_query, "s", $email);
                        mysqli_stmt_execute($check_query);
                        mysqli_stmt_store_result($check_query);
                        $rowCount = mysqli_stmt_num_rows($check_query);
                        mysqli_stmt_close($check_query);

                        // If the email is already registered
                        if ($rowCount > 0) {
                            echo "<script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Email Already Exists',
                                        text: 'Please use a different email address.'
                                    });
                                </script>";
                        } else {

                            $otp = rand(1000, 9999);
                            $_SESSION['otp'] = $otp;
                            $_SESSION['email'] = $email;
                            $_SESSION['name'] = $name;
                            $_SESSION['password'] = $password;
                            $_SESSION['otp_timestamp'] = time();


                            try {

                                $mail = new PHPMailer(true);
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'lauroprescillas18@gmail.com';
                                $mail->Password = 'azyy taor ujko ievl';
                                $mail->Port = 465;
                                $mail->SMTPSecure = 'ssl';
                                $mail->isHTML(true);
                                $mail->setFrom('lauroprescillas18@gmail.com', 'AKKWA');
                                $mail->addAddress($email);
                                $mail->Subject = "Email Verification OTP";

                                // Enable debugging
                                // $mail->SMTPDebug = 2; // Set to 2 for verbose debugging
                                $mail->Body = "
                                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                                    <div style='background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 25px; border: 1px solid #e0e0e0;'>
                                        <!-- Logo/Image centered -->
                                        <div style='text-align: center; margin-bottom: 20px;'>
                                            <img src='cid:logo' alt='AKKWA Logo' style='max-width: 250px; height: auto;'>
                                        </div>
                                        
                                        <h2 style='color: #333333; text-align: center; margin-bottom: 20px;'>Email Verification</h2>
                                        
                                        <p style='color: #555555; font-size: 16px;'>Hi there,</p>
                                        
                                        <p style='color: #555555; font-size: 16px;'>Thanks for signing up! Please use the verification code below to complete your registration:</p>
                                        
                                        <!-- OTP in a highlighted box -->
                                        <div style='background-color: #f7f9fc; border: 1px dashed #cbd5e0; border-radius: 5px; padding: 15px; margin: 20px 0; text-align: center;'>
                                            <p style='font-size: 28px; font-weight: bold; color: #4a5568; margin: 0;'>$otp</p>
                                            <p style='font-size: 12px; color: #718096; margin: 5px 0 0 0;'>This code will expire in 10 minutes</p>
                                        </div>
                                        
                                        <p style='color: #555555; font-size: 16px;'>If you didn't request this code, you can safely ignore this email.</p>
                                        
                                        <p style='color: #555555; font-size: 16px; margin-top: 25px;'>Cheers,<br>The AKKWA Team</p>
                                    </div>
                                    
                                    <div style='text-align: center; padding: 15px; color: #888888; font-size: 12px;'>
                                        <p>© 2025 AKKWA. All rights reserved.</p>
                                        <p>If you have any questions, we're here to help.</p>
                                    </div>
                                </div>
                                ";

                                $mail->addEmbeddedImage('../assets/images/akkwa.webp', 'logo', 'logo.png');

                                $mail->send();

                                // After OTP is sent, prompt the user to verify OTP
                                echo "<script>
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Verification OTP Sent!',
                                            text: 'An OTP has been sent to your email for verification.',
                                            confirmButtonText: 'Proceed'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = 'Verification_Otp.php'; // Redirect to OTP verification page
                                            }
                                        });
                                    </script>";
                            } catch (Exception $e) {
                                // Log the actual error message
                                // error_log("PHPMailer Error: " . $mail->ErrorInfo);
                                echo "<script>
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Mail error: " . str_replace("'", "\'", $mail->ErrorInfo) . "'
                                        });
                                      </script>";
                            }
                        }
                    }
                    ?>


                    <div class="col-lg-6 shadow-lg">
                        <div class="card-body p-4 p-xl-5">
                            <h4 class="text-secondary">WELCOME</h4>
                            <h2 class="mb-4">Sign up Here</h2>
                            <form method="POST" action="signup-form.php" id="signupForm" onsubmit="return validateForm()">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Your full name" autocomplete="off" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email-address" id="email" placeholder="name@example.com" autocomplete="off" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                    <div class="password-strength-meter">
                                        <div class="strength-meter"></div>
                                    </div>
                                    <div class="password-requirements">
                                        <div id="length-check" class="requirement">✕ At least 8 characters</div>
                                        <div id="uppercase-check" class="requirement">✕ At least one uppercase letter</div>
                                        <div id="lowercase-check" class="requirement">✕ At least one lowercase letter</div>
                                        <div id="number-check" class="requirement">✕ At least one number</div>
                                        <div id="special-check" class="requirement">✕ At least one special character</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm-password" class="form-label">Re-Enter Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="confirmPassword" id="confirm-password" required>
                                    <div id="password-match" class="requirement mt-2"></div>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="show-password">
                                    <label class="form-check-label" for="show-password">Show Password</label>
                                </div>
                                <button type="submit" name="submitInfo" class="btn btn-primary w-100">Register Now</button>
                            </form>
                            <hr class="my-4">
                            <p class="text-center mb-0">Already have an account? <a href="login-form.php" class="text-decoration-none">Log In Here</a></p>
                        </div>
                    </div>
                </div>
    </section>


    <script>
        // Password validation function
        function validatePassword(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            // Update requirement checks
            document.getElementById('length-check').className =
                'requirement' + (requirements.length ? ' met' : '');
            document.getElementById('uppercase-check').className =
                'requirement' + (requirements.uppercase ? ' met' : '');
            document.getElementById('lowercase-check').className =
                'requirement' + (requirements.lowercase ? ' met' : '');
            document.getElementById('number-check').className =
                'requirement' + (requirements.number ? ' met' : '');
            document.getElementById('special-check').className =
                'requirement' + (requirements.special ? ' met' : '');

            // Update checkmark/x symbols
            document.getElementById('length-check').innerHTML =
                (requirements.length ? '✓' : '✕') + ' At least 8 characters';
            document.getElementById('uppercase-check').innerHTML =
                (requirements.uppercase ? '✓' : '✕') + ' At least one uppercase letter';
            document.getElementById('lowercase-check').innerHTML =
                (requirements.lowercase ? '✓' : '✕') + ' At least one lowercase letter';
            document.getElementById('number-check').innerHTML =
                (requirements.number ? '✓' : '✕') + ' At least one number';
            document.getElementById('special-check').innerHTML =
                (requirements.special ? '✓' : '✕') + ' At least one special character';

            // Calculate password strength
            const strengthMeter = document.querySelector('.strength-meter');
            const metRequirements = Object.values(requirements).filter(Boolean).length;

            if (metRequirements <= 2) {
                strengthMeter.className = 'strength-meter weak';
            } else if (metRequirements <= 4) {
                strengthMeter.className = 'strength-meter medium';
            } else {
                strengthMeter.className = 'strength-meter strong';
            }

            return Object.values(requirements).every(Boolean);
        }

        // Real-time password validation
        document.getElementById('password').addEventListener('input', function() {
            validatePassword(this.value);
            checkPasswordMatch();
        });

        // Real-time password matching
        document.getElementById('confirm-password').addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const matchDisplay = document.getElementById('password-match');

            if (confirmPassword) {
                if (password === confirmPassword) {
                    matchDisplay.innerHTML = '✓ Passwords match';
                    matchDisplay.className = 'requirement met';
                } else {
                    matchDisplay.innerHTML = '✕ Passwords do not match';
                    matchDisplay.className = 'requirement';
                }
            } else {
                matchDisplay.innerHTML = '';
            }
        }

        // Email validation function
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Real-time email validation
        document.getElementById('email').addEventListener('input', function() {
            const emailField = this.value;
            const emailError = document.getElementById('email-error');

            if (emailField && !validateEmail(emailField)) {
                emailError.style.display = 'block'; // Show error message
            } else {
                emailError.style.display = 'none'; // Hide error message
            }
        });

        // Form submission validation
        function validateForm() {
            const emailField = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            // Validate email
            if (!validateEmail(emailField)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.',
                });
                return false;
            }

            // Validate password
            if (!validatePassword(password)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Password',
                    text: 'Please meet all password requirements'
                });
                return false;
            }

            // Validate password match
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Passwords Do Not Match',
                    text: 'Please make sure both passwords are identical'
                });
                return false;
            }

            return true;
        }

        // Show/hide password toggle
        document.getElementById('show-password').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm-password');
            const type = this.checked ? 'text' : 'password';
            passwordField.type = type;
            confirmPasswordField.type = type;
        });

        // Disable the browser back button
        (function() {
            // Push a new state to the browser's history
            window.history.pushState(null, "", window.location.href);

            // Listen for the popstate event
            window.addEventListener("popstate", function() {
                // Push the same state again to prevent navigation
                window.history.pushState(null, "", window.location.href);
            });
        })();
    </script>

</body>

</html>