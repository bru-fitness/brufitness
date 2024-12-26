<?php
require_once 'dbConnect.php';


// รับค่า ID
$serviceId = isset($_POST['service_id']) ? $conn->real_escape_string($_POST['service_id']) : null;

if (!$serviceId) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูล ID ที่ต้องการลบ']);
    exit;
}

// ลบข้อมูลจากฐานข้อมูล
$sql_delete = "DELETE FROM service_usage WHERE service_id = '$serviceId'";
if ($conn->query($sql_delete) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลสำเร็จ']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $conn->error]);
}

$conn->close();
?>