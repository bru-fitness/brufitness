<?php
session_start();
if(isset($_SESSION['username'])) {
    // คำสั่งที่ต้องการทำเมื่อตัวแปร $_SESSION['your_variable'] มีการตั้งค่า
    $value = $_SESSION['username'];
} else {
    // คำสั่งที่ต้องการทำเมื่อตัวแปร $_SESSION['your_variable'] ไม่มีการตั้งค่า
    $value = ''; // หรือค่าเริ่มต้นที่เหมาะสม
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลทางการแพทย์</title>
</head>
<body>
    <div>
        <h2>ข้อมูลทางการแพทย์ ของ <?php echo $_SESSION['username']; ?></h2>
        <a  href="users.php">กลับหน้าแรก</a>

    </div>
</body>
</html>
