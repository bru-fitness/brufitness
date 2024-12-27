<?php
// เริ่มต้น session
session_start();
include('dbConnect.php'); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['userid'])) {
    header('Location: index.php');
    exit();
}
// ดึงข้อมูลเดิมมาแสดงในฟอร์ม
$sql = "SELECT * FROM medical WHERE medical_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$medical_data = $result->fetch_assoc();

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
    ];
}
$user_id = $_SESSION['userid'];
// ตรวจสอบว่ามีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $congenital = $_POST['congenital'];
    $other_congenital = $_POST['other_congenital'];
    $medical_history = $_POST['medical_history'];
    $other_history = $_POST['other_history'];
    $smoke = $_POST['smoke'];
    $alcohol = $_POST['alcohol'];
    $beer = isset($_POST['beer']) ? $_POST['beer'] : null;
    $wine = isset($_POST['wine']) ? $_POST['wine'] : null;
    $spirits = isset($_POST['spirits']) ? $_POST['spirits'] : null;
    $caffeine = $_POST['caffeine'];
    $coffee = isset($_POST['coffee']) ? $_POST['coffee'] : null;
    $tea = isset($_POST['tea']) ? $_POST['tea'] : null;
    $soft_drink = isset($_POST['soft_drink']) ? $_POST['soft_drink'] : null;
    $food = $_POST['food'];
    $other_food = $_POST['other_food'];
    $consumption = $_POST['consumption'];
    $exercise = $_POST['exercise'];
    $medical_savedate = date('Y-m-d H:i:s'); // บันทึกวันที่ปัจจุบัน

    // อัปเดตข้อมูลในฐานข้อมูล
    $sql = "UPDATE medical SET 
            congenital = ?, 
            other_congenital = ?, 
            medical_history = ?, 
            other_history = ?, 
            smoke = ?, 
            alcohol = ?, 
            beer = ?, 
            wine = ?, 
            spirits = ?, 
            caffeine = ?, 
            coffee = ?, 
            tea = ?, 
            soft_drink = ?, 
            food = ?, 
            other_food = ?, 
            consumption = ?, 
            exercise = ?, 
            medical_savedate = ? 
            WHERE medical_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssssssss",
        $congenital,
        $other_congenital,
        $medical_history,
        $other_history,
        $smoke,
        $alcohol,
        $beer,
        $wine,
        $spirits,
        $caffeine,
        $coffee,
        $tea,
        $soft_drink,
        $food,
        $other_food,
        $consumption,
        $exercise,
        $medical_savedate,
        $user_id
    );

    if ($stmt->execute()) {
        header('Location: medical.php?update=success');
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// ดึงข้อมูลเดิมมาแสดงในฟอร์ม
$sql = "SELECT * FROM medical WHERE medical_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$medical_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลทางการแพทย์</title>
</head>

<body>
    <h1>แก้ไขข้อมูลทางการแพทย์</h1>
    <form method="post">
        <label>โรคประจำตัว:</label><br>
        <?php
        // ตัวเลือกโรคประจำตัว
        $congenital_options = [
            '0' => 'ผ่าตัดเส้นเลือดหัวใจ/เปลี่ยนลิ้นหัวใจ',
            '1' => 'หลอดเลือดสมอง',
            '2' => 'ขยายหลอดเลือดหัวใจ',
            '3' => 'เบาหวาน',
            '4' => 'โรคหัวใจ',
            '5' => 'โรคข้ออักเสบ',
            '6' => 'โรคปอด',
            '7' => 'ภูมิแพ้',
            '8' => 'หอบหืด',
        ];

        $congenital_selected = explode(',', $medical_data['congenital']); // แปลงค่าที่เก็บใน DB เป็น array
        ?>

        <!-- แสดงตัวเลือก checkbox -->
        <?php foreach ($congenital_options as $key => $label): ?>
            <label>
                <input type="checkbox" name="congenital[]" value="<?= $key ?>"
                    <?= in_array($key, $congenital_selected) ? 'checked' : '' ?>>
                <?= $label ?>
            </label><br>
        <?php endforeach; ?>

        <!-- ตัวเลือก "อื่นๆ" -->
        <label>
            <input type="checkbox" id="other_checkbox" name="congenital[]" value="other"
                <?= in_array('other', $congenital_selected) ? 'checked' : '' ?>>
            อื่นๆ
        </label><br>

        <!-- แสดงช่องกรอกข้อมูลเมื่อเลือก "อื่นๆ" -->
        <div id="other_congenital_div" style="display: <?= in_array('other', $congenital_selected) ? 'block' : 'none' ?>;">
            <label>โรคประจำตัวอื่นๆ:</label>
            <input type="text" name="other_congenital" value="<?= htmlspecialchars($medical_data['other_congenital']) ?>">
        </div>


        <label>ประวัติการรักษา:</label>
        <textarea name="medical_history"><?= $medical_data['medical_history'] ?></textarea><br>

        <label>ประวัติการรักษาอื่นๆ:</label>
        <textarea name="other_history"><?= $medical_data['other_history'] ?></textarea><br>

        <label>การสูบบุหรี่:</label>
        <input type="text" name="smoke" value="<?= $medical_data['smoke'] ?>"><br>

        <div class="form-group">
            <label>การดื่มแอลกอฮอล์:</label>
            <select>
                <option>ดื่ม</option>
                <option>ไม่ดื่ม</option>
            </select>
            <!-- ตัวเลือกแอลกอฮอล์ -->
            <label>จำนวนเบียร์ต่อวัน:</label>
            <input type="number" name="beer" value="<?= $medical_data['beer'] ?>"><br>

            <label>จำนวนไวน์ต่อวัน:</label>
            <input type="number" name="wine" value="<?= $medical_data['wine'] ?>"><br>

            <label>จำนวนสุราต่อวัน:</label>
            <input type="number" name="spirits" value="<?= $medical_data['spirits'] ?>"><br>
        </div>
        <div class="form-group">
            <label>การดื่มคาเฟอีน:</label>
            <select>
                <option>ดื่ม</option>
                <option>ไม่ดื่ม</option>
            </select>

            <!-- ตัวเลือกคาเฟอีน -->
            <label>จำนวนกาแฟต่อวัน:</label>
            <input type="number" name="coffee" value="<?= $medical_data['coffee'] ?>"><br>

            <label>จำนวนชาต่อวัน:</label>
            <input type="number" name="tea" value="<?= $medical_data['tea'] ?>"><br>

            <label>จำนวนน้ำอัดลมต่อวัน:</label>
            <input type="number" name="soft_drink" value="<?= $medical_data['soft_drink'] ?>"><br>
        </div>

        <label>ลักษณะอาหาร:</label>
        <input type="text" name="food" value="<?= $medical_data['food'] ?>"><br>

        <label>ลักษณะอาหารอื่นๆ:</label>
        <input type="text" name="other_food" value="<?= $medical_data['other_food'] ?>"><br>

        <label>การบริโภค:</label>
        <input type="text" name="consumption" value="<?= $medical_data['consumption'] ?>"><br>

        <label>พฤติกรรมการออกกำลังกาย:</label>
        <input type="text" name="exercise" value="<?= $medical_data['exercise'] ?>"><br>

        <button type="submit">บันทึกการเปลี่ยนแปลง</button>
    </form>
    </div>
    <script>
        document.getElementById('other_checkbox').addEventListener('change', function() {
            const otherDiv = document.getElementById('other_congenital_div');
            if (this.checked) {
                otherDiv.style.display = 'block';
            } else {
                otherDiv.style.display = 'none';
            }
        });
    </script>

</body>

</html>