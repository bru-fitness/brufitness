<?php
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเริ่มต้น session
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}

// สร้างคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง users
$query = "SELECT user_id, picture, CONCAT(name, ' ', surname) AS full_name, type, recorddate FROM users WHERE user_id LIKE '3%'";  // เงื่อนไข LIKE เพื่อกรองเฉพาะ user_id ที่ขึ้นต้นด้วย '3'
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
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

// นับจำนวนคนที่เข้าใช้บริการวันนี้
$count_today_query = "SELECT COUNT(*) AS count_today 
                      FROM signup 
                      WHERE user_id IS NOT NULL AND DATE(recorddate) = CURDATE()";
$count_today_result = mysqli_query($conn, $count_today_query);

if (!$count_today_result) {
    die("Query Failed: " . mysqli_error($conn));
}

$count_today_row = mysqli_fetch_assoc($count_today_result);
$count_today = $count_today_row['count_today'];

mysqli_free_result($count_today_result); // คืนพื้นที่หน่วยความจำที่ใช้เก็บผลลัพธ์

// นับจำนวนคนที่เข้าใช้บริการสัปดาห์นี้
$count_week_query = "SELECT COUNT(*) AS count_week 
                     FROM signup 
                     WHERE user_id IS NOT NULL AND WEEK(recorddate, 1) = WEEK(CURDATE(), 1) 
                     AND YEAR(recorddate) = YEAR(CURDATE())";
$count_week_result = mysqli_query($conn, $count_week_query);

if (!$count_week_result) {
    die("Query Failed: " . mysqli_error($conn));
}

$count_week_row = mysqli_fetch_assoc($count_week_result);
$count_week = $count_week_row['count_week'];

mysqli_free_result($count_week_result); // คืนพื้นที่หน่วยความจำที่ใช้เก็บผลลัพธ์

// นับจำนวนคนที่เข้าใช้บริการเดือนนี้
$count_month_query = "SELECT COUNT(*) AS count_month 
                      FROM signup 
                      WHERE user_id IS NOT NULL AND MONTH(recorddate) = MONTH(CURDATE()) 
                      AND YEAR(recorddate) = YEAR(CURDATE())";
$count_month_result = mysqli_query($conn, $count_month_query);

if (!$count_month_result) {
    die("Query Failed: " . mysqli_error($conn));
}

$count_month_row = mysqli_fetch_assoc($count_month_result);
$count_month = $count_month_row['count_month'];

mysqli_free_result($count_month_result); // คืนพื้นที่หน่วยความจำที่ใช้เก็บผลลัพธ์

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการสมัครสมาชิก</title>
    <title>ต่ออายุสมาชิก</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
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
    </style>

    <style>
        .container {
            padding-top: 40px;
            display: flex;
            justify-content: center;
            max-width: 90%;
            margin-top: 50px;
            /* เพิ่ม margin-top เพื่อไม่ให้ topbar บัง */
        }

        .container-fluid {
            margin-left: 120px;
        }

        .dropdown-item.active {
            background-color: lightblue;
            /* กำหนดสีพื้นหลัง */
            color: #ffffff;
            /* กำหนดสีข้อความ */
        }
    </style>

</head>

<body>
    <div class="inner_container">
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
                                <a href="payrate.php"><i class="fas fa-hand-holding-usd"></i> <span>อัตราการชำระเงิน</span></a>
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
                                <a class="dropdown-item active" href="report_signup.php" aria-current="true"><i class="fas fa-user-check"></i> <span>การสมัครสมาชิก</span></a>
                            </li>
                            <li>
                                <a href="report_renewal.php"><i class="fa fa-refresh"></i> <span>การต่ออายุ</span></a>
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
        </div>

        <div class="container">
            <div class="container-fluid">
            <div class="row g-4">
                    <div class="col-sm-4 ">
                        <div class="bg-info  rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-line fa-3x text-white"></i>
                            <div class="ms-3">
                                <p class="mb-2 text-white">เข้าใช้บริการวันนี้</p>
                                <h6 class="mb-0 text-white">จำนวน <?php echo htmlspecialchars($count_today); ?> คน</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 ">
                        <div class="bg-secondary  rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-line fa-3x text-white"></i>
                            <div class="ms-3">
                                <p class="mb-2 text-white">เข้าใช้บริการสัปดาห์นี้</p>
                                <h6 class="mb-0 text-white">จำนวน <?php echo htmlspecialchars($count_week); ?> คน</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 ">
                        <div class="bg-success  rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-line fa-3x text-white"></i>
                            <div class="ms-3">
                                <p class="mb-2 text-white">เข้าใช้บริการเดือนนี้</p>
                                <h6 class="mb-0 text-white">จำนวน <?php echo htmlspecialchars($count_month); ?> คน</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-container">
                    <h3 class="text-center">รายงานการสมัครสมาชิก</h3>
                    <tbody>
                        <?php
                        // Query เพื่อดึงข้อมูลจากตาราง renewals
                        $signup_query = "
                        SELECT s.signup_id, s.user_id, s.payrate_signup, s.signup_fee, s.recorddate, 
                               s.record_id, CONCAT(u.name, ' ', u.surname) AS recorder_name
                        FROM signup s
                        LEFT JOIN users u ON s.record_id = u.user_id";
                        $signup_result = mysqli_query($conn, $signup_query);

                        if (!$signup_result) {
                            die("Query Failed: " . mysqli_error($conn));
                        }

                        ?>
                        <div class="table-responsive">
                            <table class="table  table-striped table-bordered mt-4">
                                <thead>
                                    <tr>
                                        <th>รหัสสมาชิก</th>
                                        <th>ชื่อ-สกุล</th>
                                        <th>วันที่สมัครสมาชิก</th>
                                        <th>ผู้ทำรายการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['recorddate']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($row['recorder_name'] ?? 'ไม่ทราบชื่อ'); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
            <script src="js/jquery.min.js"></script>
            <script src="js/popper.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
            <script src="js/animate.js"></script>
            <script src="js/bootstrap-select.js"></script>
            <script src="js/owl.carousel.js"></script>
            <script src="js/Chart.min.js"></script>
            <script src="js/Chart.bundle.min.js"></script>
            <script src="js/utils.js"></script>
            <script src="js/analyser.js"></script>
            <script src="js/perfect-scrollbar.min.js"></script>
            <script>
                var ps = new PerfectScrollbar('#sidebar');
            </script>
            <script src="js/custom.js"></script>
            <script src="js/semantic.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        </div>

</body>

</html>

<?php
mysqli_close($conn); // ปิดการเชื่อมต่อฐานข้อมูล
?>