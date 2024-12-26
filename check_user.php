<?php
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = trim($_POST['user_id']);

    // ตรวจสอบ user_id ในฐานข้อมูล
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // ส่งผลลัพธ์กลับในรูปแบบ JSON
    echo json_encode(['exists' => $row['count'] > 0]);
    $stmt->close();
    $conn->close();
}
?>
