<?php
session_start();
require_once 'dbConnect.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}

// ดึงข้อมูลรูปโปรไฟล์ของผู้ใช้ที่ล็อกอินอยู่
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

$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$startDate = "$selectedYear-$selectedMonth-01";
$endDate = date("Y-m-t", strtotime($startDate)); // วันที่สุดท้ายของเดือน

$data_signup = array_fill(0, 31, 0);
$data_services = array_fill(0, 31, 0);
$data_renewal = array_fill(0, 31, 0);

// ดึงข้อมูลการสมัครสมาชิก
$signup_sql = "SELECT DAYOFMONTH(recorddate) AS day, COUNT(signup_id) AS total FROM signup 
               WHERE recorddate BETWEEN '$startDate' AND '$endDate' 
               GROUP BY DAYOFMONTH(recorddate)";
$signup_result = $conn->query($signup_sql);
while ($row = $signup_result->fetch_assoc()) {
    $data_signup[$row['day'] - 1] = $row['total'];
}

// ดึงข้อมูลการใช้บริการ
$service_sql = "SELECT DAYOFMONTH(service_date) AS day, COUNT(service_id) AS total FROM service_usage 
                WHERE service_date BETWEEN '$startDate' AND '$endDate' 
                GROUP BY DAYOFMONTH(service_date)";
$service_result = $conn->query($service_sql);
while ($row = $service_result->fetch_assoc()) {
    $data_services[$row['day'] - 1] = $row['total'];
}

// ดึงข้อมูลการต่ออายุ
$renewal_sql = "SELECT DAYOFMONTH(renewal_date) AS day, COUNT(renewal_id) AS total FROM renewals 
                WHERE renewal_date BETWEEN '$startDate' AND '$endDate' 
                GROUP BY DAYOFMONTH(renewal_date)";
$renewal_result = $conn->query($renewal_sql);
while ($row = $renewal_result->fetch_assoc()) {
    $data_renewal[$row['day'] - 1] = $row['total'];
}

// สร้างลิสต์วันที่ในเดือนนั้น
$days = [];
$daysInMonth = date('t', strtotime($startDate));
for ($d = 1; $d <= $daysInMonth; $d++) {
    $days[] = "$d/$selectedMonth";
}
// ตัวแปรชื่อเดือนภาษาไทย
$thai_months = [
    'มกราคม',
    'กุมภาพันธ์',
    'มีนาคม',
    'เมษายน',
    'พฤษภาคม',
    'มิถุนายน',
    'กรกฎาคม',
    'สิงหาคม',
    'กันยายน',
    'ตุลาคม',
    'พฤศจิกายน',
    'ธันวาคม'
];
// นับจำนวนรายการใน service_usage
$service_sql = "SELECT COUNT(service_id) AS total_services FROM service_usage WHERE DATE(service_date) = CURDATE()";
$service_result = $conn->query($service_sql);
$total_services = ($service_result->num_rows > 0) ? $service_result->fetch_assoc()['total_services'] : 0;

// นับจำนวนรายการใน signup
$signup_sql = "SELECT COUNT(signup_id) AS total_signups FROM signup WHERE DATE(recorddate) = CURDATE()";
$signup_result = $conn->query($signup_sql);
$total_signups = ($signup_result->num_rows > 0) ? $signup_result->fetch_assoc()['total_signups'] : 0;

// นับจำนวนรายการใน renewal
$renewal_sql = "SELECT COUNT(renewal_id) AS total_renewals FROM renewals WHERE DATE(renewal_date) = CURDATE()";
$renewal_result = $conn->query($renewal_sql);
$total_renewals = ($renewal_result->num_rows > 0) ? $renewal_result->fetch_assoc()['total_renewals'] : 0;

