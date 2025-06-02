<?php
// Clear any previous output
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

include('../../config.php');
require('../fpdf181/fpdf.php');

// PDF Class Definition with improved pagination
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(70, 20, "AKKWA", 0, 1);
        $this->SetFont('Arial', '', 14);
        $this->Cell(50, 7, "Feeding Control Monitoring", 0, 1);
        $this->Cell(50, 7, "Feeding Control Report", 0, 1);

        $this->SetY(15);
        $this->SetX(-60);
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(50, 10, "REPORT", 0, 1);

        // Add image below the REPORT text
        // Adjust X and Y coordinates to position it properly
        $this->Image('../assets/images/Akkwa-Alternate-Logo-2.png', 140, 13, 50); // Change the path to your image file

        // Add horizontal line to separate header from content
        $this->Line(0, 48, 210, 48);

        // Add table headers on each page
        if ($this->PageNo() >= 1) {
            $this->SetY(55);
            $this->SetFont('Arial', 'B', 12);
            $this->SetFillColor(200, 220, 255);
            $this->Ln();
            $this->Cell(30, 10, "ID", 1, 0, "C", true);
            $this->Cell(80, 10, "Type of Feeding Control", 1, 0, "C", true);
            $this->Cell(70, 10, "Date and Time Recorded", 1, 0, "C", true);

            $this->Ln();
        }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Validate FPDF library
if (!class_exists('FPDF')) {
    die('FPDF class not found. Check your library inclusion.');
}

// Fetch and validate dates and description filter
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';

// Validate date inputs
if (empty($from_date) || empty($to_date)) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Input',
                text: 'Please provide both start and end dates.',
                confirmButtonText: 'OK'
            }).then((result) => {
                window.location.href = '../controls.php';
            });
        });
    </script>";
    exit;
}

// Format dates for display and query
$formatted_from_date = date('F d Y', strtotime($from_date));
$formatted_to_date = date('F d Y', strtotime($to_date));

// Prepare the base query
$query = "SELECT * FROM spin_logs WHERE timestamp BETWEEN ? AND ?";

// Append description filter if provided
if (!empty($description)) {
    $query .= " AND description = ?";
}

// Prepare and execute the query
try {
    $stmt = $conn->prepare($query);

    // Bind parameters
    if (!empty($description)) {
        $stmt->bind_param("sss", $from_date, $to_date, $description); // Adding description parameter
    } else {
        $stmt->bind_param("ss", $from_date, $to_date); // No description filter
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Clear all output buffers before sending content
    while (ob_get_level()) {
        ob_end_clean();
    }
    ob_start();

    if ($result->num_rows > 0) {
        // Set PDF headers explicitly
        header('Content-Type: application/pdf');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $pdf = new PDF('P', 'mm', 'A4'); // Portrait mode
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Title
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(-35);
        $pdf->Cell(0, 30, "Report Period: " . $formatted_from_date . " to " . $formatted_to_date, 0, 1, "C");
        $pdf->Cell(0, -17, "Selected Option: " . $description, 0, 1, "C");

        if (!empty($description)) {
            // DISPLAY THE SELECTED FEEDING OPTIONS
            //  $pdf->Cell(0, 10, "Description: " . $description, 0, 1, "C");
        }
        $pdf->SetY(65);
        $pdf->Ln(10);

        // Table Content
        $pdf->SetFont('Arial', '', 10);
        $totalEntries = 0;
        $rowHeight = 10;

        while ($data = $result->fetch_assoc()) {
            // Check if we need a new page (less than rowHeight mm from bottom)
            if ($pdf->GetY() > $pdf->GetPageHeight() - 25) {
                $pdf->AddPage();
            }

            $pdf->Cell(30, $rowHeight, $data['id'], 1, 0, "C"); // ID column

            // Fix for the level field - correctly handle as string
            $level_value = isset($data['description']) ? $data['description'] : 'N/A';
            $pdf->Cell(80, $rowHeight, $level_value, 1, 0, "C"); // Level column

            $formatted_date = date('F d Y', strtotime($data['timestamp']));
            $pdf->Cell(70, $rowHeight, $formatted_date, 1, 0, "C"); // Recorded Date column

            $pdf->Ln(); // Move to the next row
            $totalEntries++;
        }

        // Add Total Count
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Total Records: " . $totalEntries, 0, 1);

        // Output PDF
        $pdf->Output('I', 'feeding_control_report.pdf');
        exit;
    } else {
        // Clear any buffered output
        while (ob_get_level()) {
            ob_end_clean();
        }

        echo "<!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Data Found',
                        text: 'No data was found for the selected filters. Please try different criteria.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../controls.php';
                        }
                    });
                }
            </script>
        </body>
        </html>";
        exit;
    }
} catch (Exception $e) {
    // Handle error gracefully
    echo "<!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred: " . addslashes($e->getMessage()) . "',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    window.location.href = '../controls.php';
                });
            }
        </script>
    </body>
    </html>";
    exit;
} finally {
    // Close database connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
