<?php
session_start();
require_once 'dbConnect.php';

$response = array("success" => false, "error" => "");

// ฟังก์ชันสร้างรหัสผู้ใช้
function generateUserId($userlevel, $type, $conn)
{
    $prefix = $userlevel . $type;

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id LIKE ? ORDER BY user_id DESC LIMIT 1");
    if (!$stmt) {
        error_log("Error preparing statement: " . $conn->error);
        return false;
    }

    $like = $prefix . '%';
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $last_id = (int)substr($row['user_id'], strlen($prefix));
        $new_id = $last_id + 1;
        return $prefix . str_pad($new_id, 3, '0', STR_PAD_LEFT);
    } else {
        return $prefix . '001';
    }
}

// ฟังก์ชันตรวจสอบข้อมูลที่ POST มา
function validatePostData($data)
{
    $errors = [];

    if (empty($data['name'])) $errors[] = 'กรอกชื่อ';
    if (empty($data['surname'])) $errors[] = 'กรอกนามสกุล';
    if (!isset($data['gender'])) $errors[] = 'เลือกเพศ';
    if (empty($data['username'])) $errors[] = 'ตั้งชื่อผู้ใช้';
    if (empty($data['password'])) $errors[] = 'กรอกรหัสผ่าน';
    if (strlen($data['password']) > 20 || strlen($data['password']) < 5) {
        $errors[] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';
    }
    if ($data['password'] !== $data['c_password']) {
        $errors[] = 'รหัสผ่านไม่ตรงกัน';
    }

    return $errors;
}

