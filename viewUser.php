<?php
session_start();
require_once 'dbConnect.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    echo "User ID: $user_id<br>"; // Debugging output
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
?>
<div class="container">
    <!-- <h2>ข้อมูลผู้ใช้</h2> -->
    <table class="table table-bordered">
        <tr>
            <td colspan="2" class="text-center">
                <!-- เพิ่มรูปภาพ -->
                <?php
                        if (!empty($row['picture'])) {
                            $imagePath = "uploads/" . $row['picture'];
                            echo "<img src='$imagePath' alt='User Picture' style='max-width: 200px;'>";
                        } else {
                            echo "ไม่มีรูปภาพ";
                        }
                        ?>
            </td>
        </tr>
        <tr>
            <!-- <td><?php echo $row['picture'] ?></td> -->
        </tr>
        <tr>
            <th>ชื่อ</th>
            <td><?php echo $row['name']; ?></td>
        </tr>
        <tr>
            <th>นามสกุล</th>
            <td><?php echo $row['surname']; ?></td>
        </tr>
        <tr>
            <th>เพศ</th>
            <td>
                <?php
                        switch ($row['gender']) {
                            case '0':
                                echo "ชาย";
                                break;
                            case '1':
                                echo "หญิง";
                                break;
                            default:
                                echo "ไม่ระบุ";
                                break;
                        }
                        ?>
            </td>
        </tr>
        <tr>
            <th>ประเภทผู้ใช้</th>
            <td>
                <?php
                        $userlevel = "";
                        switch ($row['userlevel']) {
                            case '0':
                                $userlevel = "ผู้ดูแลระบบ";
                                break;
                            case '1':
                                $userlevel = "อาจารย์";
                                break;
                            case '2':
                                $userlevel = "เทรนเนอร์";
                                break;
                            case '3':
                                $userlevel = "สมาชิก";
                                break;
                            case '4':
                                $userlevel = "ผู้ใช้ทั่วไป";
                                break;
                            default:
                                $userlevel = "ไม่ระบุ";
                                break;
                        }
                        echo $userlevel;
                        ?>
            </td>
        </tr>
        <tr>
            <th>ชื่อผู้ใช้</th>
            <td><?php echo $row['username']; ?></td>
        </tr>
        <tr>
            <th>เบอร์โทรศัพท์</th>
            <td><?php echo $row['telephone']; ?></td>
        </tr>
        <tr>
            <th>สถานะ</th>
            <td>
                <?php
                        switch ($row['status']) {
                            case 0:
                                echo "ยังเป็นสมาชิก";
                                break;
                            case 1:
                                echo "ใกล้หมดอายุ";
                                break;
                            case 2:
                                echo "สมาชิกหมดอายุ";
                                break;
                            case 3:
                                echo "ไม่ได้เป็นสมาชิก";
                                break;
                            case 4:
                                echo "บุคลากร";
                                break;
                            default:
                                echo "ไม่ระบุ";
                                break;
                        }
                        ?>
            </td>
        </tr>
        <tr>
            <th>วันที่บันทึกเข้าระบบ</th>
            <td><?php echo $row['recorddate']; ?></td>
        </tr>
    </table>
</div>
<?php
    } else {
        echo "ไม่พบข้อมูลผู้ใช้";
    }
} else {
    echo "ไม่พบรหัสผู้ใช้";
}
?>