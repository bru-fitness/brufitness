<?php
require_once 'dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form submission
    $service_id = $_POST['service_id'];
    $payment_type_id = $_POST['payment_type_id'];
    $service_fee = $_POST['service_fee'];
    $service_date = $_POST['service_date'];
    // $other_payment_type = !empty($_POST['other_payment_type']) ? $_POST['other_payment_type'] : null;

    // Prepare the SQL query for updating the service data
    $sql = "UPDATE service_usage 
    SET payment_type_id = ?, 
        service_fee = ?, 
        service_date = ? 
    WHERE service_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $payment_type_id, $service_fee, $service_date, $service_id);
    // $other_payment_type,

    // Execute the statement and check if successful
    if ($stmt->execute()) {
        header("Location: service.php?update=success");
        exit;
    } else {
        echo "การแก้ไขข้อมูลล้มเหลว: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "ไม่พบข้อมูลในการแก้ไข";
}
