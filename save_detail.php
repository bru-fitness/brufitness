<?php
include('dbConnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = $_POST['service_id'];
    $with_trainer_detail = $_POST['with_trainer_detail'];

    $query = "UPDATE service_with_trainer SET with_trainer_detail=? WHERE service_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $with_trainer_detail, $service_id);

    if ($stmt->execute()) {
        echo "บันทึกความคิดเห็นเรียบร้อยแล้ว";
        header("Location: service_trainer.php"); // หลังบันทึกเสร็จกลับไปที่หน้าเดิม
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
    $stmt->close();
}
?>
