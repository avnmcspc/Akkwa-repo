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

// SQL query to get total fish quantity
$sql = "SELECT SUM(quantity) AS total_fish_quantity FROM fish";
$result = $conn->query($sql);

// Get the total quantity
$totalFishQuantity = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalFishQuantity = $row["total_fish_quantity"];
}


// SQL query to count distinct fish types
$typeSql = "SELECT COUNT(DISTINCT type) AS fish_type_count FROM fish";
$typeResult = $conn->query($typeSql);

// Get the count of fish types
$fishTypeCount = 0;
if ($typeResult->num_rows > 0) {
    $row = $typeResult->fetch_assoc();
    $fishTypeCount = $row["fish_type_count"];
}


$typeSql = "SELECT COUNT(DISTINCT type) AS fish_type_count FROM fish";
$typeResult = $conn->query($typeSql);

// Get the count of fish types
$fishTypeCount = 0;
if ($typeResult->num_rows > 0) {
    $row = $typeResult->fetch_assoc();
    $fishTypeCount = $row["fish_type_count"];
}


// Ordering by timestamp descending to get the most recent entry
$sql = "SELECT * FROM `schedule` ORDER BY `timestamp` DESC LIMIT 1";
$result = $conn->query($sql);

// Initialize variables
$hour = "";
$minute = "";
$timestamp = "";

// Fetch the data
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $hour = $row["hour"];
  $minute = $row["minute"];
  $timestamp = $row["timestamp"];
  
  // Format the timestamp into (month, day, year)
  $formattedDate = date("F j, Y", strtotime($timestamp));
  
  // Format hour and minute with correct plural/singular
  $hourText = $hour == 1 ? "1 hour" : $hour . " hours";
  $minuteText = $minute == 1 ? "1 minute" : $minute . " minutes";
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
  <title>AKKWA</title>
  <script src="../dist/login-signup-operations/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <!-- Date and Time -->
            <form id="dateTimeContainer" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">

            </form>


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
      <nav class="sidebar sidebar-offcanvas sidebar-icon-only" id="sidebar" style="margin-top: 30px;">
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

                      <div class="col-sm-3">
                        <div
                          class="statistics-details d-flex align-items-center justify-content-between">

                          <div>
                            <p class="statistics-title">Total of fish</p>
                            <h3 class="rate-percentage"><?php echo $totalFishQuantity; ?></h3> 
                          </div>
                        </div>
                      </div>

                      <div class="col-sm-3">
                        <div
                          class="statistics-details d-flex align-items-center justify-content-between">

                          <div>
                            <p class="statistics-title">Fish Category</p>
                            <h3 class="rate-percentage"><?php echo $fishTypeCount; ?></h3> 
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div
                          class="statistics-details d-flex align-items-center justify-content-between">

                          <div>
                            <p class="statistics-title">Current Set Feeding Time</p>
                            <h3 class="rate-percentage"><?php echo $hourText . " and " . $minuteText; ?></h3>
                          </div>
                        </div>
                      </div>
                      <style>
                          .ph-badge {
                              display: inline-block;
                              padding: 15px 25px;
                              font-size: 1.2rem;
                              font-weight: bold;
                              color: white;
                              border-radius: 30px;
                              text-align: center;
                              transition: all 0.3s ease-in-out;
                              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                          }
                          .good-condition {
                              background: linear-gradient(to right, #28a745, #218838);
                          }
                          .bad-condition {
                              background: linear-gradient(to right, #dc3545, #c82333);
                          }
                      </style>
                      <div class="col-sm-3">
                        <div
                          class="statistics-details d-flex align-items-center justify-content-between">

                          <div>
                            <p class="statistics-title">Water Condition</p>
                            <h3 class="rate-percentage"><span id="phBadge" class="ph-badge">Loading...</span></h3>
                          </div>
                        </div>
                      </div>
                      
                    </div>
                    <div class="row">
  <!-- PH Level Chart -->
  <div class="col-lg-12 d-flex flex-column">
    <div class="row flex-grow">
      <div class="col-12 grid-margin stretch-card">
        <div class="card card-rounded">
          <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start">
              <div>
                <h4 class="card-title card-title-dash">PH Level Chart</h4>
              </div>
              <div id="performanceLine-legend"></div>
            </div>
            <div class="chartjs-wrapper mt-4">
              <canvas id="performanceLine"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Left Column -->
  <div class="col-lg-8 d-flex flex-column">
    <div class="row flex-grow">
      <!-- Water Temperature Chart -->
      <div class="col-8 grid-margin stretch-card">
        <div class="card card-rounded">
          <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start">
              <div>
                <h4 class="card-title card-title-dash">Water Temperature Level Chart</h4>
              </div>
            </div>
            <div class="d-sm-flex align-items-center mt-1 justify-content-between">
              <div class="d-sm-flex align-items-center mt-4 justify-content-between"></div>
              <div class="me-3">
                <div id="marketingOverview-legend"></div>
              </div>
            </div>
            <div class="chartjs-bar-wrapper mt-3">
              <canvas id="marketingOverview"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Battery Chart -->
      <div class="col-4 grid-margin stretch-card">
        <div class="card card-rounded">
          <div class="card-body">
            <h4 class="card-title card-title-dash">Battery Percentage</h4>
            <div style="height: 400px; width: 300px;">
              <canvas id="batteryChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- You had an empty row here; kept it commented out -->
    <!-- <div class="row flex-grow">
      <div class="col-md-6 col-lg-6 grid-margin stretch-card"></div>
      <div class="col-md-6 col-lg-6 grid-margin stretch-card"></div>
    </div> -->
  </div>

  <!-- Right Column -->
  <div class="col-lg-4 d-flex flex-column align-items-center">
    <div class="row flex-grow">
      <!-- Water Level Chart -->
      <div class="col-12 grid-margin stretch-card">
        <div class="card card-rounded">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h4 class="card-title card-title-dash">Water Level Chart</h4>
                </div>
                <div>
                  <canvas class="my-auto" id="doughnutChart"></canvas>
                </div>
                <div id="doughnutChart-legend" class="mt-5 text-center"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Placeholder row if needed -->
    <div class="row flex-grow">
      <div class="col-12 grid-margin stretch-card"></div>
    </div>
  </div>
</div>

<!-- Battery Chart Script -->
<script>
  function fetchBatteryDataAndRenderChart() {
    fetch('get_battery_percentage.php')
      .then(response => response.json())
      .then(data => {
        console.log("API Response:", data);
        const percentage = parseFloat(data.battery_percentage);
        if (!isNaN(percentage)) {
          renderBatteryChart(percentage);
        } else {
          console.error("Invalid battery percentage:", data.battery_percentage);
        }
      })
      .catch(error => console.error('Fetch error:', error));
  }

  function renderBatteryChart(batteryPercentage) {
    const canvas = document.getElementById('batteryChart');
    if (!canvas) {
      console.error("Canvas element not found");
      return;
    }

    const ctx = canvas.getContext('2d');

    if (window.batteryChart instanceof Chart) {
      window.batteryChart.destroy();
    }

    window.batteryChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ["Current Energy", "Consumed Energy"],
        datasets: [{
          data: [batteryPercentage, 100 - batteryPercentage],
          backgroundColor: ['#1F3BB3', '#FDD0C7'],
          borderColor: '#ffffff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true }
        }
      }
    });
  }

  document.addEventListener('DOMContentLoaded', fetchBatteryDataAndRenderChart);
  setInterval(fetchBatteryDataAndRenderChart, 10000);
