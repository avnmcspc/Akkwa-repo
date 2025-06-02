<?php
include('../config.php');
include('css-link.php');

session_start();
if (!isset($_SESSION["user"])) {
  header("Location: login-signup-operations/login-form.php");
  exit();
}

$currentUser = $_SESSION['email'];
$sql = "SELECT * FROM user_accounts WHERE email_address ='$currentUser'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  $name = $row['name'];
  $email = $row['email_address'];
  $usertype = $row['user_type'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>AKKWA | Water Level</title>
  <script src="../dist/login-signup-operations/scripts.js"></script>
  <link rel="stylesheet" href="user_profile.css">
</head>

<body class="with-welcome-text">
  <div class="container-scroller">
    <div class="row p-0 m-0 proBanner" id="proBanner">
      <div class="col-md-12 p-0 m-0">

      </div>
    </div>
    <!-- partial:partials/_navbar.html -->
    <nav
      class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
      <div
        class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <div class="me-3">
          <button
            class="navbar-toggler navbar-toggler align-self-center"
            type="button"
            data-bs-toggle="minimize">
            <span class="icon-menu"></span>
          </button>
        </div>
        <div>
          <a class="navbar-brand brand-logo" href="index.php">
            AKKWA
          </a>

        </div>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-top">
        <ul class="navbar-nav">
          <li class="nav-item fw-semibold d-none d-lg-block ms-0">
          <li class="nav-item fw-semibold d-none d-lg-block ms-0">
          <hr><hr>
            <h1 class="welcome-text">
              <span id="greeting"></span>, <span class="text-black fw-bold"><?php echo $name; ?></span>
            </h1>
            <div class="input-group " style="margin-top: 10px;">
                <h5 class="welcome-sub-text"><span id="date"></span> </h5>
                <div class="topbar-divider d-none d-sm-block"></div>
              </div>
              <h5 class="welcome-sub-text"><span id="time"></span> </h5>
            <h3 class="welcome-sub-text">
              ADMINISTRATOR
            </h3>

          </li>

          <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Function to update and display current date and time
    function updateDateTime() {
      const now = new Date();
      const currentHour = now.getHours();
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      const currentDate = now.toLocaleDateString('en-US', options);
      
      // Format time without seconds
      const currentTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

      // Determine the greeting based on the time of day
      let greeting;
      if (currentHour < 12) {
        greeting = 'Good Morning';
      } else if (currentHour < 18) {
        greeting = 'Good Afternoon';
      } else {
        greeting = 'Good Evening';
      }

      // Update the greeting
      document.getElementById('greeting').textContent = greeting;

      // Update date and time
      document.getElementById('date').textContent = currentDate;
      document.getElementById('time').textContent = currentTime;

      // Update hidden input fields (if needed)
      if (document.getElementById('dateToday')) {
        document.getElementById('dateToday').value = currentDate;
      }
      if (document.getElementById('timeToday')) {
        document.getElementById('timeToday').value = currentTime;
      }
    }

    setInterval(updateDateTime, 1000); // Update every second 
    updateDateTime();
  });
