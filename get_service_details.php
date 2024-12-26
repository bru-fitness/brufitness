<?php
require_once 'dbConnect.php';

if (isset($_GET['with_trainer_id'])) {
    $with_trainer_id = $_GET['with_trainer_id'];

    // คำสั่ง SQL ดึงข้อมูล details
    $sql = "SELECT details FROM service_with_trainer WHERE with_trainer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $with_trainer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['details' => $row['details']]);
    } else {
        echo json_encode(['details' => null]);
    }

    $stmt->close();
}
$conn->close();
?>
