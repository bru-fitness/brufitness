<!-- บันทึกการแก้ไขข้อมูลส่วนตัวของเทรนเนอร์ -->
<?php
session_start();
include('dbConnect.php');

if (isset($_POST['editUser'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $userlevel = $_POST['userlevel'];
    $address = $_POST['address'];
    $telephone = $_POST['telephone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $status = $_POST['status'];
    $recorddate = $_POST['recorddate'];

    // อัปโหลดรูปภาพ
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $picture = $_FILES['picture']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($picture);

        // ตรวจสอบและย้ายไฟล์
        if (move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
            $query = "UPDATE users SET name=?, surname=?, birthday=?, gender=?, userlevel=?, address=?, telephone=?, username=?, password=?, status=?, recorddate=?, picture=? WHERE user_id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssssssssssssi', $name, $surname, $birthday, $gender, $userlevel, $address, $telephone, $username, $password, $status, $recorddate, $picture, $user_id);
        } else {
            echo "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
            exit;
        }
    } else {
        $query = "UPDATE users SET name=?, surname=?, birthday=?, gender=?, userlevel=?, address=?, telephone=?, username=?, password=?, status=?, recorddate=?, picture=? WHERE user_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssssssssssi', $name, $surname, $birthday, $gender, $userlevel, $address, $telephone, $username, $password, $status, $recorddate, $picture, $user_id);
    }

    if ($stmt->execute()) {
        echo "บันทึกข้อมูลสำเร็จ";
        header("Location: users.php"); // หลังจากบันทึกข้อมูลสำเร็จ ให้กลับไปยังหน้า users.php
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error;
    }
}
?>