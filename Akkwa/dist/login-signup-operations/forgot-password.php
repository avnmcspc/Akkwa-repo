<?php
include('../links/link-files.php');
include('../../config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Added viewport meta tag -->
    <title>Forgot Password</title>
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
            max-width: 500px;
            padding: 20px;
        }

        .form {
            background: #fff;
            padding: 30px 35px;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.8);
            width: 100%;
        }

        @media (max-width: 600px) {
            .form {
                padding: 20px;
            }
        }

        .input-group.mb-3 {
            margin-bottom: 15px;
        }

        .btn {

            color: white;
            width: 100%;
            padding: 10px;
        }

        .btn:hover {
            color: #fff;
        }

        .text-center a {
            color: #6610f2;
            /* Consistent link color */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form">
            <form class="was-validated" method="POST">
                <h4 class="text-center pt-3">Forgot Password</h4><br>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                    <input type="email" class="form-control" placeholder="Email" name="email" autocomplete="off" required>
                </div>
                <div class="col text-center">
                    <button type="submit" class="btn" style="background-color: #6610f2" name="recover">Check Email</button>
                </div>
            </form>
            <p class="text-center mt-3">Remember your password? <a href="login-form.php">Login here</a></p>
        </div>
    </div>

    <!-- Include SweetAlert2 script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>

</html>

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

session_start();

if (isset($_POST["recover"])) {
    include('../../config.php');
    $email = $_POST["email"];

    $sql = mysqli_query($conn, "SELECT * FROM user_accounts WHERE email_address ='$email'");
    $rowCount = mysqli_num_rows($sql);
    $fetch = mysqli_fetch_assoc($sql);

    if ($rowCount <= 0) {
?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Sorry, this email does not exist.'
            });
        </script>
        <?php
    } else {
        // Generate 4-digit OTP
        $otp = sprintf("%04d", mt_rand(0, 9999));

        // Store OTP in session
        $_SESSION['email'] = $email;
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_timestamp'] = time(); // For OTP expiration

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

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 25px; border: 1px solid #e0e0e0;'>
                    <!-- Logo/Image centered -->
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <img src='cid:logo' alt='AKKWA Logo' style='max-width: 250px; height: auto;'>
                    </div>
                    
                    <h2 style='color: #333333; text-align: center; margin-bottom: 20px;'>Password Reset Request</h2>
                    
                    <p style='color: #555555; font-size: 16px;'>Hello,</p>
                    
                    <p style='color: #555555; font-size: 16px;'>We received a request to reset your password for your AKKWA account. To continue with the password reset process, please use the verification code below:</p>
                    
                    <!-- OTP in a highlighted box -->
                    <div style='background-color: #f7f9fc; border: 1px dashed #cbd5e0; border-radius: 5px; padding: 15px; margin: 20px 0; text-align: center;'>
                        <p style='font-size: 28px; font-weight: bold; color: #4a5568; margin: 0;'>$otp</p>
                        <p style='font-size: 12px; color: #718096; margin: 5px 0 0 0;'>This code will expire in 10 minutes for security reasons</p>
                    </div>
                    
                    <p style='color: #555555; font-size: 16px;'>If you did not request a password reset, please ignore this email or contact our support team if you believe this is an error.</p>
                    
                    <p style='color: #555555; font-size: 16px; margin-top: 25px;'>Thank you,<br>The AKKWA Team</p>
                </div>
                
                <div style='text-align: center; padding: 15px; color: #888888; font-size: 12px;'>
                    <p>© 2025 AKKWA. All rights reserved.</p>
                    <p>For security reasons, please do not reply to this email. If you need assistance, please contact <a href='mailto:support@akkwa.com' style='color: #4a5568;'>support@akkwa.com</a></p>
                </div>
            </div>
            ";

            $mail->addEmbeddedImage('../assets/images/akkwa.webp', 'logo', 'logo.png');


            $mail->send();
        ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'OTP has been sent to your email.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.replace("Otp.php");
                    }
                });
            </script>
        <?php
        } catch (Exception $e) {
        ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to send OTP. Please try again.'
                });
            </script>
<?php
        }
    }
}

?>