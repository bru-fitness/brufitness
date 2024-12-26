<?php
require_once 'dbConnect.php';

if (isset($_POST['username']) && !empty($_POST['username'])) {
    $username = trim($_POST['username']);

    $sql_check = "SELECT COUNT(*) as count FROM users WHERE username = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);

    if ($stmt_check) {
        mysqli_stmt_bind_param($stmt_check, 's', $username);
        mysqli_stmt_execute($stmt_check);

        $result = mysqli_stmt_get_result($stmt_check);
        $row = mysqli_fetch_assoc($result);

        if ($row['count'] > 0) {
            echo "ชื่อผู้ใช้นี้มีอยู่แล้ว";
        } else {
            echo "";
        }

        mysqli_stmt_close($stmt_check);
    } else {
        echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL";
    }
} else {
    echo "โปรดกรอกชื่อผู้ใช้";
}

mysqli_close($conn);
?>
