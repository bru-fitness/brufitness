<?php 
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    include('dbConnect.php'); // เรียกใช้ไฟล์ db.php เพื่อเชื่อมต่อฐานข้อมูล

    $username = $_POST['username'];
    $password = $_POST['password'];

    // เตรียมคำสั่ง SQL เพื่อลดความเสี่ยงในการถูกโจมตีด้วย SQL Injection
    $query = "SELECT user_id, username, password, name, surname, userlevel FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // ตรวจสอบผลลัพธ์จากการ query
    if ($user) {
        // ตรวจสอบรหัสผ่าน
        if ($user['password'] === $password) {
            $_SESSION['userid'] = $user['user_id'];
            $_SESSION['user'] = $user['name'] . " " . $user['surname'];
            $_SESSION['userlevel'] = $user['userlevel'];

            // เปลี่ยนหน้าโดยอิง userlevel
            switch ($_SESSION['userlevel']) {
                case '0':
                    header("Location: admin_page.php");
                    break;
                case '1':
                    header("Location: report_summary.php");
                    break;
                case '2':
                    header("Location: service_ulist.php");
                    break;
                case '3':
                    header("Location: user_page.php");
                    break;
                case '4':
                    header("Location: general_page.php");
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = "รหัสผ่านไม่ถูกต้อง";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "ชื่อผู้ใช้ไม่ถูกต้อง";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
