<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
// ดึงข้อมูลสมาชิก
$sql_members = "SELECT * FROM payrate WHERE userlevel = 3";
$result_members = $conn->query($sql_members);
if (!$result_members) {
    die("Error in query for members: " . $conn->error);
}
// ดึงข้อมูลไม่เป็นสมาชิก
$sql_non_members = "SELECT * FROM payrate WHERE userlevel = 4";
$result_non_members = $conn->query($sql_non_members);
if (!$result_non_members) {
    die("Error in query for non-members: " . $conn->error);
}
// ดึงรูปโปรไฟล์ของผู้ใช้ที่ล็อกอินอยู่
$userId = $_SESSION['userid'];
$sql = "SELECT picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Failed to prepare statement: " . $conn->error);
}
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($photo);
$stmt->fetch();
$stmt->close();

// กำหนด path รูปโปรไฟล์
$uploadDir = 'uploads/';
$defaultImage = '../assets/images/profile/user-1.jpg';
$profileImage = !empty($photo) && file_exists($uploadDir . $photo) ? $uploadDir . $photo : $defaultImage;

// คิวรีเพื่อดึงจำนวนผู้ใช้ที่ใกล้หมดอายุการเป็นสมาชิก
$currentDate = date('Y-m-d');
$sql = "SELECT COUNT(*) as expiringCount FROM users 
        WHERE DATE_ADD(recorddate, INTERVAL 2 YEAR) BETWEEN '$currentDate' AND DATE_ADD('$currentDate', INTERVAL 30 DAY)";

