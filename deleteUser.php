<?php
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

// รับค่าจากคำร้องขอ
$user_id = isset($_POST['user_id']) ? $conn->real_escape_string($_POST['user_id']) : null;

// ตรวจสอบว่ามี user_id หรือไม่
if (!$user_id) {
    echo json_encode(['status' => 'warning', 'message' => 'ไม่พบข้อมูล user_id ที่ต้องการลบ']);
    exit;
}

// ตรวจสอบสิทธิ์ก่อนลบ (ถ้าจำเป็น)
session_start();
if (!isset($_SESSION['userlevel']) || $_SESSION['userlevel'] !== '0') {
    echo json_encode(['status' => 'warning', 'message' => 'คุณไม่มีสิทธิ์ลบผู้ใช้งาน']);
    exit;
}

// ตรวจสอบว่าผู้ใช้งานนี้สามารถลบได้หรือไม่
$sql_check = "SELECT * FROM users WHERE user_id = '$user_id'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    echo json_encode(['status' => 'warning', 'message' => 'ไม่พบผู้ใช้งานในระบบ']);
    exit;
}

// สร้างคำสั่ง SQL เพื่อลบข้อมูล
$sql_delete = "DELETE FROM users WHERE user_id = '$user_id'";

// ดำเนินการลบข้อมูล
if ($conn->query($sql_delete) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลเรียบร้อยแล้ว']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $conn->error]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>