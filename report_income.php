<?php
session_start();
require_once 'dbConnect.php';

if (!$_SESSION['userid']) {
    header("Location: index.php");
    exit();
}
// Fetch monthly revenue for service usage
$service_revenue_query = "SELECT MONTH(service_date) AS month, SUM(payrate_service) AS total_revenue 
                          FROM service_usage 
                          GROUP BY MONTH(service_date)";
$service_revenue_result = $conn->query($service_revenue_query);

$service_revenue_data = array_fill(1, 12, 0);
while ($row = $service_revenue_result->fetch_assoc()) {
    $service_revenue_data[(int)$row['month']] = (float)$row['total_revenue'];
}

// Fetch monthly revenue for signups
$signup_revenue_query = "SELECT MONTH(recorddate) AS month, SUM(payrate_signup) AS total_revenue 
                         FROM signup 
                         GROUP BY MONTH(recorddate)";
$signup_revenue_result = $conn->query($signup_revenue_query);

$signup_revenue_data = array_fill(1, 12, 0);
while ($row = $signup_revenue_result->fetch_assoc()) {
    $signup_revenue_data[(int)$row['month']] = (float)$row['total_revenue'];
}

// Fetch monthly revenue for renewals
$renewal_revenue_query = "SELECT MONTH(renewal_date) AS month, SUM(payrate_renewal) AS total_revenue 
                          FROM renewals 
                          GROUP BY MONTH(renewal_date)";
$renewal_revenue_result = $conn->query($renewal_revenue_query);

$renewal_revenue_data = array_fill(1, 12, 0);
while ($row = $renewal_revenue_result->fetch_assoc()) {
    $renewal_revenue_data[(int)$row['month']] = (float)$row['total_revenue'];
}

$months_th = [
    1 => "มกราคม",
    2 => "กุมภาพันธ์",
    3 => "มีนาคม",
    4 => "เมษายน",
    5 => "พฤษภาคม",
    6 => "มิถุนายน",
    7 => "กรกฎาคม",
    8 => "สิงหาคม",
    9 => "กันยายน",
    10 => "ตุลาคม",
    11 => "พฤศจิกายน",
    12 => "ธันวาคม"
];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            margin: auto;
            width: 500%;
            max-width: 800px;
        }

        .container {
            margin: 20px 0;
            width: 1000%;
            max-width: 1250px;
            margin-top: 60px;
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
                        <a href="report_summary.php"><i class="fa-solid fa-chart-pie"></i> <span>รายงานการเข้าใช้บริการ</span></a>
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
                                        <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#">
                                            <span class="name_user"><?php echo $_SESSION['user']; ?></span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="profile.php"><i class="fa-regular fa-id-badge"></i> <span>ข้อมูลส่วนตัว</span></a>
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
                    
                    <div class="chart-container">
                        <h2>รายได้จากการเข้าใช้บริการ</h2>
                        <canvas id="serviceRevenueChart"></canvas>
                    </div>

                    <div class="chart-container">
                        <h2>รายได้จากการสมัครสมาชิก</h2>
                        <canvas id="signupRevenueChart"></canvas>
                    </div>

                    <div class="chart-container">
                        <h2>รายได้จากการต่ออายุสมาชิก</h2>
                        <canvas id="renewalRevenueChart"></canvas>
                    </div>

                    <script>
                        const monthsTh = <?= json_encode(array_values($months_th)) ?>;
                        const serviceRevenueData = <?= json_encode(array_values($service_revenue_data)) ?>;
                        const signupRevenueData = <?= json_encode(array_values($signup_revenue_data)) ?>;
                        const renewalRevenueData = <?= json_encode(array_values($renewal_revenue_data)) ?>;

                        const renderChart = (ctx, label, data) => {
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: monthsTh,
                                    datasets: [{
                                        label: label,
                                        data: data,
                                        borderColor: '#4CAF50',
                                        fill: false,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: true,
                                        },
                                    },
                                    scales: {
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'เดือน'
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1000
                                            },
                                            title: {
                                                display: true,
                                                text: 'จำนวนเงิน (บาท)'
                                            }
                                        },
                                    },
                                },
                            });
                        };

                        renderChart(
                            document.getElementById('serviceRevenueChart').getContext('2d'),
                            'รายได้จากการเข้าใช้บริการ',
                            serviceRevenueData
                        );

                        renderChart(
                            document.getElementById('signupRevenueChart').getContext('2d'),
                            'รายได้จากการสมัครสมาชิก',
                            signupRevenueData
                        );

                        renderChart(
                            document.getElementById('renewalRevenueChart').getContext('2d'),
                            'รายได้จากการต่ออายุสมาชิก',
                            renewalRevenueData
                        );
                    </script>
                </div>
            </div>
        </div>



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

<?php
$conn->close();
?>