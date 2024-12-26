<?php
include('dbConnect.php'); // ไฟล์เชื่อมต่อฐานข้อมูล

session_start(); // เริ่มต้น session
if (!isset($_SESSION['userid'])) {
    header("Location: index.php"); // ส่งกลับไปยังหน้า index.php หรือหน้าที่เหมาะสม
    exit(); // ออกจากการทำงานของสคริปต์
}

$user_id = intval($_SESSION['userid']); // ดึง user_id จาก session

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $with_trainer_id = isset($_POST['with_trainer_id']) ? intval($_POST['with_trainer_id']) : 0;
    $trainer_id = isset($_POST['trainer_id']) ? intval($_POST['trainer_id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if ($with_trainer_id === 0) {
        die("ข้อมูลไม่ครบถ้วน: with_trainer_id ไม่มีค่า");
    }
    echo "ค่าที่ส่งมา: Trainer ID = " . htmlspecialchars($with_trainer_id) . ", User ID = " . htmlspecialchars($user_id) . ", Rating = " . htmlspecialchars($rating) . ", Review = " . htmlspecialchars($review) . "<br>";

    // ตรวจสอบว่ามีข้อมูลตรงกับ trainer_id และ user_id หรือไม่
    $checkSql = "SELECT * FROM service_with_trainer WHERE with_trainer_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $with_trainer_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        die("ไม่พบข้อมูลที่เกี่ยวข้อง: with_trainer_id = " . htmlspecialchars($with_trainer_id));
    }

    echo "พบข้อมูลในระบบ<br>";

    // อัปเดตข้อมูล
     $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $review = isset($_POST['review']) ? trim($_POST['review']) : '';

    $updateSql = "UPDATE service_with_trainer SET rating = ?, review = ? WHERE with_trainer_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("isi", $rating, $review, $with_trainer_id);

    if ($updateStmt->execute()) {
        echo "บันทึกคะแนนสำเร็จ";
        header("Location: service_trainer.php");
        exit();
    } else {
        die("เกิดข้อผิดพลาด: " . $updateStmt->error);
    }

    $stmt->close();
    $conn->close();
}
