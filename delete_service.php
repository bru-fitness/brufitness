<?php
session_start(); // เริ่มต้นเซสชัน
include 'dbConnect.php';


// ตั้งค่าการรับข้อมูล JSON
header('Content-Type: application/json');

// รับข้อมูลจากการร้องขอ
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่ามีข้อมูลที่ต้องการลบ
if (!isset($data['with_trainer_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการลบ']);
    exit;
}

$with_trainer_id = $conn->real_escape_string($data['with_trainer_id']);

// สร้างคำสั่ง SQL เพื่อลบข้อมูล
$sql = "DELETE FROM service_with_trainer WHERE with_trainer_id = '$with_trainer_id'";

// ดำเนินการลบข้อมูล
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'ลบข้อมูลสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $conn->error]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

