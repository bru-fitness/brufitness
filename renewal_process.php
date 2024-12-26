<?php
date_default_timezone_set('Asia/Bangkok');
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล


// ถ้ารับค่าผ่าน POST
if (isset($_POST['user_id'])) {
    $_SESSION['user_id'] = $_POST['user_id'];
}

// รับข้อมูลจากฟอร์ม
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0; // ตรวจสอบว่า user_id เป็นตัวเลข
$renewal_fee = isset($_POST['renewal_fee']) ? floatval($_POST['renewal_fee']) : 0.0; // ตรวจสอบว่า renewal_fee เป็นตัวเลข
$payrate_renewal = $renewal_fee; // กำหนด payrate_renewal ให้เท่ากับ renewal_fee
$renewal_date = date('Y-m-d H:i:s'); // วันที่ปัจจุบัน
$record_id = $_SESSION['userid'] ?? 1; // record ผู้บันทึก

// ตรวจสอบค่าที่ได้รับ
var_dump($user_id);
var_dump($renewal_fee);
var_dump($record_id);

// ตรวจสอบข้อมูลก่อนเพิ่ม
if ($user_id > 0 && $renewal_fee >= 0 && $record_id > 0) {
    // เริ่มต้นการทำธุรกรรม (Transaction)
    $conn->begin_transaction();
    try {
        // เพิ่มข้อมูลในตาราง renewals
        $sql_insert = "INSERT INTO renewals (renewal_date, user_id, renewal_fee, payrate_renewal, record_id) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("siddi", $renewal_date, $user_id, $renewal_fee, $payrate_renewal, $record_id);
        $stmt_insert->execute();
        $stmt_insert->close();

        // อัปเดต recorddate ในตาราง users
        $sql_update = "UPDATE users SET recorddate = ? WHERE user_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $renewal_date, $user_id);
        $stmt_update->execute();
        $stmt_update->close();

        // ยืนยันการทำธุรกรรม
        $conn->commit();
        echo "เพิ่มข้อมูลสำเร็จและอัปเดต recorddate สำเร็จ";
    } catch (Exception $e) {
        // ยกเลิกการทำธุรกรรมหากเกิดข้อผิดพลาด
        $conn->rollback();
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // ดึงข้อมูลสมาชิก
    $sql_user = "SELECT * FROM users WHERE user_id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user_data = $result_user->fetch_assoc();

    // เส้นทางบัตรสมาชิก
    $card_image_path = 'cards/' . $user_id . '.png';

    // สร้างบัตรสมาชิกใหม่ (ทับไฟล์เดิม)
    createMemberCard(
        $user_data['user_id'],
        $user_data['name'],
        $user_data['surname'],
        $renewal_date = date('d-m-Y'),
        $expiryDate = date('d-m-Y', strtotime('+2 years')),  // วันหมดอายุ
        'assets/images/logo/logo.png',         // โลโก้หลัก
        'assets/images/logo/exercise.png',     // ภาพออกกำลังกาย
        $card_image_path           // เส้นทางไฟล์บัตร
    );

    // อัพเดทเส้นทางรูปบัตรสมาชิกในตาราง users
    $sql_update_image = "UPDATE users SET cardImage = ? WHERE user_id = ?";
    $stmt_update_image = $conn->prepare($sql_update_image);
    $stmt_update_image->bind_param("si", $card_image_path, $user_id);
    $stmt_update_image->execute();

    // ไปที่หน้าบัตรสมาชิก
    header("Location: report_renewal.php");
    exit();
} else {
    echo "ข้อมูลไม่ถูกต้อง";
}

// ปิดการเชื่อมต่อฐานข้อมูล
if (isset($stmt_user)) {
    $stmt_user->close();
}
$conn->close();


// ฟังก์ชันสร้างบัตรสมาชิก
function createMemberCard($user_id, $name, $surname, $recorddate, $expiryDate, $logo_path, $exercise_image_path, $output_path)
{
    $card_width = 450;
    $card_height = 250;
    $card = imagecreatetruecolor($card_width, $card_height);

    // สีพื้นหลังและข้อความ
    $background_color = imagecolorallocate($card, 219, 255, 200); // สีเขียวอ่อน
    $black = imagecolorallocate($card, 0, 0, 0);
    imagefill($card, 0, 0, $background_color);

    // โหลดโลโก้
    if (!file_exists($logo_path)) {
        die("Error: Logo file not found.");
    }
    $logo = imagecreatefrompng($logo_path);
    $logo_width = 100;
    $logo_height = 60;
    $logo = imagescale($logo, $logo_width, $logo_height);
    imagecopy($card, $logo, 30, 20, 0, 0, $logo_width, $logo_height);

    // โหลดฟอนต์
    $font_path = dirname(__FILE__) . '/fonts/Pridi-Bold.ttf';
    if (!file_exists($font_path)) {
        die("Error: Font file not found.");
    }

    // ข้อความบนบัตร
    $title = "BRU FITNESS";
    imagettftext($card, 25, 0, 150, 50, $black, $font_path, $title);

    imagettftext($card, 14, 0, 30, 100, $black, $font_path, "รหัสสมาชิก: $user_id");
    imagettftext($card, 14, 0, 30, 130, $black, $font_path, "ชื่อ-นามสกุล: $name $surname");
    imagettftext($card, 14, 0, 30, 160, $black, $font_path, "วันที่สมัคร: $recorddate");
    imagettftext($card, 14, 0, 30, 190, $black, $font_path, "วันหมดอายุ: $expiryDate");

    // โหลดและใส่รูปภาพออกกำลังกาย
    if (!file_exists($exercise_image_path)) {
        die("Error: Exercise image file not found.");
    }
    $exercise_image = imagecreatefrompng($exercise_image_path);
    $exercise_width = 200;
    $exercise_height = 220;
    $exercise_image = imagescale($exercise_image, $exercise_width, $exercise_height);
    imagecopy($card, $exercise_image, 270, 80, 0, 0, $exercise_width, $exercise_height);

    // บันทึกบัตรสมาชิก
    if (!imagepng($card, $output_path)) {
        die("Error: Failed to save the card image.");
    }

    // ทำลายหน่วยความจำ
    imagedestroy($card);
    imagedestroy($logo);
    imagedestroy($exercise_image);
}
