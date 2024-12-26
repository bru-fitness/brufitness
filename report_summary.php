<?php
session_start();
require_once 'dbConnect.php';

if (!$_SESSION['userid']) {
    header("Location: index.php");
    exit();
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

// ดึงเดือนจาก request (ค่า default เป็นเดือนปัจจุบัน)
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Fetch data for service usage
$service_usage_query = "SELECT COUNT(*) AS total, su.user_id, CONCAT(u.name, ' ', u.surname) AS full_name, DATE(su.service_date) AS service_date 
                        FROM service_usage su 
                        JOIN users u ON su.user_id = u.user_id 
                        WHERE DATE_FORMAT(su.service_date, '%Y-%m') = ?
                        GROUP BY su.user_id, full_name, service_date";
$stmt1 = $conn->prepare($service_usage_query);
$stmt1->bind_param("s", $selected_month);
$stmt1->execute();
$service_usage_result = $stmt1->get_result();

// Fetch data for signups
$signup_query = "SELECT COUNT(*) AS total, s.user_id, CONCAT(u.name, ' ', u.surname) AS full_name, DATE(s.recorddate) AS recorddate 
                 FROM signup s 
                 JOIN users u ON s.user_id = u.user_id 
                 WHERE DATE_FORMAT(s.recorddate, '%Y-%m') = ?
                 GROUP BY s.user_id, full_name, recorddate";
$stmt2 = $conn->prepare($signup_query);
$stmt2->bind_param("s", $selected_month);
$stmt2->execute();
$signup_result = $stmt2->get_result();

// Fetch data for renewals
$renewals_query = "SELECT COUNT(*) AS total, r.user_id, CONCAT(u.name, ' ', u.surname) AS full_name, DATE(r.renewal_date) AS renewal_date 
                   FROM renewals r 
                   JOIN users u ON r.user_id = u.user_id
                   WHERE DATE_FORMAT(r.renewal_date, '%Y-%m') = ?
                   GROUP BY r.user_id, full_name, renewal_date";
$stmt3 = $conn->prepare($renewals_query);
$stmt3->bind_param("s", $selected_month);
$stmt3->execute();
$renewals_result = $stmt3->get_result();

// Fetch counts for pie charts
$service_usage_count = $conn->query("SELECT COUNT(*) AS total FROM service_usage WHERE DATE_FORMAT(service_date, '%Y-%m') = '$selected_month'")->fetch_assoc()["total"];
$signup_count = $conn->query("SELECT COUNT(*) AS total FROM signup WHERE DATE_FORMAT(recorddate, '%Y-%m') = '$selected_month'")->fetch_assoc()["total"];
$renewals_count = $conn->query("SELECT COUNT(*) AS total FROM renewals WHERE DATE_FORMAT(renewal_date, '%Y-%m') = '$selected_month'")->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่วนของอาจารย์</title>
    <!-- Basic meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
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
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="css/responsive.css" />
    <!-- Color CSS -->
    <link rel="stylesheet" href="css/color_2.css" />
    <link rel="stylesheet" href="css/style.css" />
    <!-- Select bootstrap -->
    <link rel="stylesheet" href="css/bootstrap-select.css" />
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="css/perfect-scrollbar.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/custom.css" />
    <!-- Calendar file CSS -->
    <link rel="stylesheet" href="js/semantic.min.css" />

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            margin-top: 60px;
            /* เพิ่ม margin-top เพื่อไม่ให้ topbar บัง */
            max-width: 82%;
            /* ปรับความกว้างตามที่ต้องการ */
        }

        .container-fluid {
            max-width: 100%;

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 60px;

        }

        .chart-container,
        .table-container,
        .form-container {
            flex: 1;
            min-width: 200px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
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
                        </a>
                    </div>
                </div>
            </div>
            <div class="sidebar_blog_2">
                <h4>อาจารย์</h4>
                <ul class="list-unstyled components">
                    <li>
                        <a href="report_summary.php"><i class="fa-solid fa-chart-pie"></i> <span>รายงานสรุปผล</span></a>
                    </li>
                    <li>
                        <a href="report_income.php"><i class="fa-solid fa-hand-holding-dollar"></i> <span>รายงานรายได้</span></a>
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
                                            <a class="dropdown-item" href="profile_lecturer.php"><i class="fa-regular fa-id-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
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
        <div class="container mt-5 content">

            <div class="container">
                <div class="dashboard-container">
                    <!-- Pie Charts -->
                    <div class="chart-container">
                        <canvas id="serviceUsageChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="signupChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="renewalsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="form-container">
                    
                </div>
            <div class="dashboard-container">
                <!-- ตัวเลือกเดือน -->
                <form method="get" id="monthSelector">
                        <label for="month">เลือกเดือน:</label>
                        <input type="month" id="month" name="month" value="<?= htmlspecialchars($selected_month) ?>">
                        <button type="submit">แสดงผล</button>
                    </form>
                <!-- Service Usage Table -->
                <div class="table-container">
                    <h2>Service Usage</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Full Name</th>
                                <th>Usage Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $service_usage_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['user_id'] ?></td>
                                    <td><?= $row['full_name'] ?></td>
                                    <td><?= $row['service_date'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Signup Table -->
                <div class="table-container">
                    <h2>Signups</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Full Name</th>
                                <th>Signup Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $signup_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['user_id'] ?></td>
                                    <td><?= $row['full_name'] ?></td>
                                    <td><?= $row['recorddate'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Renewals Table -->
                <div class="table-container">
                    <h2>Renewals</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Full Name</th>
                                <th>Renewal Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $renewals_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['user_id'] ?></td>
                                    <td><?= $row['full_name'] ?></td>
                                    <td><?= $row['renewal_date'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <script>
            // Pie Chart Data
            const serviceUsageData = <?= json_encode(mysqli_num_rows($service_usage_result)) ?>;
            const signupData = <?= json_encode(mysqli_num_rows($signup_result)) ?>;
            const renewalsData = <?= json_encode(mysqli_num_rows($renewals_result)) ?>;

            // Render Pie Charts
            const renderChart = (ctx, label, data) => {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Total'],
                        datasets: [{
                            label: label,
                            data: [data],
                            backgroundColor: ['#4CAF50'],
                        }],
                    },
                });
            };

            renderChart(document.getElementById('serviceUsageChart').getContext('2d'), 'Service Usage', serviceUsageData);
            renderChart(document.getElementById('signupChart').getContext('2d'), 'Signups', signupData);
            renderChart(document.getElementById('renewalsChart').getContext('2d'), 'Renewals', renewalsData);
        </script>

        <!-- jQuery -->
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