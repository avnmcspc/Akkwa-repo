<?php
include('../links/link-files.php');
include('../../config.php');

session_start();

// Check if OTP has expired (1 minute = 60 seconds)
$otpTimestamp = $_SESSION['otp_timestamp'];
$timeElapsed = time() - $otpTimestamp;
$name = $_SESSION['name'];
$email_address = $_SESSION['email'];
$password = $_SESSION['password'];

// Check if OTP has expired
if ($timeElapsed > 60) { // OTP expired after 60 seconds
  echo "<script>
            // Enable the resend OTP button if expired
            document.querySelector('button[name=\"resend\"]')?.removeAttribute('disabled');
          </script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Verification Form</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="icon" href="../assets/images/akkwa.webp" type="image/webp">


</head>

<body>
  <div class="otp-container">
    <header>
      <div class="col-12 col-lg-4">
        <img class="img-fluid rounded-start login-img" src="../assets/images/akkwa.webp" alt="AKKWA Logo" loading="lazy">
      </div>
    </header>
    <h4>Enter OTP Code</h4>
    <form method="POST">
      <div class="input-field">
        <input type="number" name="otp[]" />
        <input type="number" name="otp[]" disabled />
        <input type="number" name="otp[]" disabled />
        <input type="number" name="otp[]" disabled />
      </div>

      <button type="submit" name="verify">Verify OTP</button>
      <a style="background-color: gray; padding:10px; margin: 20px; color:white;  width: 100%;  cursor: pointer;" href="#" class="btn secondary" onclick="goToSignup()">Back to Login</a>

    </form>

  </div>

  <script>
    function goToSignup() {
      fetch('destroy_session.php', {
          method: 'POST',
        })
        .then(response => {
          if (response.ok) {
            // Redirect to the login page
            window.location.href = 'signup-form.php';
          } else {
            alert('Failed to destroy the session. Please try again.');
          }
        })
        .catch(error => console.error('Error:', error));
    }

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

    const inputs = document.querySelectorAll("input");
    const button = document.querySelector("button");
    inputs.forEach((input, index1) => {
      input.addEventListener("keyup", (e) => {
        const currentInput = input;
        const nextInput = input.nextElementSibling;
        const prevInput = input.previousElementSibling;

        if (currentInput.value.length > 1) {
          currentInput.value = "";
          return;
        }

        if (nextInput && nextInput.hasAttribute("disabled") && currentInput.value !== "") {
          nextInput.removeAttribute("disabled");
          nextInput.focus();
        }

        if (e.key === "Backspace") {
          inputs.forEach((input, index2) => {
            if (index1 <= index2 && prevInput) {
              input.setAttribute("disabled", true);
              input.value = "";
              prevInput.focus();
            }
          });
        }

        if (!inputs[3].disabled && inputs[3].value !== "") {
          button.classList.add("active");
          return;
        }
        button.classList.remove("active");
      });
    });

    window.addEventListener("load", () => inputs[0].focus());
  </script>
</body>

</html>

<?php
if (isset($_POST['verify'])) {
  $inputOtp = implode('', $_POST['otp']);
  $storedOtp = $_SESSION['otp'];
  $otpTimestamp = $_SESSION['otp_timestamp'];

  // Check if OTP is expired (5 minute)
  if (time() - $otpTimestamp > 300) {
    echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'OTP Expired',
                    text: 'Your OTP has expired. Please request a new one.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.replace('Verification_Otp.php');
                    }
                });
              </script>";
    exit();
  }

  // Verify OTP
  if ($inputOtp == $storedOtp) {

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $status = 'Active';
    $userType = 1;

    $sql = "INSERT INTO user_accounts (name, email_address, password, user_type, status)
                VALUES ('$name', '$email_address', '$hashedPassword', '$userType', '$status')";

    if (mysqli_query($conn, $sql)) {
      echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'OTP verified successfully you can now log in!',
                        confirmButtonText: 'OK'
                    }).then((result) => 
                    {
                        if (result.isConfirmed) {
                            window.location.replace('login-form.php');
                        }
                    });
                  </script>";
    } else {
      echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error saving your data. Please try again.',
                        confirmButtonText: 'OK'
                    });
                  </script>";
    }
  } else {
    echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid OTP',
                    text: 'The OTP you entered is incorrect. Please try again.',
                    confirmButtonText: 'OK'
                });
              </script>";
  }
}

?>