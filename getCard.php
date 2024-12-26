<?php
// รับค่า user_id ที่ถูกส่งมาจาก AJAX
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // สร้างการเชื่อมต่อกับฐานข้อมูล
    $conn = new mysqli('localhost', 'username', 'password', 'database');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ดึงข้อมูลภาพบัตรสมาชิกจากตาราง users
    $sql = "SELECT cardImage FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cardImagePath = $row['cardImage'];

        // ตรวจสอบว่ามีไฟล์บัตรสมาชิกจริงหรือไม่
        if (file_exists($cardImagePath)) {
            echo json_encode(['cardImage' => $cardImagePath]);
        } else {
            echo json_encode(['cardImage' => false]);
        }
    } else {
        echo json_encode(['cardImage' => false]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['cardImage' => false]);
}
?>
