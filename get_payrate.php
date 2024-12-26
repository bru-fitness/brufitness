<?php
// get_payrate.php
include 'dbConnection.php'; // เชื่อมต่อฐานข้อมูล

if (isset($_POST['type']) && isset($_POST['userlevel'])) {
    $type = $_POST['type']; // ประเภทสมาชิก
    $userlevel = $_POST['userlevel']; // ระดับผู้ใช้

    // ดึงข้อมูลจาก payrate ตามเงื่อนไข
    $sql = "SELECT payrate_signup FROM payrate 
            WHERE type = ? AND userlevel = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $type, $userlevel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['payrate_signup' => $row['payrate_signup']]);
    } else {
        echo json_encode(['payrate_signup' => null]); // ส่งค่า null ถ้าไม่พบข้อมูล
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['payrate_signup' => null]);
}
?>
