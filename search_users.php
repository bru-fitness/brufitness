<?php
require_once 'dbConnect.php'; // ไฟล์เชื่อมต่อฐานข้อมูล
// รับค่าคำค้นหาและสถานะจาก AJAX
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// สร้าง SQL สำหรับกรอง
$sql = "SELECT * FROM users WHERE 
    (name LIKE '%$search%' OR 
    surname LIKE '%$search%' OR 
    user_id LIKE '%$search%')";

if ($status !== '') { // กรองตามสถานะถ้ามีค่า
    $sql .= " AND status = '$status'";
}

$sql .= " ORDER BY recorddate DESC";

$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userlevel = "";
        switch ($row['userlevel']) {
            case '0': $userlevel = "ผู้ดูแลระบบ"; break;
            case '1': $userlevel = "อาจารย์"; break;
            case '2': $userlevel = "เทรนเนอร์"; break;
            case '3': $userlevel = "สมาชิก"; break;
            case '4': $userlevel = "ผู้ใช้ทั่วไป"; break;
            default: $userlevel = "ไม่ระบุ"; break;
        }

        $status = "";
        switch ($row['status']) {
            case 0: $status = "ยังเป็นสมาชิก"; break;
            case 1: $status = "ใกล้หมดอายุ"; break;
            case 2: $status = "สมาชิกหมดอายุแล้ว"; break;
            case 3: $status = "ไม่ได้เป็นสมาชิก"; break;
            case 4: $status = "บุคลากร"; break;
            default: $status = "ไม่ระบุ"; break;
        }

        $gender = $row['gender'] == '0' ? "ชาย" : ($row['gender'] == '1' ? "หญิง" : "ไม่ระบุ");
        $formatted_date = date_create($row['recorddate']) ? date_format(date_create($row['recorddate']), 'd/m/Y H:i') : "ไม่ระบุ";

        echo "<tr>
                <td>{$row['user_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['surname']}</td>
                <td>{$gender}</td>
                <td>{$userlevel}</td>
                <td>{$row['telephone']}</td>
                <td>{$formatted_date}</td>
                <td>{$status}</td>
                <td>
                    <button class='btn btn-info' onclick='viewUser({$row['user_id']})'>ดูข้อมูลทั้งหมด</button>
                    <button class='btn btn-danger' onclick='confirmDelete({$row['user_id']})'>ลบ</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='9' class='text-center'>ไม่พบข้อมูล</td></tr>";
}

$conn->close();
?>