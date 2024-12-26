<?php
session_start();
require_once 'dbConnect.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['userid']; // รับ user_id จาก session

// ดึงข้อมูลโปรไฟล์และบัตรสมาชิก
$sql = "SELECT picture, cardImage FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Failed to prepare statement: " . $conn->error);
}

$stmt->bind_param('s', $userId); // ใช้ 's' เพราะ user_id เป็น STRING
$stmt->execute();
$stmt->bind_result($picture, $cardImage);
$stmt->fetch();
$stmt->close();
// ตั้งค่ารูปโปรไฟล์
$uploadDir = 'uploads/';
$defaultProfileImage = 'cards/carderror.jpg';
$profileImage = (!empty($picture) && file_exists($uploadDir . $picture)) ? $uploadDir . $picture : $defaultProfileImage;

// ตั้งค่าภาพบัตรสมาชิก
$cardDir = 'cards/';
$defaultCardImage = 'cards/carderror.jpg';
if ($cardImage && file_exists($cardDir . $cardImage)) {
    $cardImagePath = $cardDir . $cardImage;
} else {
    $cardImagePath = $defaultCardImage;
}
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่วนของสมาชิก</title>
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
    </style>

    <style>
        .content {
            padding-top: 40px;
            padding-left: 20px;
        }

        .container {
            max-width: 80%;
            /* ปรับความกว้างตามที่ต้องการ */
            margin: 0 auto;
            /* จัดกึ่งกลางในแนวนอน */
            padding-left: 20px;
            /* เพิ่ม padding ด้านซ้ายเพื่อหลีกเลี่ยงการถูกบัง */
            padding-right: 20px;
            /* เพิ่ม padding ด้านขวาเพื่อความสมดุล */
            display: flex;
            /* justify-content: center; */
            align-items: center;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <div class="inner_container">
        <!-- Sidebar -->
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
                <h4>สมาชิก</h4>
                <ul class="list-unstyled components">
                    <li class="active">
                        <a href="#service" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>ประวัติการเข้าใช้บริการ</span>
                        </a>
                        <ul class="collapse list-unstyled" id="service">
                            <li>
                                <a href="service_yourself.php"><i class="fa-solid fa-user"></i> <span>เข้าใช้บริการด้วยตนเอง</span></a>
                            </li>
                            <li>
                                <a href="service_trainer.php"><i class="fas fa-dumbbell"></i> <span>เข้าใช้บริการกับเทรนเนอร์</span></a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="checkstatus.php"><i class="fa-solid fa-address-card"></i> <span>ตรวจสอบการเป็นสมาชิก</span></a>
                    </li>

                    <li>
                        <a href="alltrainer.php"><i class="fa-solid fa-people-group"></i><span> ข้อมูลเทรนเนอร์ทั้งหมด</span></a>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Topbar -->
        <div class="topbar">
            <nav class="navbar navbar-expand-lg navbar-light">
                <!-- ปุ่ม Toggle Sidebar -->
                <!-- <button id="sidebarToggle" class="btn btn-secondary">
                    <i class="fas fa-bars"></i>
                </button> -->
                <div class="full">
                    <div class="right_topbar">
                        <div class="icon_info">
                            <ul class="user_profile_dd">
                                <li>
                                    <a class="dropdown-toggle" href="javascript:void(0)" id="drop2"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="<?php echo $profileImage; ?>" alt="Profile Picture" width="35" height="35"
                                            class="rounded-circle">
                                        <span
                                            class="d-none d-lg-inline-flex fs-3 m-2 text-dark"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="profile_member.php"><i class="fa-regular fa-id-badge"></i>
                                            <span>ข้อมูลส่วนตัว</span></a>
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

        <div class="container mt-5 content">
            <h3>บัตรสมาชิก</h3>
            <div class="card mt-6">
                <img src="<?php echo htmlspecialchars($cardImagePath); ?>" alt="Member Card" class="card-img-top" style="width: 300px; height: auto;">
            </div>
        </div>



        <!-- <script>
            document.getElementById("sidebarToggle").addEventListener("click", function() {
                const sidebar = document.getElementById("sidebar");
                const content = document.getElementById("content");
                const toggleButton = document.querySelector('.toggle-button');

                // Toggle class เพื่อเลื่อน Sidebar
                sidebar.classList.toggle("hidden");
                content.classList.toggle("full");
            });
        </script> -->


        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <!-- Popper.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
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
    </div>

</body>

</html>