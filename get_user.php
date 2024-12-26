<?php
require_once 'dbConnect.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    error_log("Received user_id: " . $user_id); // เพิ่มการดีบัก

    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        error_log("Database user_id: " . $row['user_id']); // เพิ่มการดีบัก
        echo json_encode(['status' => 'success', 'user' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบผู้ใช้']);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User ID not provided']);
}

mysqli_close($conn);
?>
