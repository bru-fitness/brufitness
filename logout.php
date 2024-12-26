<?php
session_start(); // เริ่ม session
session_unset(); // ยกเลิก session
session_destroy(); // ทำลาย session
header("Location: index.php"); // เปลี่ยนเส้นทางไปยังหน้า login.php
exit();
?>
