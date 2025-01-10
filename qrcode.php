<?php
// Database connection details
$servername = "localhost";
$username = "smartitltd_container_user";
$password = "{a]-r05%i(Fd"; // Replace with your database password
$dbname = "smartitltd_esp32_qrdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read incoming data
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['device_id']) && isset($data['qr_code_data'])) {
    $device_id = $data['device_id'];
    $qr_code_data = $data['qr_code_data'];
    $current_date = date('Y-m-d'); // Only date, no time

    // Check if the data already exists in the database
    $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM qr_codes WHERE device_id = ? AND qr_code_data = ? AND DATE(date) = ?");
    $checkStmt->bind_param("sss", $device_id, $qr_code_data, $current_date);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Data already exists
        echo json_encode(["success" => false, "message" => "Data already exists"]);
    } else {
        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO qr_codes (device_id, qr_code_data, date) VALUES (?, ?, ?)");
        $current_datetime = date('Y-m-d H:i:s'); // Current date and time
        $stmt->bind_param("sss", $device_id, $qr_code_data, $current_datetime);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Data inserted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error inserting data"]);
        }
        $stmt->close();
    }
    $checkStmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}

$conn->close();
?>