</script>
        </ul>
        <ul class="navbar-nav ms-auto" style="margin-top: 10px;">
          <li class="nav-item dropdown">
            <a
              class="nav-link count-indicator"
              id="countDropdown"
              href="#"
              data-bs-toggle="dropdown"
              aria-expanded="false">
              <img
                src="assets/images/faces/user.png"
                alt="image"
                class="img-sm profile-pic rounded-circle" />
            </a>
            <div
              class=" dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0 text-center"
              aria-labelledby="countDropdown">
              <div class="dropdown-divider"></div>
              <a class="dropdown-item preview-item ">
                <div class="preview-item-content flex-grow py-2 ">
                  <p class="fw-light small-text mb-0">
                    <?php echo $name; ?>
                  </p>
                  <p class="fw-light small-text mb-0">
                    <?php echo $email; ?>
                  </p>
                </div>
              </a>

              <a class="dropdown-item preview-item" href="#" id="profileBtn" data-bs-toggle="modal" data-bs-target="#profileModal">
                <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>
                My Profile
              </a>
              <a class="dropdown-item preview-item" onclick="confirmLogout()"><i
                  class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out
              </a>
            </div>
          </li>
        </ul>
        <button
          class="navbar-toggler navbar-toggler-right d-lg-none align-self-center"
          type="button"
          data-bs-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar" style="margin-top: 30px;">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="index.php">
              <i class="mdi mdi-grid-large menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item nav-category">FUNCTIONS</li>
          <li class="nav-item">
            <a
              class="nav-link"
              data-bs-toggle="collapse"
              href="#fish"
              aria-expanded="false"
              aria-controls="form-elements">
              <i class="menu-icon mdi mdi-fish"></i>
              <span class="menu-title">Fish</span>
              <i class="menu-arrow"></i>
            </a>

            <div class="collapse" id="fish">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="add_fish.php">Add a Fish</a>
                </li>
              </ul>
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="list_of_fish.php">List of Fish</a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-item">
            <a
              class="nav-link"
              data-bs-toggle="collapse"
              href="#controls"
              aria-expanded="false"
              aria-controls="form-elements">
              <i class="menu-icon fa fa-cog"></i>
              <span class="menu-title">Controls</span>
              <i class="menu-arrow"></i>
            </a>

            <div class="collapse" id="controls">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="controls.php">
                    Feeding Control
                  </a>
                </li>
              </ul>
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="water_control.php">
                    Water Control</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              data-bs-toggle="collapse"
              href="#tables"
              aria-expanded="false"
              aria-controls="tables">
              <i class="menu-icon fa fa-bar-chart-o"></i>
              <span class="menu-title">Monitoring</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="tables">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="water_temperature.php">Water Temperature</a>
                </li>
              </ul>
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="ph_levels.php">PH Level</a>
                </li>
              </ul>
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="water_level.php">Water Level</a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-4">
              <button type="button" class="btn btn-primary btn-rounded btn-icon" data-bs-toggle="modal" data-bs-target="#exportModal" title="Export" style="margin-top:30px">
                <i> Export to PDF </i>
                <i style="margin-left: 5px;" class="mdi mdi-file-export"></i>
              </button>
            </div>

            <!-- Modal Structure -->
            <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="generateReportForm" action="export-to-pdf/water_level_report.php" method="POST">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Generate Report for Water Level</h5>
                      <button type="button" class="btn btn-light" data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="modal-body">
                      <!-- Date Range Input Fields -->
                      <div class="mb-3">
                        <label for="fromDate" class="form-label">From</label>
                        <input type="date" class="form-control" id="fromDate" name="from_date" required>
                      </div>
                      <div class="mb-3">
                        <label for="toDate" class="form-label">To</label>
                        <input type="date" class="form-control" id="toDate" name="to_date" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>

            <script>
              document.getElementById('exportModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('fromDate').value = '';
                document.getElementById('toDate').value = '';
              });

              document.getElementById("generateReportForm").addEventListener("submit", function(e) {
                const fromDateInput = document.getElementById("fromDate");
                const toDateInput = document.getElementById("toDate");
                const fromDate = fromDateInput.value;
                const toDate = toDateInput.value;
                const today = new Date().toISOString().split("T")[0]; // Get today's date in 'YYYY-MM-DD' format

                // Check if 'fromDate' or 'toDate' is in the future
                if (fromDate > today) {
                  e.preventDefault(); // Prevent form submission
                  Swal.fire({
                    icon: "error",
                    title: "Invalid Date",
                    text: "'From' date cannot be in the future. Please select a valid date.",
                  }).then(() => {
                    fromDateInput.focus(); // Focus on the 'From' date input
                  });
                } else if (toDate > today) {
                  e.preventDefault(); // Prevent form submission
                  Swal.fire({
                    icon: "error",
                    title: "Invalid Date",
                    text: "'To' date cannot be in the future. Please select a valid date.",
                  }).then(() => {
                    toDateInput.focus(); // Focus on the 'To' date input
                  });
                }
              });
            </script>

            <div class="col-sm-12">
              <div class="home-tab">
                <div
                  class="d-sm-flex align-items-center justify-content-between border-bottom"></div>
                <div class="tab-content tab-content-basic">
                  <div
                    class="tab-pane fade show active"
                    id="overview"
                    role="tabpanel"
                    aria-labelledby="overview">
                    <div class="row">
                      <div class="col-lg-12 d-flex flex-column">
                        <div class="row flex-grow">
                          <div
                            class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">

                                <h4 class="card-title">Water Level</h4>
                                <div class="table-responsive">
                                  <table id="WaterLevel" class="table table-striped">
                                    <thead>
                                      <tr>
                                        <th> Water Level</th>
                                        <th> Date/Time</th>
                                      </tr>
                                    </thead>
                                    <tbody id="waterLevelList">


                                    </tbody>
                                  </table>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div
            class="d-sm-flex justify-content-center justify-content-sm-between">
            <span
              class="text-muted text-center text-sm-left d-block d-sm-inline-block">Premium
              <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a>
              from BootstrapDash.</span>
            <span
              class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">Copyright © 2023. All rights reserved.</span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->


   <!-- Profile Modal -->
 <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="color: #000000;" id="profileModalLabel">Change User Profile</h5>
          <button type="button" class="btn" style="background-color: gray; color:white" data-bs-dismiss="modal" aria-label="Close">X</button>
        </div>
        <div class="modal-body">
          <form action="updateProfile.php" method="POST" id="profileForm">
            <!-- Name -->
            <div class="mb-3">
              <label for="name" class="form-label">Name:</label>
              <label for="name" class="form-label"><?php echo htmlspecialchars($_SESSION['name'] ?? $name); ?></label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Change Name" value="<?php echo htmlspecialchars($_SESSION['name'] ?? $name); ?>">
            </div>
            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label">Email Address:</label>
              <label for="email" class="form-label"><?php echo htmlspecialchars($_SESSION['email'] ?? $email); ?></label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Change Email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? $email); ?>">
            </div>
            <!-- Current Password -->
            <div class="mb-3">
              <label for="currentPassword" class="form-label">Current Password:</label>
              <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Enter your current password">
            </div>
            <!-- Password -->
            <div class="mb-3">
              <label for="password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password (if changing)" onkeyup="validatePassword()">
              <ul id="passwordRequirements" class="requirements-list">
                <li id="uppercase" class="invalid">Include one Uppercase letter</li>
                <li id="lowercase" class="invalid">Include one Lowercase letter</li>
                <li id="number" class="invalid">Include one Number</li>
                <li id="specialChar" class="invalid">Include one Special character</li>
                <li id="minLength" class="invalid">At least 8 characters long</li>
              </ul>
            </div>
            <!-- Confirm Password -->
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirm Password:</label>
              <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn" style="background-color: gray; color:white" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" name="updateProfile" class="btn btn-primary" id="saveButton">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const profileModal = document.getElementById("profileModal");
      const profileForm = document.getElementById("profileForm");

      profileModal.addEventListener("hidden.bs.modal", function() {
        profileForm.reset(); // Clears all the input fields
      });
    });


    // Password validation function
    function validatePassword() {
      const password = document.getElementById('password').value;

      // Check for uppercase letters
      if (password.match(/[A-Z]/)) {
        document.getElementById('uppercase').classList.remove('invalid');
        document.getElementById('uppercase').classList.add('valid');
      } else {
        document.getElementById('uppercase').classList.remove('valid');
        document.getElementById('uppercase').classList.add('invalid');
      }

      // Check for lowercase letters
      if (password.match(/[a-z]/)) {
        document.getElementById('lowercase').classList.remove('invalid');
        document.getElementById('lowercase').classList.add('valid');
      } else {
        document.getElementById('lowercase').classList.remove('valid');
        document.getElementById('lowercase').classList.add('invalid');
      }

      // Check for numbers
      if (password.match(/[0-9]/)) {
        document.getElementById('number').classList.remove('invalid');
        document.getElementById('number').classList.add('valid');
      } else {
        document.getElementById('number').classList.remove('valid');
        document.getElementById('number').classList.add('invalid');
      }

      // Check for special characters
      if (password.match(/[^A-Za-z0-9]/)) {
        document.getElementById('specialChar').classList.remove('invalid');
        document.getElementById('specialChar').classList.add('valid');
      } else {
        document.getElementById('specialChar').classList.remove('valid');
        document.getElementById('specialChar').classList.add('invalid');
      }

      // Check for minimum length
      if (password.length >= 8) {
        document.getElementById('minLength').classList.remove('invalid');
        document.getElementById('minLength').classList.add('valid');
      } else {
        document.getElementById('minLength').classList.remove('valid');
        document.getElementById('minLength').classList.add('invalid');
      }
    }

    // Form validation before submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const currentPassword = document.getElementById('currentPassword').value;

      // Check if current password is provided
      if (!currentPassword) {
        e.preventDefault();
        Swal.fire({
          icon: 'error',
          title: 'Current Password Required',
          text: 'Please enter your current password to make changes',
          confirmButtonColor: '#4B49AC'
        });
        return;
      }

      // If new password is provided, validate
      if (password) {
        // Check if password meets all requirements
        const isValid =
          document.getElementById('uppercase').classList.contains('valid') &&
          document.getElementById('lowercase').classList.contains('valid') &&
          document.getElementById('number').classList.contains('valid') &&
          document.getElementById('specialChar').classList.contains('valid') &&
          document.getElementById('minLength').classList.contains('valid');

        if (!isValid) {
          e.preventDefault();
          Swal.fire({
            icon: 'error',
            title: 'Invalid Password',
            text: 'Please ensure your password meets all requirements',
            confirmButtonColor: '#4B49AC'
          });
          return;
        }

        // Check if password and confirm password match
        if (password !== confirmPassword) {
          e.preventDefault();
          Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'New password and confirmation password do not match',
            confirmButtonColor: '#4B49AC'
          });
          return;
        }
      }
    });

    // Initialize SweetAlert2 messages from session
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: '<?php echo $_SESSION['success']; ?>',
          confirmButtonColor: '#4B49AC'
        });
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
          icon: 'error',
          title: 'Error',
          html: '<?php echo $_SESSION['error']; ?>',
          confirmButtonColor: '#4B49AC'
        });
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['info'])): ?>
        Swal.fire({
          icon: 'info',
          title: 'Information',
          text: '<?php echo $_SESSION['info']; ?>',
          confirmButtonColor: '#4B49AC'
        });
        <?php unset($_SESSION['info']); ?>
      <?php endif; ?>
    });

    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
      // Add a checkbox for showing passwords if you'd like
      const showPasswordCheckbox = document.createElement('div');
      showPasswordCheckbox.className = 'form-check mb-3';
      showPasswordCheckbox.innerHTML = `
        <input class="form-check-input" type="checkbox" id="showPasswords">
        <label class="form-check-label" id="labelShowPassword" for="showPasswords">Show Passwords</label>
    `;

      document.getElementById('confirmPassword').parentNode.after(showPasswordCheckbox);

      // Add event listener to the checkbox
      document.getElementById('showPasswords').addEventListener('change', function() {
        const passwordFields = [
          document.getElementById('currentPassword'),
          document.getElementById('password'),
          document.getElementById('confirmPassword')
        ];

        passwordFields.forEach(field => {
          field.type = this.checked ? 'text' : 'password';
        });
      });
    });
  </script> 
  
</body>

</html>
<script>
  function loadHistory() {
    fetch("get-readings/get_water_list.php")
      .then(response => response.json())
      .then(data => {
        let historyTable = document.getElementById("waterLevelList");
        historyTable.innerHTML = "";

        data.forEach(row => {

          let date = new Date(row.recorded_at);
          let options = {
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "numeric",
            minute: "2-digit",
            hour12: true
          };
          let formattedDate = date.toLocaleString("en-US", options);


          historyTable.innerHTML += `<tr><td>${row.level}</td><td>${formattedDate}</td></tr>`;
        });
        // Initialize DataTable after data is loaded
        if ($.fn.DataTable.isDataTable('#WaterLevel')) {
          $('#WaterLevel').DataTable().destroy();
        }

        $('#WaterLevel').DataTable({
          responsive: true
        });
      })
      .catch(error => console.error("Error:", error));
  }


  loadHistory();
</script>