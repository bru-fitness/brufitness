<?php
require_once 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $with_trainer_id = $_POST['with_trainer_id'];
    $details = $_POST['details'];
    $service_date = $_POST['service_date'];

    // คำสั่ง SQL สำหรับอัปเดตข้อมูล
    $sql = "UPDATE service_with_trainer SET service_date = ?,details = ? WHERE with_trainer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $service_date,$details, $with_trainer_id);

    if ($stmt->execute()) {
        // Redirect back to the page or show success message
        header("Location: your_page.php"); // เปลี่ยน 'your_page.php' เป็นหน้าที่ต้องการกลับไปหลังอัปเดตเสร็จ
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