$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์ของคิวรี
if ($result) {
    $row = $result->fetch_assoc();
    $expiringCount = $row['expiringCount']; // จำนวนผู้ใช้ที่ใกล้หมดอายุ
} else {
    $expiringCount = 0; // กรณีที่คิวรีไม่สำเร็จหรือไม่มีผู้ใช้ที่ใกล้หมดอายุ
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลค่าบริการ</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Site metas -->
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Site icon -->
    <link rel="icon" href="images/fevicon.png" type="image/png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- Site CSS -->
    <link rel="stylesheet" href="style.css" />
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="css/responsive.css" />
    <!-- Color CSS -->
    <link rel="stylesheet" href="css/color_2.css" />
    <!-- Select bootstrap -->
    <link rel="stylesheet" href="css/bootstrap-select.css" />
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="css/perfect-scrollbar.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/custom.css" />
    <!-- Calendar file CSS -->
    <link rel="stylesheet" href="js/semantic.min.css" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        body,
        h3,
        h4,
        p,
        div {
            font-family: "Chakra Petch", sans-serif;
        }

        .dropdown-item.active {
            background-color: lightblue;
            /* กำหนดสีพื้นหลัง */
            color: #ffffff;
            /* กำหนดสีข้อความ */
        }
    </style>

    <style>
        .container {
            margin-top: 20px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding-left: 50px;
            padding-right: 70px;
            display: flex;
            align-items: center;
            flex-direction: column;
            position: relative;
            /* เพิ่ม position relative เพื่อควบคุม layer ของ element */
            z-index: 10;
            /* กำหนด z-index ให้สูงขึ้นเพื่อให้ element นี้แสดงอยู่ข้างบน */
        }

        body {
            overflow-x: hidden;
            /* ป้องกันการเลื่อนแนวนอนถ้ามี overflow */
        }
        th,
        td {
            text-align: center;
            /* จัดตำแหน่งข้อความในเซลล์ให้เป็นกลาง */
        }
        .table{
            width: 1150px;
        }
    </style>

</head>

<body>
    <div class="inner_container">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar_blog_1">
                <div class="sidebar-header"></div>
                <div class="sidebar_user_info">
                    <div class="user_info">
                        <a href="#" class="brand-link">
                            <img src="https://www.bru.ac.th/wp-content/uploads/2019/08/bru-web-logo-en.png" alt="BRU Logo" class="brand-image img-circle elevation-3" style="opacity: .8; max-width: 150px;">
                            <h3>BRU FITNESS</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar_blog_2">
                <h4>ผู้ดูแลระบบ</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="admin_page.php"><i class="fa-solid fa-home"></i> <span>หน้าแรก</span></a>
                    </li>
                    <li>
                        <a href="sign_up.php"><i class="fa-solid fa-user-plus"></i> <span>สมัครสมาชิก</span></a>
                    </li>
                    <li>
                        <a href="service_page.php"><i class="fa-solid fa-person-walking-arrow-right"></i> <span>เข้าใช้บริการ</span></a>
                    </li>
                    <li>
                        <a href="renewals.php"><i class="fa-solid fa-user-clock"></i> </i> <span>ต่ออายุสมาชิก</span></a>
                    </li>
                    <li class="active">
                        <a href="#users" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle"><i class="fa-solid fa-house-user"></i> <span>จัดการข้อมูลหลัก</span></a>
                        <ul class="collapse list-unstyled" id="users">
                            <li>
                                <a href="users.php"><i class="fas fa-user-cog"></i><span>ข้อมูลผู้ใช้ระบบ</span></a>
                            </li>
                            <li>
                                <a href="payrate.php" class="dropdown-item active"><i class="fas fa-hand-holding-usd"></i> <span>อัตราการชำระเงิน</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="active">
                        <a href="#report" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle"><i class="fa fa-newspaper-o"></i> <span>รายงาน</span></a>
                        <ul class="collapse list-unstyled" id="report">
                            <li>
                                <a href="report_service.php"><i class="fa fa-calendar-check-o"></i><span>การเข้าใช้บริการ</span></a>
                            </li>
                            <li>
                                <a href=".php"><i class="fas fa-user-check"></i> <span>การสมัครสมาชิก</span></a>
                            </li>
                            <li>
                                <a href=".php"><i class="fa fa-refresh"></i> <span>การต่ออายุ</span></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="content">
            <div class="topbar">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="full">
                        <div class="right_topbar">
                            <div class="icon_info">
                                <ul class="notification">
                                    <li class="nav-item">
                                        <a class="nav-link" href="notifications.php">
                                            <i class="fa fa-bell"></i>
                                            <span class="badge badge-danger">
                                                <?php echo $expiringCount; ?>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="user_profile_dd">
                                    <li>
                                        <a class=" dropdown-toggle" href="javascript:void(0)" id="drop2"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="<?php echo $profileImage; ?>" alt="Profile Picture" width="35"
                                                height="35" class="rounded-circle">
                                            <span
                                                class="d-none d-lg-inline-flex fs-3 m-2 text-dark"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="profile_admin.php"><i class="fa-regular fa-id-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
                                            <a class="dropdown-item" href="logout.php"><span>ออกจากระบบ</span> <i class="fa fa-sign-out"></i></a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
            <br>
        </div>

        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="container">
                <h1 class="text-center">อัตราการชำระเงิน</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-center" style="background-color:aqua">สมาชิก</th>
                        </tr>
                        <tr>
                            <th class="text-center">ประเภท</th>
                            <th class="text-center">ค่าสมัครสมาชิก</th>
                            <th class="text-center">ค่าต่ออายุสมาชิก</th>
                            <th class="text-center">ค่าเข้าใช้บริการ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                                        <tbody>
                        <?php
                        // แสดงข้อมูลสมาชิก
                        if ($result_members->num_rows > 0) {
                            while ($row = $result_members->fetch_assoc()) {
                                if ($row['type'] == 1) {
                                    // สมาชิกประเภทบุคลากร
                                    echo "<tr>";
                                    echo "<td>บุคลากร</td>";
                                    echo "<td>{$row['payrate_signup']}</td>";
                                    echo "<td>{$row['payrate_renewal']}</td>";
                                    echo "<td>{$row['payrate_service']}</td>";
                                    echo "<td class='text-center'>
                                    <a href='edit_payrate.php?id={$row['payrate_id']}' class='btn btn-warning btn-sm'>แก้ไข</a>
                                  </td>";
                            echo "</tr>";
                                    echo "</tr>";
                                } else if ($row['type'] == 2) {
                                    // สมาชิกประเภทบุคคลทั่วไป
                                    echo "<tr>";
                                    echo "<td>บุคคลทั่วไป</td>";
                                    echo "<td>{$row['payrate_signup']}</td>";
                                    echo "<td>{$row['payrate_renewal']}</td>";
                                    echo "<td>{$row['payrate_service']}</td>";
                                    echo "<td class='text-center'>
                                    <a href='edit_payrate.php?id={$row['payrate_id']}' class='btn btn-warning btn-sm'>แก้ไข</a>
                                  </td>";
                            echo "</tr>";
                                    echo "</tr>";
                                }
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>ไม่มีข้อมูลสำหรับสมาชิก</td></tr>";
                        }
                        ?>
                        </tbody>
                        <br>
                        <thead>
                        <tr>
                            <th colspan="5" class="text-center" style="background-color:aqua">ไม่เป็นสมาชิก</th>
                        </tr>
                        <tr>
                            <th class="text-center">ประเภท</th>
                            <th class="text-center">ค่าสมัครสมาชิก</th>
                            <th class="text-center">ค่าต่ออายุสมาชิก</th>
                            <th class="text-center">ค่าเข้าใช้บริการ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                        <tbody>
                        <?php
                            // แสดงข้อมูลไม่เป็นสมาชิก
                            if ($result_non_members->num_rows > 0) {
                                while ($row = $result_non_members->fetch_assoc()) {
                                    if ($row['type'] == 1) {
                                        // ไม่เป็นสมาชิกประเภทบุคลากร
                                        echo "<tr>";
                                        echo "<td>บุคลากร</td>";
                                        echo "<td>{$row['payrate_signup']}</td>";
                                        echo "<td>{$row['payrate_renewal']}</td>";
                                        echo "<td>{$row['payrate_service']}</td>";
                                        echo "<td class='text-center'>
                                        <a href='edit_payrate.php?id={$row['payrate_id']}' class='btn btn-warning btn-sm'>แก้ไข</a>
                                      </td>";
                                        echo "</tr>";
                                    } else if ($row['type'] == 2) {
                                        // ไม่เป็นสมาชิกประเภทบุคคลทั่วไป
                                        echo "<tr>";
                                        echo "<td>บุคคลทั่วไป</td>";
                                        echo "<td>{$row['payrate_signup']}</td>";
                                        echo "<td>{$row['payrate_renewal']}</td>";
                                        echo "<td>{$row['payrate_service']}</td>";
                                        echo "<td class='text-center'>
                                        <a href='edit_payrate.php?id={$row['payrate_id']}' class='btn btn-warning btn-sm'>แก้ไข</a>
                                      </td>";
                                        echo "</tr>";
                                    }
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>ไม่มีข้อมูลสำหรับสมาชิก</td></tr>";
                            }
                            ?>
                        </tbody>
                </table>
            </div>
        </div>



        <script src="js/jquery.min.js"></script>
        <!-- Popper.js -->
        <script src="js/popper.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="js/bootstrap.min.js"></script>
        <!-- Wow animation -->
        <script src="js/animate.js"></script>
        <!-- Select country -->
        <script src="js/bootstrap-select.js"></script>
        <!-- Owl carousel -->
        <script src="js/owl.carousel.js"></script>
        <!-- Chart JS -->
        <script src="js/Chart.min.js"></script>
        <script src="js/Chart.bundle.min.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/analyser.js"></script>
        <!-- Nice scrollbar -->
        <script src="js/perfect-scrollbar.min.js"></script>
        <script>
            var ps = new PerfectScrollbar('#sidebar');
        </script>
        <!-- Custom JS -->
        <script src="js/custom.js"></script>
        <!-- Calendar file CSS -->
        <script src="js/semantic.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    </div>

</body>

</html>

<?php
// Close database connection
mysqli_close($conn);
?>