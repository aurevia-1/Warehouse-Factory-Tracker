<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.html");
    exit();
}

include 'includes/db.php';

// Get total added to warehouse
$added_result = $conn->query("SELECT SUM(quantity) AS total_added FROM warehouse_stock");
$row1 = $added_result->fetch_assoc();
$total_added = $row1['total_added'] ?? 0;

// Get total sent to factory
$sent_result = $conn->query("SELECT SUM(quantity_sent) AS total_sent FROM factory_transfer");
$row2 = $sent_result->fetch_assoc();
$total_sent = $row2['total_sent'] ?? 0;

// Calculate available stock
$available_stock = $total_added - $total_sent;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>

<body class="bg-blue-900 min-h-screen flex flex-col items-center justify-center p-4 space-y-8">

  <div class="bg-blue-800 text-white rounded-2xl shadow-2xl w-full max-w-lg md:max-w-xl lg:max-w-2xl p-8 space-y-6">
    <h2 class="text-3xl font-bold text-center">📊 Welcome to, <b>SHEIKH & SONS</b></h2>
    <p class="text-center text-blue-200">You are logged in.</p>

    <!-- ✅ STOCK SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-blue-600 p-4 rounded-lg shadow text-center">
        <h3 class="text-xl font-bold">Total Added</h3>
        <p class="text-3xl mt-2"><?php echo $total_added; ?></p>
      </div>

      <div class="bg-yellow-500 p-4 rounded-lg shadow text-center">
        <h3 class="text-xl font-bold">Total Sent</h3>
        <p class="text-3xl mt-2"><?php echo $total_sent; ?></p>
      </div>

      <div class="bg-green-600 p-4 rounded-lg shadow text-center">
        <h3 class="text-xl font-bold">Available</h3>
        <p class="text-3xl mt-2"><?php echo $available_stock; ?></p>
      </div>
    </div>

    <!-- ✅ ACTION LINKS -->
    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2">
      <a href="warehouse_stock.php"
        class="bg-blue-600 hover:bg-blue-500 text-white text-center py-3 px-4 rounded-lg shadow transition">
        📦 Warehouse Stock
      </a>

      <a href="factory_transfer.php"
        class="bg-blue-600 hover:bg-blue-500 text-white text-center py-3 px-4 rounded-lg shadow transition">
        🏭 Transfer to Factory
      </a>

      <a href="daily_report.php" target="_blank"
        class="bg-blue-600 hover:bg-blue-500 text-white text-center py-3 px-4 rounded-lg shadow transition">
        🧾 Daily Report (PDF)
      </a>

      <a href="monthly_report.php" target="_blank"
        class="bg-blue-600 hover:bg-blue-500 text-white text-center py-3 px-4 rounded-lg shadow transition">
        📆 Monthly Report (PDF)
      </a>

      <a href="includes/logout.php"
        class="bg-red-600 hover:bg-red-500 text-white text-center py-3 px-4 rounded-lg shadow transition col-span-full">
        🔓 Logout
      </a>
    </div>
  </div>

</body>

</html>
