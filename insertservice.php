<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
// เซฟผู้บันทึกอัตโนมัติ
$record_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
if(!$record_id){
    echo "User ID is not set";
    exit;
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $payment_type_id = $_POST['payment_type_id'];
    $service_fee = $_POST['service_fee'];
    $service_date = $_POST['service_date'];
    $record_id = $_SESSION['userid'] ?? 1; // record ผู้บันทึก

    // ตรวจสอบค่าที่ส่งมาจากฟอร์ม
    if (!isset($user_id, $payment_type_id, $service_fee, $service_date, $record_id)) {
        die('กรุณากรอกข้อมูลให้ครบถ้วน');
    }

    // ตรวจสอบว่า payment_type_id มีในตาราง payment_type หรือไม่
    $sql_check = "SELECT payment_type_id FROM payment_type WHERE payment_type_id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, 's', $payment_type_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) === 0) {
        mysqli_stmt_close($stmt_check);
        mysqli_close($conn);
        die('ไม่พบประเภทการชำระเงินที่เลือก');
    }

    mysqli_stmt_close($stmt_check);

    // เพิ่มข้อมูลเข้าไปในตาราง service_usage
    $sql = "INSERT INTO service_usage (user_id, payment_type_id, service_fee, service_date, record_id, payrate_service) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

// ใช้ service_fee เป็นค่าเดียวกันกับ payrate_service
mysqli_stmt_bind_param($stmt, 'ssssss', $user_id, $payment_type_id, $service_fee, $service_date, $record_id, $service_fee);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: service.php");
    exit(); // เพิ่ม exit() หลังจาก redirect
} else {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    die('เกิดข้อผิดพลาดในการเพิ่มข้อมูล: ' . mysqli_error($conn));
}
}
