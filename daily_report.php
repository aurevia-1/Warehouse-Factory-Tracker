<?php
require('fpdf/fpdf.php');
include 'includes/db.php';

date_default_timezone_set('Asia/Karachi');
$today = date('Y-m-d');

// ✅ Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Daily Stock Report', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Date: ' . $today, 0, 1);
$pdf->Ln(5);

// ===== Warehouse Section =====
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Warehouse Stock Added (Today)', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Product Name', 1);
$pdf->Cell(40, 10, 'Quantity', 1);
$pdf->Cell(60, 10, 'Entry Date', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$warehouseTotal = 0;

$query1 = "SELECT product_name, quantity, entry_date FROM warehouse_stock WHERE DATE(entry_date) = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("s", $today);
$stmt1->execute();
$result1 = $stmt1->get_result();

if ($result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        $pdf->Cell(60, 10, $row['product_name'], 1);
        $pdf->Cell(40, 10, $row['quantity'], 1);
        $formattedDate = date('Y-m-d', strtotime($row['entry_date']));
        $pdf->Cell(60, 10, $formattedDate, 1);
        $pdf->Ln();
        $warehouseTotal += $row['quantity'];
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(60, 10, 'TOTAL', 1);
    $pdf->Cell(40, 10, $warehouseTotal, 1);
    $pdf->Cell(60, 10, '', 1);
} else {
    $pdf->Cell(160, 10, 'No warehouse records found for today.', 1, 1);
}

$pdf->Ln(15);

// ===== Factory Section =====
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Stock Sent to Factory (Today)', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Factory Name', 1);
$pdf->Cell(40, 10, 'Quantity', 1);
$pdf->Cell(60, 10, 'Transfer Date', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$factoryTotal = 0;

$query2 = "SELECT factory_name, quantity_sent, transfer_date FROM factory_transfer WHERE DATE(transfer_date) = ?";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("s", $today);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        $pdf->Cell(60, 10, $row['factory_name'], 1);
        $pdf->Cell(40, 10, $row['quantity_sent'], 1);
        $formattedDate = date('Y-m-d', strtotime($row['transfer_date']));
        $pdf->Cell(60, 10, $formattedDate, 1);
        $pdf->Ln();
        $factoryTotal += $row['quantity_sent'];
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(60, 10, 'TOTAL', 1);
    $pdf->Cell(40, 10, $factoryTotal, 1);
    $pdf->Cell(60, 10, '', 1);
} else {
    $pdf->Cell(160, 10, 'No factory records found for today.', 1, 1);
}

$pdf->Ln(15);

// ===== 📦 Stock Summary =====
$availableStock = $warehouseTotal - $factoryTotal;

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Stock Summary (Today)', 0, 1);
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(60, 10, 'Total Stock Added', 1);
$pdf->Cell(40, 10, $warehouseTotal, 1);
$pdf->Ln();

$pdf->Cell(60, 10, 'Total Stock Sent', 1);
$pdf->Cell(40, 10, $factoryTotal, 1);
$pdf->Ln();

// Check for negative balance
if ($availableStock < 0) {
    $pdf->SetTextColor(255, 0, 0); // Red text
    $pdf->Cell(60, 10, 'Available Stock', 1);
    $pdf->Cell(40, 10, $availableStock, 1);
    $pdf->SetTextColor(0, 0, 0); // Reset back to black
    $pdf->Ln();
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(160, 10, 'Warning: Available stock is negative. Please review transactions.', 0, 1);
} else {
    $pdf->Cell(60, 10, 'Available Stock', 1);
    $pdf->Cell(40, 10, $availableStock, 1);
}

$pdf->Ln(10);

// ✅ Output PDF
$pdf->Output('D', 'daily_report_' . $today . '.pdf');
?>
