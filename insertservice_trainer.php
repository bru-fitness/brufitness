<?php
session_start(); // เริ่มต้นเซสชัน
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_SESSION['userid'])) {
            throw new Exception("User is not logged in.");
        }

        $trainer_id = $_SESSION['userid']; // ใช้ user_id ที่ล็อกอินอยู่เป็น trainer_id
        $user_id = $_POST['user_id'];
        $service_date = $_POST['service_date'];
        $details = $_POST['details'];

        // เพิ่มข้อมูลในตาราง
        $stmt = $conn->prepare("INSERT INTO service_with_trainer (user_id, trainer_id, service_date, details) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $trainer_id, $service_date, $details);
        $stmt->execute();

        $service_id = $conn->insert_id;

        // ใช้ JavaScript เพื่อปิด modal และเปลี่ยนเส้นทางไปยัง trainer_page.php
        echo json_encode(['success' => true, 'service_id' => $service_id]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
