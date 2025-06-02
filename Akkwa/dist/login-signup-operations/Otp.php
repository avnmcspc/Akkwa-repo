<?php
include('../links/link-files.php');
include('../../config.php');

session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['otp'])) {
  header("Location: forgot-password.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    </form>
  </div>

  <script>
    const inputs = document.querySelectorAll("input"),
      button = document.querySelector("button");

    inputs.forEach((input, index1) => {
      input.addEventListener("keyup", (e) => {
        const currentInput = input,
          nextInput = input.nextElementSibling,
          prevInput = input.previousElementSibling;

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

  // Check if OTP is expired (5 minutes)
  if (time() - $otpTimestamp > 300) {
?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'OTP Expired',
        text: 'Please request a new OTP.',
        confirmButtonText: 'OK'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.replace("forgot-password.php");
        }
      });
    </script>
  <?php
    exit();
  }

  // Verify OTP
  if ($inputOtp == $storedOtp) {
  ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'OTP verified successfully!',
        confirmButtonText: 'OK'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.replace("new-password.php");
        }
      });
    </script>
  <?php
  } else {
  ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Invalid OTP',
        text: 'The OTP you entered is incorrect. Please try again.',
        confirmButtonText: 'OK'
      });
    </script>
<?php
  }
}
?>