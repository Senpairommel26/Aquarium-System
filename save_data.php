<?php
// save_data.php
require_once 'db_config.php';

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// Validate incoming data
$temperature = isset($_POST['temperature']) ? floatval($_POST['temperature']) : null;
$ph_level    = isset($_POST['ph_level']) ? floatval($_POST['ph_level']) : null;
$turbidity   = isset($_POST['turbidity']) ? floatval($_POST['turbidity']) : null;

if ($temperature === null || $ph_level === null || $turbidity === null) {
    http_response_code(400);
    echo "Missing data";
    exit;
}

/* =========================
   INSERT SENSOR READINGS
   ========================= */
$stmt = $conn->prepare(
    "INSERT INTO sensorreadings (temperature, ph_level, turbidity)
     VALUES (?, ?, ?)"
);

$stmt->bind_param("ddd", $temperature, $ph_level, $turbidity);

if (!$stmt->execute()) {
    http_response_code(500);
    echo "Failed to insert sensor data";
    exit;
}

$stmt->close();

/* =========================
   ALERT LOGIC (AUTO)
   ========================= */

// pH alerts
if ($ph_level < 6.5 || $ph_level > 7.5) {
    $msg = "pH level out of range";
    insertAlert($conn, "pH", $ph_level, $msg);
}

// Temperature alerts
if ($temperature < 24.0 || $temperature > 28.0) {
    $msg = "Temperature out of safe range";
    insertAlert($conn, "Temperature", $temperature, $msg);
}

// Turbidity alerts
if ($turbidity > 25) {
    $msg = "High turbidity detected";
    insertAlert($conn, "Turbidity", $turbidity, $msg);
}

echo "Data saved successfully";
$conn->close();

/* =========================
   ALERT FUNCTION
   ========================= */
function insertAlert($conn, $type, $value, $message) {
    $stmt = $conn->prepare(
        "INSERT INTO alerts (parameter_type, recorded_value, alert_message)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sds", $type, $value, $message);
    $stmt->execute();
    $stmt->close();
}
?>