$current_page = basename($_SERVER['PHP_SELF']);
// ส่งข้อมูลเพื่อใช้ในกราฟ
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่วนของผู้ดูแลระบบ</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Site metas -->
    <title>BRU FITNESS</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Site icon -->
    <link rel="icon" href="images/fevicon.png" type="image/png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- Site CSS -->
    <link rel="stylesheet" href="style.css" />
    <!-- <link rel="stylesheet" href="css/sidebar.css" /> -->
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
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- ฟอนต์ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Anuphun:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    <style>
        body,
        h3,
        h4,
        p,
        div {
            font-family: 'Anuphun', sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            margin-top: 50px;
            /* เพิ่ม margin-top เพื่อไม่ให้ topbar บัง */
            max-width: 82%;
            /* ปรับความกว้างตามที่ต้องการ */
        }

        .container .card {
            color: cadetblue;
        }

        .dropdown-item.active {
            background-color: lightblue;
            /* กำหนดสีพื้นหลัง */
            color: #ffffff;
            /* กำหนดสีข้อความ */
        }
    </style>
    <style>
        .summary-container {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 20px;
        }

        .summary-box {
            width: 150px;
            height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #555;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .summary-box .number {
            font-size: 36px;
            font-weight: bold;
            color: #000;
        }

        .box-service {
            background-color: #FFB7DB;
            /* สีชมพู */
        }

        .box-signup {
            background-color: #D3ACF8;
            /* สีม่วง */
        }

        .box-renewal {
            background-color: #FC898B;
            /* สีแดง */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                            <img src="https://www.bru.ac.th/wp-content/uploads/2019/08/bru-web-logo-en.png"
                                alt="BRU Logo" class="brand-image img-circle elevation-3"
                                style="opacity: .8; max-width: 150px;">
                            <h3>BRU FITNESS</h3>
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar_blog_2">
                <h4>ผู้ดูแลระบบ</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="admin_page.php" class="dropdown-item active" aria-current="true"><i
                                class="fa-solid fa-home"></i> <span>หน้าแรก</span></a>
                    </li>
                    <li>
                        <a href="sign_up.php"><i class="fa-solid fa-user-plus"></i> <span>สมัครสมาชิก</span></a>
                    </li>
                    <li>
                        <a href="service_page.php"><i class="fa-solid fa-person-walking-arrow-right"></i>
                            <span>เข้าใช้บริการ</span></a>
                    </li>
                    <li>
                        <a href="renewals.php"><i class="fa-solid fa-user-clock"></i> </i>
                            <span>ต่ออายุสมาชิก</span></a>
                    </li>
                    <li class="active">
                        <a href="#users" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle"><i
                                class="fa-solid fa-house-user"></i> <span>จัดการข้อมูลหลัก</span></a>
                        <ul class="collapse list-unstyled" id="users">
                            <li>
                                <a href="users.php"><i class="fas fa-user-cog"></i><span>ข้อมูลผู้ใช้ระบบ</span></a>
                            </li>
                            <li>
                                <a href="payrate.php"><i class="fas fa-hand-holding-usd"></i>
                                    <span>อัตราการชำระเงิน</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="active">
                        <a href="#report" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle"><i
                                class="fa fa-newspaper-o"></i> <span>รายงาน</span></a>
                        <ul class="collapse list-unstyled" id="report">
                            <li>
                                <a class="dropdown-item" href="report_service.php" aria-current="true"><i
                                        class="fa fa-calendar-check-o"></i><span>การเข้าใช้บริการ</span></a>
                            </li>
                            <li>
                                <a href="report_signup.php"><i class="fas fa-user-check"></i> <span>การสมัครสมาชิก</span></a>
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
                                            <a class="dropdown-item" href="profile_admin.php"><i
                                                    class="fa-regular fa-id-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
                                            <a class="dropdown-item" href="logout.php"><span>ออกจากระบบ</span> <i
                                                    class="fa fa-sign-out"></i></a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <div class="container mt-5 content">
            <div class="container">
                <div class="summary">
                    <div class="summary-container">
                        <div class="summary-box box-service">
                            <div>เข้าใช้บริการวันนี้</div>
                            <strong><?php echo $total_services; ?> คน</strong>
                        </div>
                        <div class="summary-box box-signup">
                            <div>สมัครสมาชิกวันนี้</div>
                            <strong><?php echo $total_signups; ?> คน</strong>
                        </div>
                        <div class="summary-box box-renewal">
                            <div>ต่ออายุสมาชิกวันนี้</div>
                            <strong><?php echo $total_renewals; ?> คน</strong>
                        </div>

                    </div>
                    <hr>
                    <h3>กราฟจำนวนผู้สมัคร, จำนวนผู้ใช้บริการ, และจำนวนผู้ต่ออายุในแต่ละวัน</h3>
                    <canvas id="myChart" width="400" height="200"></canvas>
                    <div>
                        <form method="POST" action="">
                            <label for="month">เลือกเดือน:</label>
                            <select id="month" name="month">
                                <?php foreach ($thai_months as $index => $month) : ?>
                                    <option value="<?php echo $index + 1; ?>" <?php echo (isset($_POST['month']) && $_POST['month'] == ($index + 1)) ? 'selected' : ''; ?>>
                                        <?php echo $month; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button type="submit">แสดงข้อมูล</button>
                        </form>
                    </div>
                </div>

            </div>
            <div>

                <script>
                    
                    const signupData = <?php echo json_encode($data_signup); ?>;
                    const serviceData = <?php echo json_encode($data_services); ?>;
                    const renewalData = <?php echo json_encode($data_renewal); ?>;
                    const days = <?php echo json_encode($days); ?>;

                    window.onload = function() {
                        const ctx = document.getElementById('myChart').getContext('2d');
                        const chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: days,
                                datasets: [{
                                        label: 'จำนวนผู้สมัคร',
                                        data: signupData,
                                        backgroundColor: 'rgba(135, 22, 155, 0.2)',
                                        borderColor: 'rgb(97, 18, 104)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'จำนวนผู้ใช้บริการ',
                                        data: serviceData,
                                        backgroundColor: 'rgba(255, 102, 199, 0.2)',
                                        borderColor: 'rgb(255, 102, 201)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'จำนวนผู้ต่ออายุ',
                                        data: renewalData,
                                        backgroundColor: 'rgba(247, 21, 21, 0.2)',
                                        borderColor: 'rgb(184, 9, 9)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true, // เริ่มจาก 0
                                        ticks: {
                                            stepSize: 1, // เพิ่มทีละ 1
                                            callback: function(value) {
                                                return Number.isInteger(value) ? value : ''; // แสดงเฉพาะจำนวนเต็ม
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    };
                    
                </script>

            </div>
        </div>
        <!-- js active -->
        <script src="js/active.js"></script>
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
        <script src="js/Chart.js"></script>
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
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    </div>
</body>

</html>