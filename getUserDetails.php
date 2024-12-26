<?php
require_once 'dbConnect.php';

$response = array("success" => false, "error" => "", "user" => array());

if (isset($_GET["user_id"])) {
    $user_id = $_GET["user_id"];
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $response["success"] = true;
        $response["user"] = $result->fetch_assoc();
    } else {
        $response["error"] = "ไม่พบข้อมูลผู้ใช้";
    }
    $stmt->close();
} else {
    $response["error"] = "ไม่พบ ID ผู้ใช้";
}

echo json_encode($response);
?>
