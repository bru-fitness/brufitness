<?php
// Include database connection file
include 'dbConnect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $users_id = $_POST['users_id'];
    $payrate_id = $_POST['payrate_id'];
    $payment_type_id = $_POST['payment_type_id'];
    $service_fee = $_POST['service_fee'];
    $service_usage_date = $_POST['service_usage_date'];
    $record_id = $_POST['record_id'];

    // Insert data into database
    $sql = "INSERT INTO service_usage (users_id, payrate_id, payment_type_id, service_fee, service_usage_date, record_id) 
            VALUES ('$users_id', '$payrate_id', '$payment_type_id', '$service_fee', '$service_usage_date', '$record_id')";

    if (mysqli_query($conn, $sql)) {
        echo "บันทึกข้อมูลการเข้าใช้บริการเรียบร้อยแล้ว";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close database connection
    mysqli_close($conn);
}
?>
