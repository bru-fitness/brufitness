<?php
// เริ่มต้น session
session_start();
include('dbConnect.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['userid'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['userid'];

// ดึงข้อมูลทางการแพทย์ของผู้ใช้
$sql = "SELECT * FROM medical WHERE medical_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$medical_data = $result->fetch_assoc();

// ดึงชื่อและนามสกุลของผู้ใช้จากตาราง users
$sql = "SELECT name, surname FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// สร้าง $fullname
$fullname = $user_data ? $user_data['name'] . " " . $user_data['surname'] : "ไม่พบข้อมูล";

// หากไม่มีข้อมูลในฐานข้อมูล ให้กำหนดค่าเริ่มต้นเป็นค่าว่าง
if (!$medical_data) {
    $medical_data = [
        'congenital' => '',
        'other_congenital' => '',
        'medical_history' => '',
        'other_history' => '',
        'smoke' => '',
        'alcohol' => '',
        'beer' => '',
        'wine' => '',
        'spirits' => '',
        'caffeine' => '',
        'coffee' => '',
        'tea' => '',
        'soft_drink' => '',
        'food' => '',
        'other_food' => '',
        'consumption' => '',
        'exercise' => '',
        'medical_savedate' => '',
        'record_id' => $user_id,
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลทางการแพทย์</title>
    <link rel="stylesheet" href="css/medical.css" />

</head>

<body>
    <form class="medical-form">
        <h1>ข้อมูลทางการแพทย์</h1>
        <div class="form-group inline">
            <div class="form-group">
                <label>รหัสผู้ใช้:</label>
                <input type="text" value="<?= $user_id ?>" disabled><br>
            </div>
            <div class="form-group">
                <label>ชื่อ-สกุล:</label>
                <input type="text" value="<?= $fullname ?>" disabled><br>
            </div>
        </div>
        <div class="form-group">
            <label>โรคประจำตัว:</label>
            <input type="text" value="<?= $medical_data['congenital'] ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>ประวัติการรักษา:</label>
            <textarea disabled><?= $medical_data['medical_history'] ?></textarea><br>
        </div>

        <div class="form-group inline">
            <div class="form-group">
                <label>การสูบบุหรี่:</label>
                <select>
                    <option>สูบบุหรี่ประจำ</option>
                    <option>สูบบ่อย</option>
                    <option>สูบนานๆ</option>
                    <option>ไม่สูบเลย</option>
                </select>
            </div>


            <div class="form-group">
                <label>การดื่มแอลกอฮอล์:</label>
                <select>
                    <option>ดื่ม</option>
                    <option>ไม่ดื่ม</option>
                </select>
                <!-- ตัวอย่างกรณีดื่มแอลกอฮอล์ -->
                <?php if ($medical_data['alcohol'] === 'ดื่ม'): ?>
                    <label>จำนวนเบียร์ต่อวัน:</label>
                    <input type="text" value="<?= $medical_data['beer'] ?>" disabled><br>

                    <label>จำนวนไวน์ต่อวัน:</label>
                    <input type="text" value="<?= $medical_data['wine'] ?>" disabled><br>

                    <label>จำนวนสุราต่อวัน:</label>
                    <input type="text" value="<?= $medical_data['spirits'] ?>" disabled><br>
                <?php endif; ?>
            </div>



            <div class="form-group">
                <label>การดื่มคาเฟอีน:</label>
                <select>
                    <option>ดื่ม</option>
                    <option>ไม่ดื่ม</option>
                </select>
                <!-- ตัวอย่างกรณีดื่มคาเฟอีน -->
                <?php if ($medical_data['caffeine'] === 'ดื่ม'): ?>
                    <label>จำนวนกาแฟต่อวัน:</label>
                    <input type="text" value="<?= $medical_data['coffee'] ?>" disabled><br>

                    <label>จำนวนชาต่อวัน:</label>
                    <input type="text" value="<?= $medical_data['tea'] ?>" disabled><br>

                    <label>จำนวนน้ำอัดลมต่อวัน:</label>
                    <input type="text" value="<?= $medical_data['soft_drink'] ?>" disabled><br>
                <?php endif; ?>
            </div>
        </div>

        <label>ลักษณะอาหาร:</label>
        <input type="text" value="<?= $medical_data['food'] ?>" disabled><br>

        <label>การบริโภค:</label>
        <input type="text" value="<?= $medical_data['consumption'] ?>" disabled><br>

        <label>พฤติกรรมการออกกำลังกาย:</label>
        <input type="text" value="<?= $medical_data['exercise'] ?>" disabled><br>

        <label>วันที่บันทึกข้อมูล:</label>
        <input type="text" value="<?= $medical_data['medical_savedate'] ?>" disabled><br>

        <label>รหัสผู้บันทึกข้อมูล:</label>
        <input type="text" value="<?= $medical_data['record_id'] ?>" disabled><br>

        <a href="medical_edit.php">แก้ไขข้อมูล</a>
    </form>
</body>

</html>