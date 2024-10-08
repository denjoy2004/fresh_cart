<?php
session_start();
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Include your database connection file

$seller_username = $_SESSION['seller_username'] ?? null;

// Get the start and end date from the session or request
$startDate = $_SESSION['start_date'] ?? '';
$endDate = $_SESSION['end_date'] ?? '';

// Validate date inputs
if ($startDate && $endDate) {
    // Load the FPDF library
    require('fpdf/fpdf.php');

    // Create a new PDF document
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Title
    $pdf->Cell(0, 10, 'Sales Report', 0, 1, 'C');
    $pdf->Ln(10);

    // Add summary
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Seller: ' . htmlspecialchars($seller_username), 0, 1);
    $pdf->Cell(0, 10, 'Start Date: ' . htmlspecialchars($startDate), 0, 1);
    $pdf->Cell(0, 10, 'End Date: ' . htmlspecialchars($endDate), 0, 1);
    $pdf->Ln(10);

    // Query to get total sales and amount
    $query = "SELECT COUNT(*) AS total_sales, SUM(total_amount) AS total_amount FROM order_table WHERE seller_id = ? AND ordered_at BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $seller_username, $startDate, $endDate);
    $stmt->execute();
    $stmt->bind_result($totalSales, $totalAmount);
    $stmt->fetch();
    $stmt->close();

    // Add totals to PDF
    $pdf->Cell(0, 10, 'Total Sales: ' . htmlspecialchars($totalSales), 0, 1);
    $pdf->Cell(0, 10, 'Total Amount: $' . number_format($totalAmount, 2), 0, 1);
    $pdf->Ln(10);

    // Add sold products
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Sold Products:', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    $query = "SELECT p.product_name, SUM(ot.quantity) AS total_quantity FROM order_table ot JOIN product_table p ON ot.product_id = p.product_id WHERE ot.seller_id = ? AND ot.ordered_at BETWEEN ? AND ? GROUP BY p.product_name";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $seller_username, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(0, 10, htmlspecialchars($row['product_name']) . ': ' . htmlspecialchars($row['total_quantity']), 0, 1);
    }

    $stmt->close();
    $conn->close();

    // Output the PDF
    $pdf->Output('D', 'sales_report.pdf'); // The 'D' parameter forces download
} else {
    echo "Invalid date range.";
}
?>
