<?php
require_once 'dbConnect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT service_usage.*, users.user_id 
            FROM service_usage
            JOIN users ON service_usage.user_id = users.user_id
            WHERE service_usage.service_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode([]);
    }
}
?>
