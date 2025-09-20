<?php
$servername = "sql211.infinityfree.com";
$username = "if0_39982936";
$password = "w8thjQpfAhxJ";
$database = "if0_39982936_orderdata";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>