// การทำงานเมื่อรับ POST Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = validatePostData($_POST);
    if (!empty($errors)) {
        $response['error'] = implode(', ', $errors);
        echo json_encode($response);
        exit();
    }

    // รับข้อมูลจาก POST
    $user_id = generateUserId($_POST["userlevel"], $_POST["type"], $conn);
    if (!$user_id) {
        $response["error"] = "ไม่สามารถสร้างรหัสผู้ใช้ได้";
        echo json_encode($response);
        exit();
    }

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $gender = $_POST["gender"];
    $userlevel = $_POST["userlevel"];
    $type = $_POST['type'];
    $birthday = $_POST["dob"];
    $address = $_POST["address"];
    $telephone = $_POST["phone"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $c_password = $_POST["c_password"];
    $recorddate = date('Y-m-d H:i');
    $expiryDate = date('d-m-Y', strtotime('+2 years'));  // วันหมดอายุ

    // ตรวจสอบว่าชื่อผู้ใช้ซ้ำหรือไม่
    $sql = "SELECT COUNT(*) AS count FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // ส่งสถานะว่าชื่อผู้ใช้ซ้ำกลับไป
        echo json_encode(['status' => 'error', 'message' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว']);
        exit;
    }
    function createMemberCard($user_id, $name, $surname, $recorddate, $expiryDate, $logo_path, $exercise_image_path)
    {
        $card_width = 450;
        $card_height = 250;
        $card = imagecreatetruecolor($card_width, $card_height);

        // สร้างสี
        $background_color = imagecolorallocate($card, 219, 255, 200); // สีเขียวอ่อน
        $black = imagecolorallocate($card, 255, 255, 255);

        // ตรวจสอบไฟล์โลโก้และภาพออกกำลังกาย
        if (!file_exists($logo_path) || !file_exists($exercise_image_path)) {
            die("Error: Logo or exercise image file not found.");
        }

        // เพิ่มโลโก้
        $logo = imagecreatefrompng($logo_path); // โหลดไฟล์โลโก้
        $logo_width = 100;
        $logo_height = 60;
        $logo = imagescale($logo, $logo_width, $logo_height);
        imagecopy($card, $logo, 30, 20, 0, 0, $logo_width, $logo_height);

        // เพิ่มข้อความ "บัตรสมาชิก"
        $font_path = dirname(__FILE__) . '/fonts/Pridi-Bold.ttf';
        if (!file_exists($font_path)) {
            die("Error: Font file not found.");
        }
        $title = "BRU FITNESS";
        $font_size_title = 25;
        $title_box = imagettfbbox($font_size_title, 0, $font_path, $title);
        $title_width = $title_box[2] - $title_box[0];
        $title_x = ($card_width - $title_width) / 2;
        $title_y = 50;
        imagettftext($card, $font_size_title, 0, $title_x, $title_y, $black, $font_path, $title);

        // ข้อมูลสมาชิก
        $font_size = 14;
        $text_x = 30;
        $text_y_base = 100;

        imagettftext($card, $font_size, 0, $text_x, $text_y_base, $black, $font_path, "รหัสสมาชิก: $user_id");
        imagettftext($card, $font_size, 0, $text_x, $text_y_base + 25, $black, $font_path, "ชื่อ-นามสกุล: $name $surname");
        imagettftext($card, $font_size, 0, $text_x, $text_y_base + 50, $black, $font_path, "วันที่สมัคร: $recorddate");
        imagettftext($card, $font_size, 0, $text_x, $text_y_base + 75, $black, $font_path, "วันหมดอายุ: $expiryDate");

        // เพิ่มรูปภาพ exercise ทางด้านขวา
        $exercise_image = imagecreatefrompng($exercise_image_path);
        $exercise_width = 200;
        $exercise_height = 220;
        $exercise_image = imagescale($exercise_image, $exercise_width, $exercise_height);
        imagecopy($card, $exercise_image, $card_width - $exercise_width - 20, ($card_height - $exercise_height) / 2, 0, 0, $exercise_width, $exercise_height);

        // กำหนดเส้นทางไฟล์ผลลัพธ์
        $output_path = 'cards/' . $user_id . '.png';

        // ตรวจสอบเส้นทางบันทึกไฟล์
        if (!imagepng($card, $output_path)) {
            die("Error: Failed to save the image to $output_path.");
        }
        // บันทึกบัตรสมาชิก
        imagepng($card, $output_path);
        imagedestroy($card);
        imagedestroy($logo);
        imagedestroy($exercise_image);

        return file_exists($output_path);  // ตรวจสอบว่าไฟล์บัตรสมาชิกถูกสร้างขึ้นหรือไม่
    }

    // เริ่มต้น Transaction
    try {
        $conn->begin_transaction();
        // กำหนดวันหมดอายุและเส้นทางไฟล์
        $expiryDate = date('d-m-Y', strtotime('+2 years'));
        $logo_path = 'assets/images/logo/logo.png';
        $exercise_image_path = 'assets/images/logo/exercise.png';
        // ตรวจสอบค่าสมัคร
        if (!isset($_POST['signup_fee']) || !is_numeric($_POST['signup_fee']) || $_POST['signup_fee'] <= 0) {
            throw new Exception("กรุณาระบุค่าสมัครที่ถูกต้อง");
        }
        // ตรวจสอบไฟล์โลโก้และภาพออกกำลังกาย
        if (!file_exists($logo_path) || !file_exists($exercise_image_path)) {
            throw new Exception("ไฟล์โลโก้หรือภาพออกกำลังกายไม่ถูกต้อง");
        }
        // สร้างบัตรสมาชิก
        $output_path = 'cards/' . $user_id . '.png';
        $result = createMemberCard($user_id, $name, $surname, $recorddate, $expiryDate, $logo_path, $exercise_image_path);
        if (!$result) {
            throw new Exception("Failed to create member card.");
        }
        $cardImage = $output_path;

        if (!createMemberCard($user_id, $name, $surname, $recorddate, $expiryDate, $logo_path, $exercise_image_path)) {
            throw new Exception("ไม่สามารถสร้างบัตรสมาชิกได้");
        }
        // เตรียมข้อมูลสำหรับการเพิ่มข้อมูลใน users
        $sql = "INSERT INTO users (user_id, name, surname, birthday, gender, userlevel, type, address, telephone, username, password, recorddate, status, cardImage, record_id) 
 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed for users: " . $conn->error);
        }

        $status = '0'; // สมาชิก
        $record_id = $_SESSION['userid'] ?? 1; // record ผู้บันทึก
        $stmt->bind_param(
            "sssssssssssssss",  $user_id,  $name, $surname,  $birthday,  $gender, $userlevel,  $type, $address, $telephone,
            $username,  $password,  $recorddate, $status, $cardImage,  $record_id );

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert into users: " . $stmt->error);
        }

        // ปิด Statement หลังจาก execute เสร็จ
        $stmt->close();

        // เตรียมข้อมูลสำหรับการเพิ่มข้อมูลใน signup
        $signup_fee = $_POST['signup_fee']; // รับค่าค่าสมัคร
        if (!is_numeric($signup_fee) || $signup_fee <= 0) {
            throw new Exception("Invalid signup fee.");
        }

        $payrate_signup = $signup_fee;
        $recorddate_signup = date('Y-m-d H:i:s');

        $insert_signup = $conn->prepare("INSERT INTO signup (user_id, payrate_signup, signup_fee, recorddate, record_id) VALUES (?, ?, ?, ?, ?)");
        if (!$insert_signup) {
            throw new Exception("Prepare failed for signup: " . $conn->error);
        }

        $insert_signup->bind_param("iidss", $user_id, $payrate_signup, $signup_fee, $recorddate_signup, $record_id);
        if (!$insert_signup->execute()) {
            throw new Exception("Failed to insert into signup: " . $insert_signup->error);
        }

        // ปิด Statement หลังจาก execute เสร็จ
        $insert_signup->close();
        // เรียกใช้ฟังก์ชัน `createMemberCard`
        $card_path = createMemberCard(...);
        if (!$card_path) throw new Exception("Failed to create member card");
        // Commit ข้อมูลทั้งสองตาราง
        $conn->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'สมัครสมาชิกสำเร็จ!',
            'redirect' => 'users.php'
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error',  'message' => 'การสมัครสมาชิกเกิดข้อผิดพลาด: ' . $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
