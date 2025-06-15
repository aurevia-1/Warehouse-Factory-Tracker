<?php
require('fpdf/fpdf.php');
include 'includes/db.php';

date_default_timezone_set('Asia/Karachi');
$currentMonth = date('Y-m'); // e.g. 2025-06

// ✅ Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Monthly Stock Report', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Month: ' . date('F Y'), 0, 1);
$pdf->Ln(5);

// ===== Warehouse Section =====
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Warehouse Stock Added (This Month)', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Product Name', 1);
$pdf->Cell(40, 10, 'Quantity', 1);
$pdf->Cell(60, 10, 'Entry Date', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$warehouseTotal = 0;

$result = $conn->query("SELECT product_name, quantity, entry_date FROM warehouse_stock WHERE entry_date LIKE '$currentMonth%'");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $formattedDate = date('Y-m-d', strtotime($row['entry_date']));
        $pdf->Cell(60, 10, $row['product_name'], 1);
        $pdf->Cell(40, 10, $row['quantity'], 1);
        $pdf->Cell(60, 10, $formattedDate, 1);
        $pdf->Ln();
        $warehouseTotal += $row['quantity'];
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(60, 10, 'TOTAL', 1);
    $pdf->Cell(40, 10, $warehouseTotal, 1);
    $pdf->Cell(60, 10, '', 1);
} else {
    $pdf->Cell(160, 10, 'No warehouse records found for this month.', 1, 1);
}

$pdf->Ln(15);

// ===== Factory Section =====
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Stock Sent to Factory (This Month)', 0, 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Factory Name', 1);
$pdf->Cell(40, 10, 'Quantity', 1);
$pdf->Cell(60, 10, 'Transfer Date', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$factoryTotal = 0;

$result2 = $conn->query("SELECT factory_name, quantity_sent, transfer_date FROM factory_transfer WHERE transfer_date LIKE '$currentMonth%'");
if ($result2 && $result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        $formattedDate = date('Y-m-d', strtotime($row['transfer_date']));
        $pdf->Cell(60, 10, $row['factory_name'], 1);
        $pdf->Cell(40, 10, $row['quantity_sent'], 1);
        $pdf->Cell(60, 10, $formattedDate, 1);
        $pdf->Ln();
        $factoryTotal += $row['quantity_sent'];
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(60, 10, 'TOTAL', 1);
    $pdf->Cell(40, 10, $factoryTotal, 1);
    $pdf->Cell(60, 10, '', 1);
} else {
    $pdf->Cell(160, 10, 'No factory records found for this month.', 1, 1);
}

$pdf->Ln(15);

// ===== Summary Section =====
$availableStock = $warehouseTotal - $factoryTotal;

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Stock Summary (This Month)', 0, 1);
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(60, 10, 'Total Stock Added', 1);
$pdf->Cell(40, 10, $warehouseTotal, 1);
$pdf->Ln();

$pdf->Cell(60, 10, 'Total Stock Sent', 1);
$pdf->Cell(40, 10, $factoryTotal, 1);
$pdf->Ln();

// ❗ Show warning if available stock is negative
if ($availableStock < 0) {
    $pdf->SetTextColor(255, 0, 0); // Red color
    $pdf->Cell(60, 10, 'Available Stock', 1);
    $pdf->Cell(40, 10, $availableStock, 1);
    $pdf->SetTextColor(0, 0, 0); // Reset
    $pdf->Ln();
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(160, 10, 'Warning: Available stock is negative. Please review transactions.', 0, 1);
} else {
    $pdf->Cell(60, 10, 'Available Stock', 1);
    $pdf->Cell(40, 10, $availableStock, 1);
}

$pdf->Ln(10);

// ✅ Output PDF
$pdf->Output('D', 'monthly_report_' . date('F_Y') . '.pdf');
?>