</script>


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
    function updatePHBadge(phValue) {
    const badge = document.getElementById("phBadge");

    if (phValue < 3.5) {
        badge.className = "badge bg-danger text-white p-3 fs-5";
        badge.innerText = `Too Much Acid (pH: ${phValue})`;
    } 
    else if (phValue > 10.5) {
        badge.className = "badge bg-danger text-white p-3 fs-5";
        badge.innerText = `Too Much Alkaline (pH: ${phValue})`;
    }
    else if (phValue < 6.5 || phValue > 8.5) {
        badge.className = "badge bg-warning text-white p-3 fs-5";
        badge.innerText = `Bad Condition (pH: ${phValue})`;
    } 
    else {
        badge.className = "badge bg-info text-white p-3 fs-5";
        badge.innerText = `Normal Condition (pH: ${phValue})`;
    }
}

    // Example function to fetch latest pH value (replace this with your actual fetching method)
    function fetchLatestPH() {
        fetch("get-readings/get_latest_reading_ph_level.php") // Replace with actual API URL
            .then(response => response.json())
            .then(data => {
                let latestPH = data.ph_level;
                console.log('aivan',data);
                 // Replace with actual data structure
                updatePHBadge(latestPH);
            })
            .catch(error => {
                console.error("Error fetching pH data:", error);
                document.getElementById("phBadge").innerText = "Error fetching data";
            });
    }

    // Fetch data every 5 seconds
    setInterval(fetchLatestPH, 5000);

    // Initial fetch
    fetchLatestPH();
</script>