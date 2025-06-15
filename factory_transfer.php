<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit();
}

include 'includes/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $factory_name = $_POST['factory_name'];
    $quantity_sent = $_POST['quantity_sent'];
    $transfer_date = $_POST['transfer_date'];

    $stmt = $conn->prepare("INSERT INTO factory_transfer (factory_name, quantity_sent, transfer_date) VALUES (?, ?, ?)");

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("sis", $factory_name, $quantity_sent, $transfer_date);

    if ($stmt->execute()) {
        $msg = "✅ Stock transferred to factory successfully!";
    } else {
        $msg = "❌ Error transferring stock: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Factory Transfer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>
<body class="bg-blue-900 min-h-screen flex items-center justify-center p-4">

  <div class="bg-blue-800 text-white w-full max-w-xl rounded-2xl shadow-2xl p-8 space-y-6">
    <h2 class="text-2xl font-bold text-center">🏭 Transfer Stock to Factory</h2>

    <?php if (!empty($msg)): ?>
      <div class="bg-blue-600 text-white text-center py-2 px-4 rounded">
        <?php echo $msg; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1">Factory Name:</label>
        <input type="text" name="factory_name" required
          class="w-full px-4 py-3 rounded bg-blue-700 placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white" />
      </div>

      <div>
        <label class="block mb-1">Quantity Sent:</label>
        <input type="number" name="quantity_sent" required
          class="w-full px-4 py-3 rounded bg-blue-700 placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white" />
      </div>

      <div>
        <label class="block mb-1">Transfer Date:</label>
        <input type="date" name="transfer_date" required
          class="w-full px-4 py-3 rounded bg-blue-700 text-white focus:outline-none focus:ring-2 focus:ring-white" />
      </div>

      <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-lg font-semibold transition">
        ➡️ Transfer Stock
      </button>
    </form>

    <div class="flex flex-col sm:flex-row justify-center gap-4 pt-6">
      <a href="dashboard.php"
         class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-lg text-center transition shadow">
        ⬅️ Back to Dashboard
      </a>

      <a href="includes/logout.php"
         class="bg-red-600 hover:bg-red-500 text-white px-6 py-2 rounded-lg text-center transition shadow">
        🔒 Logout
      </a>
    </div>
  </div>

</body>
</html>
