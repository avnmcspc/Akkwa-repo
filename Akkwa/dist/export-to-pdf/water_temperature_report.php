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
        $this->Cell(50, 7, "Water Temperature Monitoring", 0, 1);
        $this->Cell(50, 7, "Water Temperature Report", 0, 1);

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
            $this->Cell(80, 10, "Water Temperature", 1, 0, "C", true);
            $this->Cell(70, 10, "Date Recorded", 1, 0, "C", true);

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

// Fetch and validate dates
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';

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
                window.location.href = '../water_temperature.php';
            });
        });
    </script>";
    exit;
}

// Format dates for display and query
$formatted_from_date = date('F d Y', strtotime($from_date));
$formatted_to_date = date('F d Y', strtotime($to_date));

try {
    // Prepare query with parameterized statement to prevent SQL injection
    $query = "SELECT * FROM temperature_level WHERE recorded_at BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $from_date, $to_date);
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
        $pdf->Ln(-35); // Move down after header
        $pdf->Cell(0, 30, "Report Period: " . $formatted_from_date . " to " . $formatted_to_date, 0, 1, "C");
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
            $level_value = isset($data['temperature']) ? $data['temperature'] : 'N/A';
            $pdf->Cell(80, $rowHeight, $level_value, 1, 0, "C"); // Level column

            $formatted_date = date('F d Y', strtotime($data['recorded_at']));
            $pdf->Cell(70, $rowHeight, $formatted_date, 1, 0, "C"); // Recorded Date column

            $pdf->Ln(); // Move to the next row
            $totalEntries++;
        }

        // Add Total Count
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Total Records: " . $totalEntries, 0, 1);

        // Output PDF
        $pdf->Output('I', 'water_temperature_report.pdf');
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
                        text: 'No data was found for the selected date range. Please try a different range.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../water_temperature.php';
                        }
                    });
                }
            </script>
        </body>
        </html>";
        exit;
    }
} catch (Exception $e) {
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
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred: " . addslashes($e->getMessage()) . "',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    window.location.href = '../water_temperature.php';
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
