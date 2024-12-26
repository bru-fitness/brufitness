<?php
require_once 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่าข้อมูลครบหรือไม่
    if (!isset($_POST['with_trainer_id'], $_POST['user_id'], $_POST['details'])) {
        die('ข้อมูลไม่ครบถ้วน');
    }

    $with_trainer_id = $_POST['with_trainer_id'];
    $user_id = $_POST['user_id'];
    $details = $_POST['details'];

    // ตรวจสอบว่าค่าที่ได้มาถูกต้องหรือไม่
    echo 'with_trainer_id: ' . htmlspecialchars($with_trainer_id) . '<br>';
    echo 'user_id: ' . htmlspecialchars($user_id) . '<br>';
    echo 'details: ' . htmlspecialchars($details) . '<br>';

    // ถ้าผ่านการตรวจสอบ ก็ทำการอัพเดต
    $sql = "UPDATE service_with_trainer 
            SET details = ? 
            WHERE with_trainer_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $details, $with_trainer_id);

    if ($stmt->execute()) {
        // ถ้าอัปเดตสำเร็จ, เปลี่ยนเส้นทางไปยังหน้า trainer_page.php
        header("Location: trainer_page.php");
        exit(); // สิ้นสุดการทำงานหลังจากการเปลี่ยนเส้นทาง
    } else {
        echo 'ไม่สามารถอัปเดตข้อมูลได้: ' . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
