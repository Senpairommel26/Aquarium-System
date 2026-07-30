<?php
// db_config.php
$servername = "localhost"; // Usually 'localhost' if running on a local machine
$username = "root"; // Your MySQL username (default for XAMPP/WAMP)
$password = ""; // Your MySQL password (default is empty for XAMPP/WAMP)
$dbname = "aquarium_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>