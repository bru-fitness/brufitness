<?php
session_start();
require_once 'dbConnect.php';

// ตรวจสอบว่ามีการส่งค่า service_id มาหรือไม่
if (isset($_POST['with_trainer_id'])) {
    $with_trainer_id = $_POST['with_trainer_id'];
    echo "Received with_trainer_id: " . $with_trainer_id;
    // สร้างคำสั่ง SQL เพื่อดึงข้อมูลความคิดเห็น
    $sql = "SELECT details FROM service_with_trainer WHERE with_trainer_id = ?";
    
    // เตรียม statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $with_trainer_id);
    $stmt->execute();
    
    // เก็บผลลัพธ์
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = array('details' => $row['details']);
        echo json_encode($response);
    } else {
        // กรณีที่ไม่มีข้อมูลความคิดเห็น
        echo json_encode(array('details' => 'ไม่มีข้อมูลความคิดเห็น'));
    }

    // ปิด statement และการเชื่อมต่อฐานข้อมูล
    $stmt->close();
    mysqli_close($conn);
} else {
    // กรณีที่ไม่มีการส่งค่า service_id มา
    echo json_encode(array('error' => 'ไม่มีการระบุ ID ของการบริการ'));
}
?>
