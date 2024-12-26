<?php
require_once 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['service_id'];
    $service_fee = $_POST['service_fee'];
    $service_date = $_POST['service_date'];

    $sql = "UPDATE service_usage SET service_fee = ?, service_date = ? WHERE service_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $service_fee, $service_date, $id);

    if ($stmt->execute()) {
        header('Location: your_page.php'); // เปลี่ยนเส้นทางไปหน้าที่เหมาะสม
    } else {
        echo 'Error updating record: ' . $conn->error;
    }
}
?>
